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

    public function get_moiFraisHorsForfait() {
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

    
}

?>