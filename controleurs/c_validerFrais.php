<?php
//validation des frais pour les comptables
require_once("modele/class.database.inc.php");
require_once("modele/class.unePersonne.inc.php");
require_once("modele/class.personne.inc.php");
require_once("modele/class.fichedefrais.inc.php");
require_once("modele/class.uneFichedefrais.inc.php");
require_once("modele/class.fraisforfait.inc.php");
require_once("modele/class.lignefraisforfait.inc.php");
require_once("modele/class.lignefraishorsforfait.inc.php");
require_once("include/fct.inc.php");

//on récupère les données de la personne connectée
$utilisateur=$_SESSION['visiteur'];
$login=$utilisateur->get_login();
$mdp=$utilisateur->get_mdp();
//on créer l'objet visiteur avec les données récupérées
$visiteur = unePersonne::getInfosVisiteur($login,$mdp);
//on appelle la vue du sommaire
include("vues/v_sommaire.php");

//on récupère l'action reçue par le controleur et on le gère avec un switch case
$action = $_REQUEST['action'];
switch($action) {
    //cas où l'utilisateur arrive sur la page et qu'il doit sélectionner le visiteur et le mois
    case 'selectionnerMoisetVisiteur': {
        //on stock le rôle v pour retrouver tous les utilisateurs ayant pour role visiteur
        $role="v";
        $lesVisiteurs=unePersonne::getVisiteurByRole($role);
        //on créer un tableau d'objet de tous ces visiteurs
        $tabLesVisiteurs=new arrayObject();
        foreach($lesVisiteurs as $unVisiteur) {
            $tabLesVisiteurs->append(new Personne($unVisiteur["id"],$unVisiteur["nom"],$unVisiteur["prenom"],$unVisiteur['login'], $unVisiteur['mdp'], $unVisiteur["role"]));
        }
        //on récupère la date du jour au format année mois jour
        $date=substr(date("Ymd"),0,6);
        //on récupère les mois qui ne dépassent pas le mois actuel, donc le mois en cours
        $lesMois=uneFichedefrais::getLesMoisDisponiblesComptable($date);
        //on stock le tableau d'objet des visiteurs et les mois dans la super variable SESSION
        $_SESSION['lesMois'] = $lesMois;
        $_SESSION['lesVisiteurs'] = $tabLesVisiteurs;
        //on appelle la vue des deux menus déroulant
		include("vues/v_listeAValider.php");
		break;
	}
    case 'voirFicheAValider': {
        //on récupère le mois, le visiteur sélectionné, le tableau des visiteurs et les mois stockés précédemment
        $leMois = $_REQUEST['lstMois']; 
        $nomVisiteur = $_REQUEST['lstVisiteur'];
        $tabLesVisiteurs = $_SESSION['lesVisiteurs'];
        $lesMois = $_SESSION['lesMois'];

        //on fait en sorte que le mois sélectionné soit récupéré pour la vue en le décomposant et en récupérant son libellé
		$moisASelectionner = obtenirLibelleMois(substr($leMois,4,2));
        //on récupère les informations du visiteur sélectionné
        $infosV=unePersonne::get_visiteurByNom($nomVisiteur);
        //on créer l'objet
        $leVisiteur = new Personne($infosV['id'],$infosV['nom'],$infosV['prenom'],$infosV['login'],$infosV['mdp'],$infosV['role']);
        $idVisiteur = $leVisiteur->get_id();
        $visiteurASelectionner = $leVisiteur->get_nom();
        //on appelle la vue
        include("vues/v_listeAValider.php");

        //on récupère les informations de la fiche pour le vistieur et le mois sélectionné
        $lesInfosFicheFrais = uneFichedefrais::getLesInfosFraisValide($idVisiteur,$leMois);
        //si il n'y a pas de fiche on retourne un message d'erreur
        if ($lesInfosFicheFrais == null) {
            ajouterErreur("Il n'y a pas de fiche clôturée pour le visiteur et le mois sélectionnés.");
			include("vues/v_erreurs.php");
        }
        else {
            //on créer le tableau object ligne frais hors forfait
            $lesFraisHorsForfait = uneFichedefrais::getLesFraisHorsForfait($idVisiteur,$leMois);
            $tabLesFraisHorsForfait = new arrayObject();
            foreach($lesFraisHorsForfait as $unFraisHorsForfait) {
                $tabLesFraisHorsForfait->append(new ligneFraisHorsForfait($unFraisHorsForfait["id"],$unFraisHorsForfait["idVisiteur"],$unFraisHorsForfait["mois"],$unFraisHorsForfait["date"],$unFraisHorsForfait["libelle"],$unFraisHorsForfait["montant"]));
            }
            $lesFraisForfait= uneFichedefrais::getLesFraisForfait($idVisiteur,$leMois);
            //on créer le tableau d'objet de ligne forfait
            $tabLesFraisForfait = new arrayObject();
            foreach($lesFraisForfait as $unFraisForfait) {
                $tabLesFraisForfait->append(new ligneFraisForfait($idVisiteur,$leMois,$unFraisForfait["idfrais"],$unFraisForfait["quantite"]));
            }
            //on créer le tableau object frais forfait
            $FraisForfait = uneFichedefrais::getFraisForfait();
            $tabLeFraisForfait = new arrayObject();
            foreach($FraisForfait as $leFrais) {
                $tabLeFraisForfait->append(new FraisForfait($leFrais["id"],$leFrais["libelle"],$leFrais["montant"]));
            }

            //on initialise la variable montantValide à 0 pour pouvoir l'utiliser par la suite
            $montantValide = 0;
            //on parcours notre tableau d'objet de la classe ligneFraisForfait
            //cela nous permet de parcourir les quantités qui ont été saisies par le visiteur dans sa fiche pour chaque id frais
            foreach($tabLesFraisForfait as $laQuantite) {
                //on parcours notre tableau d'objet de la classe FraisForfait
                //cela nous permet de parcourir les montants présents en base de données pour chaque id frais
                foreach($tabLeFraisForfait as $leMontant) {
                    //on teste si l'id frais de la quantité et du montant sont égaux
                    if($leMontant->get_idFraisForfait() == $laQuantite->get_idFraisForfaitLigne()) {
                        //si oui on multiplie le montant par la quantité et on l'ajoute au montantValide
                        $newQuantite = $leMontant->get_montantFraisForfait() * $laQuantite->get_quantiteLigneFraisForfait();
                        $montantValide += $montantValide + $newQuantite;
                    }
                }
            }

            //on créer la l'objet Fichedefrais avec toutes les informations obtenues précédemment
            $laFicheDeFrais=new Fichedefrais($lesInfosFicheFrais["idVisiteur"],$lesInfosFicheFrais["mois"],$lesInfosFicheFrais["nbJustificatifs"],$montantValide,$lesInfosFicheFrais["dateModif"],$lesInfosFicheFrais["idEtat"]);
            
            //on récupère d'autres informations pour la vue
            $numAnnee = substr($leMois,0,4);
            $numMois = substr($leMois,4,2);
            $libEtat = $lesInfosFicheFrais['libEtat'];
            $nbJustificatifs = $laFicheDeFrais->get_nbJustificatifsFiche();
            $dateModif =  $laFicheDeFrais->get_dateModifFiche();
            $dateModif =  dateAnglaisVersFrancais($dateModif);
            
            //stockage des objets dans la varible SESSION
            $_SESSION['lesFrais'] = $tabLesFraisForfait;
            $_SESSION['fraisHorsForfait'] = $tabLesFraisHorsForfait;
            $_SESSION['leFrais'] = $tabLeFraisForfait;
            $_SESSION['laFiche'] = $laFicheDeFrais;
            $_SESSION['lesVisiteurs'] = $tabLesVisiteurs;
            $_SESSION['leVisiteur'] = $leVisiteur;
            //on appelle la vue
            include("vues/v_editionFrais.php");
        }
        break;
    }
    case 'modifierFichefrais': {
        //on récupère les données crées et récupérées précédemment
        $lesFrais = $_REQUEST['lesFrais'];
        $tabLesFraisForfait = $_SESSION['lesFrais'];
        $tabLesFraisHorsForfait = $_SESSION['fraisHorsForfait'];
        $tabLeFraisForfait = $_SESSION['leFrais'];
        $laFicheDeFrais = $_SESSION['laFiche'];
        $tabLesVisiteurs = $_SESSION['lesVisiteurs'];
        $leVisiteur = $_SESSION['leVisiteur'];
        $lesMois = $_SESSION['lesMois'];
        $idVisiteur = $leVisiteur->get_id();
        $leMois = $laFicheDeFrais->get_moisFiche();

        //on modifie l'objet pour prendre en compte les modifications de l'utilisateur
        for($i=0;count($tabLesFraisForfait)>$i;$i++) {
            $intitule = $tabLesFraisForfait[$i]->get_idFraisForfaitLigne();
            $tabLesFraisForfait[$i]->set_quantiteLigneFraisForfait($lesFrais[$intitule]);
        }
        //on test si les frais saisis sont des entiers ou non, grâce aux fonctions présentes dans le fichier fct
		if(lesQteFraisValides($lesFrais)) {
            //si c'est le cas on appelle la méthode qui modifier les valeurs en base de données
			uneFichedefrais::majFraisForfait($idVisiteur,$leMois,$lesFrais);
            echo 'La fiche de frais a bien été modifiée';
		}
		else {
            //sinon on affiche une erreur
			ajouterErreur("Les valeurs des frais doivent être numériques");
			include("vues/v_erreurs.php");
		}

        //on récupère les données pour afficher le menu déroulant
        $date=substr(date("Ymd"),0,6);
		$moisASelectionner = obtenirLibelleMois(substr($leMois,4,2));
        $visiteurASelectionner = $leVisiteur->get_nom();
        include("vues/v_listeAValider.php");
        
        //on recalcul le montant validé avec les nouvelles quantités saisies et le même procédé que précédemment
        $montantValide = 0;
        foreach($tabLesFraisForfait as $laQuantite) {
            foreach($tabLeFraisForfait as $leMontant) {
                if($leMontant->get_idFraisForfait() == $laQuantite->get_idFraisForfaitLigne()) {
                    $newQuantite = $leMontant->get_montantFraisForfait() * $laQuantite->get_quantiteLigneFraisForfait();
                    $montantValide += $montantValide + $newQuantite;
                }
            }
        }

        //récupération des informations pour afficher le reste de la vue
        $lesInfosFicheFrais = uneFichedefrais::getLesInfosFraisValide($idVisiteur,$leMois);
		$numAnnee = substr($leMois,0,4);
		$numMois = substr($leMois,4,2);
		$libEtat = $lesInfosFicheFrais['libEtat'];
        $nbJustificatifs = $laFicheDeFrais->get_nbJustificatifsFiche();
        $dateModif =  $laFicheDeFrais->get_dateModifFiche();
		$dateModif = dateAnglaisVersFrancais($dateModif);
        include("vues/v_editionFrais.php");
        break;
    }
    case 'validerFichefrais': {
        //on récupère les données crées et récupérées précédemment
        $leVisiteur = $_SESSION['leVisiteur'];
        $tabLesVisiteurs = $_SESSION['lesVisiteurs'];
        $laFicheDeFrais = $_SESSION['laFiche'];
        $idVisiteur = $leVisiteur->get_id();
        $leMois = $laFicheDeFrais->get_moisFiche();
        $montantValide = $laFicheDeFrais->get_montantValideFiche();
        //on initiale une variable à VA, qui correspond au statut validé en base de données, afin de modifier l'état de la fiche
        $etat='VA';
        //on modifie l'objet Fichedefrais
        $laFicheDeFrais->set_idEtatFiche($etat);
        //on appelle la méthode pour modifier les données en base de données
        //uneFichedefrais::majEtatFicheFrais($idVisiteur,$leMois,$etat,$montantValide);
        echo 'La fiche a été validée !';

        //on récupère les données pour afficher le menu déroulant
        $date=substr(date("Ymd"),0,6);
        $lesMois=uneFichedefrais::getLesMoisDisponiblesComptable($date);
        include("vues/v_listeAValider.php");
        break;
    }
    case 'supprimerFraishorsforfait': {
        //on récupère les données crées et récupérées précédemment
        $tabLesFraisForfait = $_SESSION['lesFrais'];
        $tabLesFraisHorsForfait = $_SESSION['fraisHorsForfait'];
        $tabLeFraisForfait = $_SESSION['leFrais'];
        $laFicheDeFrais = $_SESSION['laFiche'];
        $tabLesVisiteurs = $_SESSION['lesVisiteurs'];
        $leVisiteur = $_SESSION['leVisiteur'];
        $idVisiteur = $leVisiteur->get_id();
        $leMois = $laFicheDeFrais->get_moisFiche();
        $idSelect=$_REQUEST['idFrais'];

        //on initialise la variable libelle à une chaîne de caractère vide
        $leLibelle = '';
        //on parcourt le tableau d'objet des lignes frais hors forfait
        foreach($tabLesFraisHorsForfait as $unFraisHorsForfait) {
            //on testesi l'id du frais hors forfait sélectionné par l'utilisateur correspond à l'id de la ligne frais hors forfait
            //du tableau
            if($idSelect == $unFraisHorsForfait->get_idFraisHorsForfait()) {
                //si oui on modifier le libellé de la ligne frais hors forfait sélectionné pour afficher refusé dedans
                $leLibelle = $unFraisHorsForfait->get_libelleFraisHorsForfait();
                $nouveauLibelle='REFUSE : '.$leLibelle;
                $unFraisHorsForfait->set_libelleFraisHorsForfait($nouveauLibelle);
            }
        }

        //on récupère les données pour afficher le menu déroulant
        $date=substr(date("Ymd"),0,6);
        $lesMois=uneFichedefrais::getLesMoisDisponiblesComptable($date);
		$moisASelectionner = $leMois;
        $visiteurASelectionner = $leVisiteur->get_nom();
        include("vues/v_listeAValider.php");

        //on récupère les données pour afficher la fiche de frais
        $lesInfosFicheFrais = uneFichedefrais::getLesInfosFraisValide($idVisiteur,$leMois);
		$numAnnee =substr( $leMois,0,4);
		$numMois =substr( $leMois,4,2);
		$libEtat = $lesInfosFicheFrais['libEtat'];
        $montantValide = $laFicheDeFrais->get_montantValideFiche();
        $nbJustificatifs = $laFicheDeFrais->get_nbJustificatifsFiche();
        $dateModif =  $laFicheDeFrais->get_dateModifFiche();
		$dateModif =  dateAnglaisVersFrancais($dateModif);
        include("vues/v_editionFrais.php");
        //on supprime le frais hors forfait en base de données
        //uneFichedefrais::supprimerFraisHorsForfait($idSelect);
        break;
    }
    case 'reporterFraishorsforfait': {
        //on récupère les données du frais hors forfait sélectionné et donc les données du visiteur
        $tabLesFraisForfait = $_SESSION['lesFrais'];
        $tabLesFraisHorsForfait = $_SESSION['fraisHorsForfait'];
        $tabLeFraisForfait = $_SESSION['leFrais'];
        $laFicheDeFrais = $_SESSION['laFiche'];
        $tabLesVisiteurs = $_SESSION['lesVisiteurs'];
        $leVisiteur = $_SESSION['leVisiteur'];
        $idVisiteur = $leVisiteur->get_id();
        $leMois = $laFicheDeFrais->get_moisFiche();
        $idSelect=$_REQUEST['idFrais'];
        //là on récupère le frais hors forfait qui a été sélectionné
        $leFrais=uneFichedefrais::getLeFraisHorsForfaitById($idSelect);
        $mois=$leFrais['mois'];
        $numAnnee=substr($mois,0,4);
        $numMois=substr($mois,4,2);
        $nouveauMois=reporterLeMois($numMois);
        //si le mois est 12 on passe à l'année suivante à revoir
        if ($nouveauMois=='12') {
            $numAnnee=intval($numAnnee)+1;
            $nouveauNumAnnee=strval($numAnnee);
            $moisUpdate=$nouveauNumAnnee.$nouveauMois;
        } 
        else {
            $moisUpdate=$numAnnee.$nouveauMois;
        }

        //on utilise la méthode pour reporter la fiche de frais au mois suivant
        //uneFichedefrais::reporterFraisHorsForfait($idSelect,$moisUpdate);
        echo ("Le frais a bien été reporté ! ");
        
        //on récupère les données pour afficher le menu déroulant
        $date=substr(date("Ymd"),0,6);
        $lesMois=uneFichedefrais::getLesMoisDisponiblesComptable($date);
        $moisASelectionner = $leMois;
        $visiteurASelectionner = $leVisiteur->get_nom();
        include("vues/v_listeAValider.php");

        //modifier le tableau des frais hors forfait pour ne pas récupérer le frais qui a été reporté
        $lesFraisHorsForfait = uneFichedefrais::getLesFraisHorsForfait($idVisiteur,$leMois);
        $tabLesFraisHorsForfait = new arrayObject();
        foreach($lesFraisHorsForfait as $unFraisHorsForfait) {
            $tabLesFraisHorsForfait->append(new ligneFraisHorsForfait($unFraisHorsForfait["id"],$unFraisHorsForfait["idVisiteur"],$unFraisHorsForfait["mois"],$unFraisHorsForfait["date"],$unFraisHorsForfait["libelle"],$unFraisHorsForfait["montant"]));
        }
        $lesFraisForfait= uneFichedefrais::getLesFraisForfait($idVisiteur,$leMois);
        
        //récupération des informations pour afficher le reste de la fiche
        $lesInfosFicheFrais = uneFichedefrais::getLesInfosFicheFrais($idVisiteur,$leMois);
		$numAnnee =substr( $leMois,0,4);
		$numMois =substr( $leMois,4,2);
		$libEtat = $lesInfosFicheFrais['libEtat'];
        $montantValide = $laFicheDeFrais->get_montantValideFiche();
		$nbJustificatifs = $laFicheDeFrais->get_nbJustificatifsFiche();
		$dateModif =  $laFicheDeFrais->get_nbJustificatifsFiche();
		$dateModif =  dateAnglaisVersFrancais($dateModif);
        include("vues/v_editionFrais.php");
        break;
    }
}

?>