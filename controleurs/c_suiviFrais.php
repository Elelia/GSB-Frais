<?php
//suivi des frais pour les comptables, leur permet de valider le paiement des fiches cloturées (donc pas de ce mois-ci)
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
	case 'selectionnerPaiement': {
		//on récupère les mois et les visiteurs qui ont une fiche de frais à l'état validé
		$lesMoisClotures=uneFichedefrais::getMoisEtVisiteursValides();
		if($lesMoisClotures==null) {
			echo("Aucune fiche de frais n'est disponible à la mise en paiement");
		}
		else {
			$lesCles = array_keys($lesMoisClotures);
			$moisASelectionner = $lesCles[0];
			include("vues/v_listePaiement.php");
		}
		break;
	}
	case 'voirFraisCloture': {
		//On récupère les données pour afficher le menu déroulant
		$visiteurSelect=$_REQUEST['lstPaiement'];
		$lidVisiteur=substr($visiteurSelect,6);
		$infosVisiteur = unePersonne::getVisiteurById($lidVisiteur);
		$leVisiteur = new Personne($infosVisiteur['id'],$infosVisiteur['nom'],$infosVisiteur['prenom'],$infosVisiteur['login'],$infosVisiteur['mdp'],$infosVisiteur['role']);
		$idVisiteur = $leVisiteur->get_id();
		$lesMoisClotures=uneFichedefrais::getMoisEtVisiteursValides();
		$moisASelectionner = $leVisiteur;
		$leMois=substr($visiteurSelect,0,6);
		include("vues/v_listePaiement.php");

		//on récupère les données pour afficher la fiche
		$lesFraisForfait=uneFichedefrais::getLesFraisForfait($idVisiteur,$leMois);
		$tabLesFraisForfait = new arrayObject();
        foreach($lesFraisForfait as $unFraisForfait) {
            $tabLesFraisForfait->append(new ligneFraisForfait($idVisiteur,$leMois,$unFraisForfait["idfrais"],$unFraisForfait["quantite"]));
        }
		$lesFraisHorsForfait = uneFichedefrais::getLesFraisHorsForfait($idVisiteur,$leMois);
		$tabLesFraisHorsForfait = new arrayObject();
        foreach($lesFraisHorsForfait as $unFraisHorsForfait) {
            $tabLesFraisHorsForfait->append(new ligneFraisHorsForfait($unFraisHorsForfait["id"],$unFraisHorsForfait["idVisiteur"],$unFraisHorsForfait["mois"],$unFraisHorsForfait["date"],$unFraisHorsForfait["libelle"],$unFraisHorsForfait["montant"]));
        }
		$FraisForfait = uneFichedefrais::getFraisForfait();
        $tabLeFraisForfait = new arrayObject();
        foreach($FraisForfait as $leFrais) {
            $tabLeFraisForfait->append(new FraisForfait($leFrais["id"],$leFrais["libelle"],$leFrais["montant"]));
        }
		$lesInfosFicheFrais = uneFichedefrais::getLesInfosFicheFrais($idVisiteur,$leMois);
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

		$laFicheDeFrais=new Fichedefrais($lesInfosFicheFrais["idVisiteur"],$lesInfosFicheFrais["mois"],$lesInfosFicheFrais["nbJustificatifs"],$montantValide,$lesInfosFicheFrais["dateModif"],$lesInfosFicheFrais["idEtat"]);

		$numAnnee =substr( $leMois,0,4);
		$numMois =substr( $leMois,4,2);
		$libEtat = $lesInfosFicheFrais['libEtat'];
        $montantValide = $laFicheDeFrais->get_montantValideFiche();
        $nbJustificatifs = $laFicheDeFrais->get_nbJustificatifsFiche();
        $dateModif =  $laFicheDeFrais->get_dateModifFiche();

		$_SESSION['lesFrais'] = $tabLesFraisForfait;
        $_SESSION['fraisHorsForfait'] = $tabLesFraisHorsForfait;
        $_SESSION['leFrais'] = $tabLeFraisForfait;
        $_SESSION['laFiche'] = $laFicheDeFrais;
        $_SESSION['leVisiteur'] = $leVisiteur;
		include("vues/v_fraisClotures.php");
		break;
	}
	case 'passerFicheEnRembourse': {
		//on modifie l'état de la fiche pour la passer en rembourser
		$leVisiteur=$_SESSION['leVisiteur'];
		$idVisiteur = $leVisiteur->get_id();
		$leMois=$_REQUEST['moisFiche'];
		$etat='RB';
		uneFichedefrais::majEtatFicheFrais($idVisiteur,$leMois,$etat);
		echo 'Le paiement a été effectué, bravo !';

		//On récupère les données pour afficher le menu déroulant
		$lesMoisClotures=uneFichedefrais::getMoisEtVisiteursValides();
		if($lesMoisClotures==null) {
			echo("Aucune fiche de frais n'est disponible à la mise en paiement");
		}
		else {
			$lesCles = array_keys($lesMoisClotures);
			$moisASelectionner = $lesCles[0];
			include("vues/v_listePaiement.php");
		}
		break;
	}
}

?>