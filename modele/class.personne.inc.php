<?php
class Personne
{
    private $id;
    private $nom;
    private $prenom;
    private $login;
    private $mdp;
    private $role;
    
    public function __construct($unId,$unNom,$unPrenom,$unLogin,$unMdp,$unRole)
    {
        $this->id=$unId;
        $this->nom=$unNom;
        $this->prenom=$unPrenom;
        $this->login=$unLogin;
        $this->mdp=$unMdp;
        $this->role=$unRole;
    }
    
    public function get_id()
    {
        return $this->id;
    }
    
    public function get_nom()
    {
        return $this->nom;
    }
    
    public function get_prenom()
    {
        return $this->prenom;
    }
    
    public function get_login()
    {
        return $this->login;
    }
    
    public function get_mdp()
    {
        return $this->mdp;
    }
    
    public function get_role()
    {
        return $this->role;
    }

    public function set_id($unId) {
        $this->id=$unId;
    }

    public function set_nom($unNom) {
        $this->nom=$unNom;
    }

    public function set_prenom($unPrenom) {
        $this->prenom=$unPrenom;
    }

    public function set_login($unLogin) {
        $this->login=$unLogin;
    }

    public function set_mdp($unMdp) {
        $this->mdp=$unMpd;
    }

    public function set_role($unRole) {
        $this->role=$unRole;
    }
}


?>
