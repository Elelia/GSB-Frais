<?php
//collection des frais hors forfait
class ligneFraisHorsForfait {
    private $id;
    private $idVisiteur;
    private $mois;
    private $date;
    private $libelle;
    private $montant;

    public function __construct($unId,$unIdVisiteur,$unMois,$uneDate,$unLibelle,$unMontant) {
        $this->id=$unId;
        $this->idVisiteur=$unIdVisiteur;
        $this->mois=$unMois;
        $this->date=$uneDate;
        $this->libelle=$unLibelle;
        $this->montant=$unMontant;
    }

    public function get_idFraisHorsForfait() {
        return $this->id;
    }

    public function get_idVisiteurFraisHorsForfait() {
        return $this->idVisiteur;
    }

    public function get_moisFraisHorsForfait() {
        return $this->mois;
    }

    public function get_dateFraisHorsForfait() {
        return $this->date;
    }

    public function get_libelleFraisHorsForfait() {
        return $this->libelle;
    }

    public function get_montantFraisHorsForfait() {
        return $this->montant;
    }

    public function set_idFraisHorsForfait($unId) {
        $this->id=$unId;
    }

    public function set_idVisiteurFraisHorsForfait($unIdVisiteur) {
        $this->idVisiteur=$unIdVisiteur;
    }

    public function set_moisFraisHorsForfait($unMois) {
        $this->mois=$unMois;
    }

    public function set_dateFraisHorsForfait($uneDate) {
        $this->date=$uneDate;
    }

    public function set_libelleFraisHorsForfait($unLibelle) {
        $this->libelle=$unLibelle;
    }

    public function set_montantFraisHorsForfait($unMontant) {
        $this->montant=$unMontant;
    }

    
}

?>