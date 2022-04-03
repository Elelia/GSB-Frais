<?php
require_once("modele/class.database.inc.php");
require_once("modele/class.personne.inc.php");
require("controleurs/c_principal.php");
// session_start();
if (!isset($_SESSION)) {
 	session_start();
 }
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

