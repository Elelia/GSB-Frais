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

    public function get_moisLigneFraisForfait() {
        return $this->mois;
    }

    public function get_idFraisForfaitLigne() {
        return $this->idFraisForfait;
    }

    public function get_quantiteLigneFraisForfait() {
        return $this->quantite;
    }

    public function set_idVisiteurLigneFraisForfait($unIdVisiteur) {
        $this->idVisiteur=$unIdVisiteur;
    }

    public function set_moisLigneFraisForfait($unMois) {
        $this->mois=$unMois;
    }

    public function set_idFraisForfaitLigne($unIdFraisForfait) {
        $this->idFraisForfait=$unIdFraisForfait;
    }
}


?>