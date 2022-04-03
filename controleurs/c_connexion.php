<?php
require_once ("modele/class.database.inc.php");
require_once("modele/class.unePersonne.inc.php");
require_once("modele/class.personne.inc.php");

if(!isset($_REQUEST['action']))
{
	$_REQUEST['action'] = 'demandeConnexion';
}
$action = $_REQUEST['action'];
switch($action)
{
	case 'demandeConnexion':
	{
		include("vues/v_connexion.php");
		break;
	}
	case 'valideConnexion':
	{
		$login = $_REQUEST['login'];
		$mdp = $_REQUEST['mdp'];
		$visiteur = unePersonne::getInfosVisiteur($login,$mdp);
		if(!is_array($visiteur))
		{
			unePersonne::ajouterErreur("Login ou mot de passe incorrect");
			include("vues/v_erreurs.php");
			include("vues/v_connexion.php");
		}
		else
		{
			unePersonne::login($login,$mdp);
			$utilisateur=$_SESSION['visiteur'];
			include("vues/v_sommaire.php");
		}
		break;
	}
	default :
	{
		include("vues/v_connexion.php");
		break;
	}

}

?>