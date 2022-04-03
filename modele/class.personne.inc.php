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
}


?>
