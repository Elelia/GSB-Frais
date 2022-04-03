<?php
//création frais pour les visiteurs médicaux
//en fonction de l'action ce serait soit un truc de comptable soit un truc de visiteur, à gérer au niveau des controleurs
require_once("modele/class.database.inc.php");
require_once("modele/class.unePersonne.inc.php");
require_once("modele/class.personne.inc.php");
require_once("modele/class.fichedefrais.inc.php");
require_once("modele/class.uneFichedefrais.inc.php");
require_once("modele/class.fraisforfait.inc.php");
require_once("modele/class.lignefraisforfait.inc.php");
require_once("modele/class.lignefraishorsforfait.inc.php");
require_once("include/fct.inc.php");

$utilisateur=$_SESSION['visiteur'];
$login=$utilisateur->get_login();
$mdp=$utilisateur->get_mdp();
$visiteur = unePersonne::getInfosVisiteur($login,$mdp);

include("vues/v_sommaire.php");
$idVisiteur = $utilisateur->get_id();
$mois = getMois(date("d/m/Y"));
$numAnnee =substr( $mois,0,4);
$numMois =substr( $mois,4,2);
$action = $_REQUEST['action'];

//a chaque fiche créer le ranger dans la bonne collection
switch($action){
	case 'saisirFrais':{
		if(uneFichedefrais::estPremierFraisMois($idVisiteur,$mois)) {
			uneFichedefrais::creeNouvellesLignesFrais($idVisiteur,$mois);
		}
		break;
	}
	case 'validerMajFraisForfait':{
		$lesFrais = $_REQUEST['lesFrais'];
		if(lesQteFraisValides($lesFrais)){
			uneFichedefrais::majFraisForfait($idVisiteur,$mois,$lesFrais);
		}
		else {
			ajouterErreur("Les valeurs des frais doivent être numériques");
			include("vues/v_erreurs.php");
		}
	  break;
	}
	case 'validerCreationFrais': { //ah en fait c'est pour les frais hors forfait
		$dateFrais = $_REQUEST['dateFrais'];
		$libelle = $_REQUEST['libelle'];
		$montant = $_REQUEST['montant'];
		valideInfosFrais($dateFrais,$libelle,$montant);
		if (nbErreurs() != 0 ){
			include("vues/v_erreurs.php");
		}
		else {
			uneFichedefrais::creeNouveauFraisHorsForfait($idVisiteur,$mois,$libelle,$dateFrais,$montant);
			var_dump($libelle);
			var_dump($dateFrais);
			var_dump($montant);
			var_dump($idVisiteur);
			var_dump($mois);
			//donc mes variables sont bien récupérée mais rien n'est crée en base de donnée
			//non mais y a tout je comprend pas pourquoi ça marche pas pour le coup zzzzz
		}
		break;
	}
	case 'supprimerFrais': {
		$idFrais = $_REQUEST['idFrais'];
	    uneFichedefrais::supprimerFraisHorsForfait($idFrais);
		break;
	}
}
$lesFraisHorsForfait = uneFichedefrais::getLesFraisHorsForfait($idVisiteur,$mois);
$lesFraisForfait= uneFichedefrais::getLesFraisForfait($idVisiteur,$mois); //ici c'est les tableaux en gros
//ce serait donc mes collections d'objet ?
include("vues/v_listeFraisForfait.php");
include("vues/v_listeFraisHorsForfait.php");
?>