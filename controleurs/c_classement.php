<?php
//affichage des classements
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
include("vues/v_propositionClassement.php");

$action = $_REQUEST['action'];
switch($action) {
    case 'classementFraisForfait': {
        $leClassement=uneFichedefrais::getLeClassementFraisForfait();
        include("vues/v_classementFraisForfait.php");
        break;
    }
    case 'classementFraisHorsForfait': {
        $leClassement=uneFichedefrais::getLeClassementFraisHorsForfait();
        include("vues/v_classementFraisHorsForfait.php");
        break;
    }
}

?>