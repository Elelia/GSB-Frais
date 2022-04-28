<?php
//l'objet fraisforfait
class FraisForfait {
    private $id;
    private $libelle;
    private $montant;

    public function __construct($unId,$unLibelle,$unMontant) {
        $this->id=$unId;
        $this->libelle=$unLibelle;
        $this->montant=$unMontant;
    }

    public function get_idFraisForfait() {
        return $this->id;
    }

    public function get_libelleFraisForfait() {
        return $this->libelle;
    }

    public function get_montantFraisForfait() {
        return $this->montant;
    }

    public function set_idFraisForfait($unId) {
        $this->id=$unId;
    }

    public function set_libelleFraisForfait($unLibelle) {
        $this->libelle=$unLibelle;
    }

    public function set_montantFraisForfait($unMontant) {
        $this->montant=$unMontant;
    }

}

?>