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
$visiteur = unePersonne::getInfosVisiteur($login,$mdp);
include("vues/v_sommaire.php");

$action = $_REQUEST['action'];
switch($action) {
    case 'selectionnerMoisetVisiteur': {
        $role="v";
        $lesVisiteurs=unePersonne::getVisiteurByRole($role);
        $tabLesVisiteurs=new arrayObject();
        foreach($lesVisiteurs as $unVisiteur) {
            $tabLesVisiteurs->append(new Personne($unVisiteur["id"],$unVisiteur["nom"],$unVisiteur["prenom"],$unVisiteur['login'], $unVisiteur['mdp'], $unVisiteur["role"]));
        }
        $lesCles=array_keys($lesVisiteurs);
        $clesVi=array_keys((array)$tabLesVisiteurs);
        $visiteurASelectionner=$clesVi[0];

        $date=substr(date("Ymd"),0,6);
        var_dump($date);
        $lesMois=uneFichedefrais::getLesMoisDisponiblesComptable($date);
        $lesClesMois = array_keys($lesMois);
        $moisASelectionner = $lesClesMois[0];
        $_SESSION['lesVisiteurs'] = $tabLesVisiteurs;
		include("vues/v_listeAValider.php");
		break;
	}
    case 'voirFicheAValider': {
        //on récupère le mois et le visiteur sélectionné
        $leMois = $_REQUEST['lstMois']; 
        $nomVisiteur = $_REQUEST['lstVisiteur'];
        $tabLesVisiteurs = $_SESSION['lesVisiteurs'];

        $date=substr(date("Ymd"),0,6);
        $lesMois=uneFichedefrais::getLesMoisDisponiblesComptable($date);
		$moisASelectionner = $leMois;
        $role="v";
        $infosV=unePersonne::get_visiteurByNom($nomVisiteur);
        $leVisiteur = new Personne($infosV['id'],$infosV['nom'],$infosV['prenom'],$infosV['login'],$infosV['mdp'],$infosV['role']);
        $idVisiteur = $leVisiteur->get_id();
        $visiteurASelectionner = $leVisiteur->get_nom();
        //faire pareil pour le mois
        include("vues/v_listeAValider.php");

        $lesInfosFicheFrais = uneFichedefrais::getLesInfosFraisValide($idVisiteur,$leMois);
        if ($lesInfosFicheFrais == null) {
            echo "Il n'y a pas de fiche clôturée pour le visiteur et le mois sélectionnés.";
        }
        else {
            //tableau object ligne frais hors forfait
            $lesFraisHorsForfait = uneFichedefrais::getLesFraisHorsForfait($idVisiteur,$leMois);
            $tabLesFraisHorsForfait = new arrayObject();
            foreach($lesFraisHorsForfait as $unFraisHorsForfait) {
                $tabLesFraisHorsForfait->append(new ligneFraisHorsForfait($unFraisHorsForfait["id"],$unFraisHorsForfait["idVisiteur"],$unFraisHorsForfait["mois"],$unFraisHorsForfait["date"],$unFraisHorsForfait["libelle"],$unFraisHorsForfait["montant"]));
            }
            $lesFraisForfait= uneFichedefrais::getLesFraisForfait($idVisiteur,$leMois);
            //tableau d'objet de ligne forfait
            $tabLesFraisForfait = new arrayObject();
            foreach($lesFraisForfait as $unFraisForfait) {
                $tabLesFraisForfait->append(new ligneFraisForfait($idVisiteur,$leMois,$unFraisForfait["idfrais"],$unFraisForfait["quantite"]));
            }
            //tableau object frais forfait
            $FraisForfait = uneFichedefrais::getFraisForfait();
            $tabLeFraisForfait = new arrayObject();
            foreach($FraisForfait as $leFrais) {
                $tabLeFraisForfait->append(new FraisForfait($leFrais["id"],$leFrais["libelle"],$leFrais["montant"]));
            }
            //je récupère les quantités et l'id
            $tabLesQuantites = new arrayObject();
            $montantValide = 0;
            foreach($tabLesFraisForfait as $laQuantite) {
                foreach($tabLeFraisForfait as $leMontant) {
                    if($leMontant->get_idFraisForfait() == $laQuantite->get_idFraisForfaitLigne()) {
                        $newQuantite = $leMontant->get_montantFraisForfait() * $laQuantite->get_quantiteLigneFraisForfait();
                        $montantValide += $montantValide + $newQuantite;
                        $tabLesQuantites->append($newQuantite);
                    }
                }
            }
            //création de l'objet fiche de frais
            $laFicheDeFrais=new Fichedefrais($lesInfosFicheFrais["idVisiteur"],$lesInfosFicheFrais["mois"],$lesInfosFicheFrais["nbJustificatifs"],$montantValide,$lesInfosFicheFrais["dateModif"],$lesInfosFicheFrais["idEtat"]);
            
            $numAnnee = substr($leMois,0,4);
            $numMois = substr($leMois,4,2);
            $libEtat = $lesInfosFicheFrais['libEtat'];
            $nbJustificatifs = $laFicheDeFrais->get_nbJustificatifsFiche();
            $dateModif =  $laFicheDeFrais->get_dateModifFiche();
            $dateModif =  dateAnglaisVersFrancais($dateModif);
            
            //sotckage des objets dans la varible SESSION
            $_SESSION['lesFrais'] = $tabLesFraisForfait;
            $_SESSION['fraisHorsForfait'] = $tabLesFraisHorsForfait;
            $_SESSION['leFrais'] = $tabLeFraisForfait;
            $_SESSION['laFiche'] = $laFicheDeFrais;
            $_SESSION['lesVisiteurs'] = $tabLesVisiteurs;
            $_SESSION['leVisiteur'] = $leVisiteur;
            include("vues/v_editionFrais.php");
        }
        break;
    }
    case 'modifierFichefrais': {
        //on récupère les données pour modifier la fiche de frais
        $lesFrais = $_REQUEST['lesFrais'];
        $tabLesFraisForfait = $_SESSION['lesFrais'];
        $tabLesFraisHorsForfait = $_SESSION['fraisHorsForfait'];
        $tabLeFraisForfait = $_SESSION['leFrais'];
        $laFicheDeFrais = $_SESSION['laFiche'];
        $tabLesVisiteurs = $_SESSION['lesVisiteurs'];
        $leVisiteur = $_SESSION['leVisiteur'];
        $idVisiteur = $leVisiteur->get_id();
        $leMois = $laFicheDeFrais->get_moisFiche();

        //je modifie mon objet pour prendre en compte les modifications
        for($i=0;count($tabLesFraisForfait)>$i;$i++) {
            $intitule = $tabLesFraisForfait[$i]->get_idFraisForfaitLigne();
            $tabLesFraisForfait[$i]->set_quantiteLigneFraisForfait($lesFrais[$intitule]);
        }
		if(lesQteFraisValides($lesFrais)) {
			uneFichedefrais::majFraisForfait($idVisiteur,$leMois,$lesFrais);
            echo 'La fiche de frais a bien été modifiée';
		}
		else {
			ajouterErreur("Les valeurs des frais doivent être numériques");
			include("vues/v_erreurs.php");
		}

        //on récupère les données pour afficher le menu déroulant
        $date=substr(date("Ymd"),0,6);
        $lesMois=uneFichedefrais::getLesMoisDisponiblesComptable($date);
		$moisASelectionner = $leMois;
        $visiteurASelectionner = $leVisiteur->get_nom();
        include("vues/v_listeAValider.php");
        
        //calcul du montant total
        $tabLesQuantites = new arrayObject();
        $montantValide = 0;
        foreach($tabLesFraisForfait as $laQuantite) {
            foreach($tabLeFraisForfait as $leMontant) {
                if($leMontant->get_idFraisForfait() == $laQuantite->get_idFraisForfaitLigne()) {
                    $newQuantite = $leMontant->get_montantFraisForfait() * $laQuantite->get_quantiteLigneFraisForfait();
                    $montantValide += $montantValide + $newQuantite;
                    $tabLesQuantites->append($newQuantite);
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
        //on modifie l'état de la fiche pour la passer à l'état validée
        $leVisiteur = $_SESSION['leVisiteur'];
        $tabLesVisiteurs = $_SESSION['lesVisiteurs'];
        $laFicheDeFrais = $_SESSION['laFiche'];
        $idVisiteur = $leVisiteur->get_id();
        $leMois = $leMois = $laFicheDeFrais->get_moisFiche();
        $etat='VA';
        $laFicheDeFrais->set_idEtatFiche($etat);
        //uneFichedefrais::majEtatFicheFrais($idVisiteur,$leMois,$etat);
        echo 'La fiche a été validée !';

        //on récupère les données pour afficher le menu déroulant
        $date=substr(date("Ymd"),0,6);
        $lesMois=uneFichedefrais::getLesMoisDisponiblesComptable($date);
        include("vues/v_listeAValider.php");
        break;
    }
    case 'supprimerFraishorsforfait': {
        //on récupère l'id du frais hors forfait et on ajoute la mention REFUSE au libellé
        $tabLesFraisForfait = $_SESSION['lesFrais'];
        $tabLesFraisHorsForfait = $_SESSION['fraisHorsForfait'];
        $tabLeFraisForfait = $_SESSION['leFrais'];
        $laFicheDeFrais = $_SESSION['laFiche'];
        $tabLesVisiteurs = $_SESSION['lesVisiteurs'];
        $leVisiteur = $_SESSION['leVisiteur'];
        $idVisiteur = $leVisiteur->get_id();
        $leMois = $laFicheDeFrais->get_moisFiche();
        $idSelect=$_REQUEST['idFrais'];

        //pour l'affichage du refusé
        $leLibelle = '';
        foreach($tabLesFraisHorsForfait as $unFraisHorsForfait) {
            if($idSelect == $unFraisHorsForfait->get_idFraisHorsForfait()) {
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
        uneFichedefrais::supprimerFraisHorsForfait($idSelect);
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