<?php
//fiche de frais
class Fichedefrais
{
    private $idVisiteur;
    private $mois;
    private $nbJustificatifs;
    private $montantValide;
    private $dateModif;
    private $idEtat;
    private $fraisForfait;
    private $lesFraisF;
    private $lesFraisHorsF;

    public function __construct($unIdVisiteur,$unMois,$unNbJustificatifs,$unMontantValide,$uneDateModif,$unIdEtat) {
        $this->idVisiteur=$unIdVisiteur;
        $this->mois=$unMois;
        $this->nbJustificatifs=$unNbJustificatifs;
        $this->montantValide=$unMontantValide;
        $this->dateModif=$uneDateModif;
        $this->idEtat=$unIdEtat;
    }

    public function get_idVisiteurFiche() {
        return $this->idVisiteur;
    }

    public function get_moisFiche() {
        return $this->mois;
    }

    public function get_nbJustificatifsFiche() {
        return $this->nbJustificatifs;
    }

    public function get_montantValideFiche() {
        return $this->montantValide;
    }

    public function get_dateModifFiche() {
        return $this->dateModif;
    }

    public function get_idEtatFiche() {
        return $this->idEtat;
    }

    public function set_idVisiteurFiche($unIdVisiteur) {
        $this->idVisiteur=$unIdVisiteur;
    }

    public function set_moisFiche($unMois) {
        $this->mois=$unMois;
    }

    public function set_nbJustificatifsFiche($unNbJustificatifs) {
        $this->nbJustificatifs=$unNbJustificatifs;
    }

    public function set_montantValideFiche($unMontantValide) {
        $this->montantValide=$unMontantValide;
    }

    public function set_dateModifFiche($uneDateModif) {
        $this->dateModif=$uneDateModif;
    }

    public function set_idEtatFiche($unIdEtat) {
        $this->idEtat=$unIdEtat;
    }

}


?>