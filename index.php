<?php
require_once("modele/class.database.inc.php");
require_once("modele/class.personne.inc.php");
require_once("modele/class.fichedefrais.inc.php");
require_once("modele/class.uneFichedefrais.inc.php");
require_once("modele/class.fraisforfait.inc.php");
require_once("modele/class.lignefraisforfait.inc.php");
require_once("modele/class.lignefraishorsforfait.inc.php");
//j'appelle ces include ici sinon mon $_SESSION ne fonctionne pas
require("controleurs/c_principal.php");
if (!isset($_SESSION)) {
    session_start();
}
// session_start();
include("vues/v_entete.php");

$pdo = Database::getDatabase();//on stock la connexion à la base de donnée dans cette variable

if (isset($_GET["uc"])) {
    $uc = $_GET["uc"];
}
else {  
    $uc = "defaut";
}

$fichier = controleurPrincipal($uc);
include("controleurs/$fichier");

?>

