<?php
require_once ("modele/class.database.inc.php");
require_once("modele/class.unePersonne.inc.php");

unePersonne::logout();

include('vues/v_deconnexion.php');

if (!isset($_SESSION)) {
    include("vues/v_connexion.php");
}
?>