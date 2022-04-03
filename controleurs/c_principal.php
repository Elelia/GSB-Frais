<?php

function controleurPrincipal($uc) {
    $lesActions=array();
    $lesActions["defaut"]="c_connexion.php";
    $lesActions["connexion"]="c_connexion.php";
    $lesActions["deconnexion"]="c_deconnexion.php";
    //partie visiteur
    $lesActions["gererFrais"]="c_gererFrais.php";
    $lesActions["etatFrais"]="c_etatFrais.php";
    //partie comptable
    $lesActions["validerFrais"]="c_validerFrais.php";
    $lesActions["suiviFrais"]="c_suiviFrais.php";
    $lesActions["classement"]="c_classement.php";

    if (array_key_exists($uc, $lesActions)) {
        return $lesActions[$uc];
    } 
    else {
        return $lesActions["defaut"];
    }
}
?>