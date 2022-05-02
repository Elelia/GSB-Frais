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
        $lesMois=uneFichedefrais::getLesMoisDisponiblesComptable($date);
        $lesClesMois = array_keys($lesMois);
        $moisASelectionner = $lesClesMois[0];
		include("vues/v_listeAValider.php");
		break;
	}
    case 'voirFicheAValider': {
        //on récupère le mois et le visiteur sélectionné
        $leMois = $_REQUEST['lstMois']; 
        $nomVisiteur = $_REQUEST['lstVisiteur'];
        $date=substr(date("Ymd"),0,6);
        $lesMois=uneFichedefrais::getLesMoisDisponiblesComptable($date);
		$moisASelectionner = $leMois;
        $role="v";
        $lesVisiteurs=unePersonne::getVisiteurByRole($role);
        $tabLesVisiteurs=new arrayObject();
        foreach($lesVisiteurs as $unVisiteur) {
            $tabLesVisiteurs->append(new Personne($unVisiteur["id"],$unVisiteur["nom"],$unVisiteur["prenom"],$unVisiteur['login'], $unVisiteur['mdp'], $unVisiteur["role"]));
        }
        $infosV=unePersonne::get_IdVisiteurByNom($nomVisiteur);
        $leVisiteur = new Personne($infosV['id'],$infosV['nom'],$infosV['prenom'],$infosV['login'],$infosV['mdp'],$infosV['role']);
        //$idVisiteur=implode("",$idV);
        $idVisiteur = $leVisiteur->get_id();
        $visiteurASelectionner=$leVisiteur->get_nom();
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
        //voir comment le faire en objet

        // $idVisiteur=$_REQUEST['idVisiteur'];
        // $mois=$_REQUEST['leMois'];
        $lesFrais = $_REQUEST['lesFrais'];
        //je récupère mes objets de $_SESSION
        // $tabLesFraisForfait = $_SESSION['lesFrais'];
        // $tabLeFraisForfait = $_SESSION['leFrais'];
        // $tabLesFraisHorsForfait = $_SESSION['fraisHorsForfait'];

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
        //$leMois = $_REQUEST['leMois']; 
        $date=substr(date("Ymd"),0,6);
        $lesMois=uneFichedefrais::getLesMoisDisponiblesComptable($date);
		$moisASelectionner = $leMois;
        // $idVisiteur = $_REQUEST['idVisiteur'];
        // $leVisiteur=unePersonne::getVisiteurById($idVisiteur);
        // $role="v";
        // $lesVisiteurs=unePersonne::getVisiteurByRole($role);
        // $tabLesVisiteurs=new arrayObject();
        // foreach($lesVisiteurs as $unVisiteur) {
        //     $tabLesVisiteurs->append(new Personne($unVisiteur["id"],$unVisiteur["nom"],$unVisiteur["prenom"],$unVisiteur['login'], $unVisiteur['mdp'], $unVisiteur["role"]));
        // }
        $visiteurASelectionner=$leVisiteur;
        include("vues/v_listeAValider.php");
        
        //on récupère les données pour afficher la fiche de frais

        //tableau object ligne frais hors forfait
        //$lesFraisHorsForfait = uneFichedefrais::getLesFraisHorsForfait($idVisiteur,$leMois);
        //$tabLesFraisHorsForfait = new arrayObject();
        //foreach($lesFraisHorsForfait as $unFraisHorsForfait) {
            //$tabLesFraisHorsForfait->append(new ligneFraisHorsForfait($unFraisHorsForfait["id"],$unFraisHorsForfait["idVisiteur"],$unFraisHorsForfait["mois"],$unFraisHorsForfait["date"],$unFraisHorsForfait["libelle"],$unFraisHorsForfait["montant"]));
        //}
		//$lesFraisForfait= uneFichedefrais::getLesFraisForfait($idVisiteur,$leMois);
        
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
        $laFichedeFrais = $_SESSION['laFiche'];
        $idVisiteur = $leVisiteur->get_id();
        $leMois = $leMois = $laFicheDeFrais->get_moisFiche();
        $etat='VA';
        $laFichedeFrais->set_idEtatFiche($etat);
        uneFichedefrais::majEtatFicheFrais($idVisiteur,$leMois,$etat);
        echo 'La fiche a été validée !';

        //on récupère les données pour afficher le menu déroulant
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
        $lesMois=uneFichedefrais::getLesMoisDisponiblesComptable($date);
        $lesClesMois = array_keys($lesMois);
        $moisASelectionner = $lesClesMois[0];
        include("vues/v_listeAValider.php");
        break;
    }
    case 'supprimerFraishorsforfait': {
        //voir pour tout récupérer dans la variable session afin de ne pas refaire les objets à chaque fois
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
        var_dump($tabLesFraisHorsForfait);
        //pour l'affichage du refusé
        $leLibelle = '';
        foreach($tabLesFraisHorsForfait as $unFraisHorsForfait) {
            if($idSelect == $unFraisHorsForfait->get_idFraisHorsForfait()) {
                $leLibelle = $unFraisHorsForfait->get_libelleFraisHorsForfait();
            }
        }
        $nouveauLibelle='REFUSE : '.$leLibelle;
        //uneFichedefrais::majLibelleFraisHorsForfait($nouveauLibelle,$idselect);
        $tabLesFraisHorsForfait->set_libelleFraisHorsForfait($nouveaLibelle);
        

        //on récupère le frais hors forfait avec le libellé modifié
        //pas besoin je modifi juste l'objet ?

        $IdVetMois=uneFichedefrais::getLeFraisHorsForfaitById($idselect);
        $idVisiteur=$IdVetMois['idVisiteur'];
        $leMois=$IdVetMois['mois'];

        //on récupère les données pour afficher le menu déroulant
        $date=substr(date("Ymd"),0,6);
        $lesMois=uneFichedefrais::getLesMoisDisponiblesComptable($date);
		$moisASelectionner = $leMois;
        $leVisiteur=unePersonne::getVisiteurById($idVisiteur);
        $role="v";
        $lesVisiteurs=unePersonne::getVisiteurByRole($role);
        $visiteurASelectionner=$leVisiteur;
        include("vues/v_listeAValider.php");

        //on récupère les données pour afficher la fiche de frais
        $lesFraisHorsForfait = uneFichedefrais::getLesFraisHorsForfait($idVisiteur,$leMois);
		$lesFraisForfait= uneFichedefrais::getLesFraisForfait($idVisiteur,$leMois);
		$lesInfosFicheFrais = uneFichedefrais::getLesInfosFicheFrais($idVisiteur,$leMois);
		$numAnnee =substr( $leMois,0,4);
		$numMois =substr( $leMois,4,2);
		$libEtat = $lesInfosFicheFrais['libEtat'];
		$montantValide = $lesInfosFicheFrais['montantValide'];
		$nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
		$dateModif =  $lesInfosFicheFrais['dateModif'];
		$dateModif =  dateAnglaisVersFrancais($dateModif);
        include("vues/v_editionFrais.php");
        //on supprime le frais hors forfait en base de données
        uneFichedefrais::supprimerFraisHorsForfait($idselect);
        break;
    }
    case 'reporterFraishorsforfait': {
        //voir pour tout récupérer dans la variable session afin de ne pas refaire les objets à chaque fois
        //on récupère les données du frais hors forfait sélectionné et donc les données du visiteur
        var_dump($_REQUEST);
        var_dump($_SESSION);
        $laFichesDeFrais = $_SESSION['laFiche'];
        $idVisiteur = $_SESSION['laFiche']->get_idVisiteurFiche();
        $leMois = $_SESSION['laFiche']->get_moisFiche();
        $idSelect=$_REQUEST['idFrais'];
        //$idVisiteur=$_REQUEST['idVisiteur'];
        //$leMois=$_REQUEST['leMois'];
        $leFrais=uneFichedefrais::getLeFraisHorsForfaitById($idSelect);
        $mois=$leFrais['mois'];
        $numAnnee=substr($mois,0,4);
        $numMois=substr($mois,4,2);
        $nouveauMois=reporterLeMois($numMois);
        //si le mois est 12 on passe à l'année suivante
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
        
        //On récupère les données pour afficher le menu déroulant
        $date=substr(date("Ymd"),0,6);
        $lesMois=uneFichedefrais::getLesMoisDisponiblesComptable($date);
		$moisASelectionner = $leMois;
        $leVisiteur=unePersonne::getVisiteurById($idVisiteur);
        $role="v";
        $lesVisiteurs=unePersonne::getVisiteurByRole($role);
        $tabLesVisiteurs=new arrayObject();
        foreach($lesVisiteurs as $unVisiteur) {
            $tabLesVisiteurs->append(new Personne($unVisiteur["id"],$unVisiteur["nom"],$unVisiteur["prenom"],$unVisiteur['login'], $unVisiteur['mdp'], $unVisiteur["role"]));
        }
        $visiteurASelectionner=$leVisiteur;
        include("vues/v_listeAValider.php");

        //on récupère les données pour afficher la fiche de frais
        //a revoir
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
        $lesInfosFicheFrais = uneFichedefrais::getLesInfosFicheFrais($idVisiteur,$leMois);
		$numAnnee =substr( $leMois,0,4);
		$numMois =substr( $leMois,4,2);
		$libEtat = $lesInfosFicheFrais['libEtat'];
		$nbJustificatifs = $laFichesDeFrais->get_nbJustificatifsFiche();
		$dateModif =  $laFichesDeFrais->get_nbJustificatifsFiche();
		$dateModif =  dateAnglaisVersFrancais($dateModif);
        include("vues/v_editionFrais.php");
        break;
    }
}

?>