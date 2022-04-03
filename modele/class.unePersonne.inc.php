<?php

class unePersonne
{
    public static function login($unLogin,$unMdp) {
        if (empty($unLogin) || empty($unMdp)) {
            return;
        }
        if (!isset($_SESSION)) {
            session_start();
        }
    
        $visiteur = self::getVisiteurByLogin($unLogin);
        $mdpBD = $visiteur["mdp"];
    
        if (trim($mdpBD) == trim($unMdp)) {
            $utilisateur=new Personne($visiteur["id"],$visiteur["nom"],$visiteur["prenom"],$unLogin, $mdpBD, $visiteur["role"]);
            $_SESSION['visiteur']=$utilisateur;
        }
    }

    public static function estConnecte() {
        return isset($_SESSION['visiteur']);
    }

    public static function ajouterErreur($msg) {
        if (! isset($_REQUEST['erreurs'])) {
           $_REQUEST['erreurs']=array();
         } 
        $_REQUEST['erreurs'][]=$msg;
    }

    public static function logout() {
        if (!isset($_SESSION)) {
            session_start();
        }
        unset($_SESSION);
    }

    public static function getVisiteurByRole($role) {
        $resultat = array();

        try {
            $cnx = Database::get_monPdo();
            $req = $cnx->prepare("select * from visiteur where role=:role");
            $req->bindValue(':role', $role, PDO::PARAM_STR);
            $req->execute();
            //fetch() permet de récupérer le résultat de la requête et FETCH_ASSOC place les résultats trouvés dans un tableau indexé
            $ligne = $req->fetch(PDO::FETCH_ASSOC);
            while ($ligne) {
                $resultat[] = $ligne;
                $ligne = $req->fetch(PDO::FETCH_ASSOC);
            }
        } 
        catch (PDOException $e) {
            print "Erreur !: " . $e->getMessage();
            die();
        }
        return $resultat;
    }

    public static function getLesVisiteurs() {
		$req ="select * from visiteur";
		$res = Database::get_monPdo()->query($req);
		$lesLignes = $res->fetchAll();
		return $lesLignes; 
	}

    public static function getVisiteurByLogin($unLogin) {
        try {
            $cnx = Database::get_monPdo();
            $req = $cnx->prepare("select * from visiteur where login like :login");
            $req->bindValue(':login', $unLogin, PDO::PARAM_STR);

            $req->execute();

            $resultat = $req->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            print "Erreur !: " . $e->getMessage();
            die();
        }
        return $resultat;
    }

    public static function getInfosVisiteur($unLogin, $unMdp) {
		$req = "select visiteur.id as id, visiteur.nom as nom, visiteur.prenom as prenom, visiteur.role as role from visiteur 
		where visiteur.login='$unLogin' and visiteur.mdp='$unMdp'";
		$rs = Database::get_monPdo()->query($req);
		$ligne = $rs->fetch();
		return $ligne;
	}

    public static function get_IdVisiteurByNom($unNom) {
        try {
            $cnx = Database::get_monPdo();
            $req = $cnx->prepare("select id from visiteur where nom like :nom");
            $req->bindValue(':nom', $unNom, PDO::PARAM_STR);

            $req->execute();

            $resultat = $req->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            print "Erreur !: " . $e->getMessage();
            die();
        }
        return $resultat;
    }

    public static function getVisiteurById($unId) {
        $req = "select visiteur.id as id, visiteur.nom as nom, visiteur.prenom as prenom, visiteur.login as login,
        visiteur.mdp as mdp, visiteur.role as role from visiteur where id='$unId'";
        $rs = Database::get_monPdo()->query($req);
		$ligne = $rs->fetch();
		return $ligne;
    }
}

?>