<?php
class Database
{
	private static $bdd='mysql:dbname=gsb_fraislisa'; 
    //private static $serveur='host=192.168.0.15';
	private static $serveur='mysql:host=localhost';
	private static $port='3333';  		
    private static $user='gsb';    		
    private static $mdp='verT22+sLam10';
	//private static $user='lisa';    		
    //private static $mdp='********';		
	private static $monPdo;
    private static $monPdoGsb=null;

    private function __construct()
    {
    	Database::$monPdo = new PDO('mysql:host=localhost;mysql:dbname=gsb_fraislisa;3333, gsb, verT22+sLam10'); 
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