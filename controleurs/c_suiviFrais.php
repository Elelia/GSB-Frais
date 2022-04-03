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
		$leVisiteur=$_REQUEST['lstPaiement'];
		$lesMoisClotures=uneFichedefrais::getMoisEtVisiteursValides();
		$moisASelectionner = $leVisiteur;
		$leMois=substr($leVisiteur,0,6);
		$lId=substr($leVisiteur,6);
		include("vues/v_listePaiement.php");

		//on récupère les données pour afficher la fiche
		$lesFraisForfait=uneFichedefrais::getLesFraisForfait($lId,$leMois);
		$lesFraisHorsForfait = uneFichedefrais::getLesFraisHorsForfait($lId,$leMois);
		$lesInfosFicheFrais = uneFichedefrais::getLesInfosFicheFrais($lId,$leMois);
		$numAnnee =substr( $leMois,0,4);
		$numMois =substr( $leMois,4,2);
		$libEtat = $lesInfosFicheFrais['libEtat'];
		$montantValide = $lesInfosFicheFrais['montantValide'];
		$nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
		$dateModif =  $lesInfosFicheFrais['dateModif'];
		$dateModif =  dateAnglaisVersFrancais($dateModif);
		include("vues/v_fraisClotures.php");
		break;
	}
	case 'passerFicheEnRembourse': {
		//on modofie l'état de la fiche pour la passer en rembourser
		$leVisiteur=$_REQUEST['idVisiteur'];
		$leMois=$_REQUEST['moisFiche'];
		$lEtat='RB';
		uneFichedefrais::majEtatFicheFrais($leVisiteur,$leMois,$lEtat);
		echo 'Le paiement a été effectué, bravo !';

		//On récupère les données pour afficher le menu déroulant
		$lesMoisClotures=uneFichedefrais::getMoisEtVisiteursValides();
		$lesCles = array_keys($lesMoisClotures);
		$moisASelectionner = $lesCles[0];
		include("vues/v_listePaiement.php");
		break;
	}
}


?>