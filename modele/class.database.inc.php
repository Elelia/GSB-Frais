<?php
class Database
{
	private static $serveur='mysql:host=localhost';
    //private static $serveur='mysql:host=192.168.0.15';
    private static $bdd='dbname=gsb_fraislisa';   		
    private static $user='gsb';    		
    private static $mdp='verT22+sLam10';	
	private static $monPdo;
    private static $monPdoGsb=null;

    private function __construct()
    {
    	Database::$monPdo = new PDO(Database::$serveur.';'.Database::$bdd, Database::$user, Database::$mdp); 
		Database::$monPdo->query("SET CHARACTER SET utf8");
	}

	public function __destruct()
    {
		Database::$monPdo = null;
	}

    public static function getDatabase(){
		if(Database::$monPdoGsb==null)
		{
			Database::$monPdoGsb= new Database();
		}
		return Database::$monPdoGsb;  
	}

	public static function get_monPdo()
	{
		return Database::$monPdo;
	}
}
?>