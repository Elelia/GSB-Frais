<?php 
//collection des lignes frais forfait
class ligneFraisForfait {

    private $idVisiteur;
    private $mois;
    private $idFraisForfait;
    private $quantite;

    public function __construct($unIdVisiteur,$unMois,$unIdFraisForfait,$uneQuantite) {
        $this->idVisiteur=$unIdVisiteur;
        $this->mois=$unMois;
        $this->idFraisForfait=$unIdFraisForfait;
        $this->quantite=$uneQuantite;
    }

    public function get_idVisiteurLigneFraisForfait() {
        return $this->idVisiteur;
    }

    public function get_moiLigneFraisForfait() {
        return $this->mois;
    }

    public function get_idFraisForfaitLigne() {
        return $this->idFraisForfait;
    }

    public function get_quantiteLigneFraisForfait() {
        return $this->quantite;
    }
}


?>