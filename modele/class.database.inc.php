<?php
class Database
{
	private static $bdd='mysql:dbname=gsb_fraislisa'; 
	private static $serveur='mysql:host=localhost';
	private static $port='3333';  		
    private static $user='gsb';    		
    private static $mdp='verT22+sLam10';		
	private static $monPdo;
    private static $monPdoGsb=null;

    private function __construct()
    {
    	Database::$monPdo = new PDO('mysql:host=62.23.119.27;port=3333;dbname=gsb_fraislisa', 'Lisa', 'Lisa');
		//Database::$monPdo = new PDO('mysql:host=192.168.0.15;port=3333;dbname=gsb_fraislisa', 'lisa', 'lisa');
		//Database::$monPdo = new PDO('pgsql:host=localhost;port=5432;dbname=gsb_fraislisa', 'superuser', 'pinG07@un');
		//Database::$monPdo = new PDO(Database::$bdd.';'.Database::$serveur.';'.Database::$port, Database::$user, Database::$mdp);
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