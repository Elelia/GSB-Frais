<?php
class uneFichedefrais {
    //Teste si un visiteur possède une fiche de frais pour le mois passé en argument
    public static function estPremierFraisMois($idVisiteur,$mois) {
		$ok = false;
		$req = "select count(*) as nblignesfrais from fichefrais 
		where fichefrais.mois = '$mois' and fichefrais.idvisiteur = '$idVisiteur'";
		$res = Database::get_monPdo()->query($req);
		$laLigne = $res->fetch();
		if($laLigne['nblignesfrais'] == 0){
			$ok = true;
		}
		return $ok;
	}

    //Crée une nouvelle fiche de frais et les lignes de frais au forfait pour un visiteur et un mois donnés
    public static function creeNouvellesLignesFrais($idVisiteur,$mois) {
		$dernierMois = self::dernierMoisSaisi($idVisiteur);
		$laDerniereFiche = self::getLesInfosFicheFrais($idVisiteur,$dernierMois);
		if($laDerniereFiche['idEtat']=='CR'){
			self::majEtatFicheFrais($idVisiteur, $dernierMois,'CL');
		}
		$req = "insert into fichefrais(idvisiteur,mois,nbJustificatifs,montantValide,dateModif,idEtat) 
		values('$idVisiteur','$mois',0,0,now(),'CR')";
		Database::get_monPdo()->exec($req);
		$lesIdFrais = self::getLesIdFrais();
		foreach($lesIdFrais as $uneLigneIdFrais){
			$unIdFrais = $uneLigneIdFrais['idfrais'];
			$req = "insert into lignefraisforfait(idvisiteur,mois,idFraisForfait,quantite) 
			values('$idVisiteur','$mois','$unIdFrais',0)";
			Database::get_monPdo()->exec($req);
		}
	}

	public static function getLesIdFrais() {
		$req = "select fraisforfait.id as idfrais from fraisforfait order by fraisforfait.id";
		$res = Database::get_monPdo()->query($req);
		$lesLignes = $res->fetchAll();
		return $lesLignes;
	}

	//Retourne les mois pour lesquels un visiteur a une fiche de frais
	public static function getLesMoisDisponibles($idVisiteur){
		$req = "select fichefrais.mois as mois from  fichefrais where fichefrais.idvisiteur ='$idVisiteur' 
		order by fichefrais.mois desc ";
		$res = Database::get_monPdo()->query($req);
		$lesMois =array();
		$laLigne = $res->fetch();
		while($laLigne != null)	{
			$mois = $laLigne['mois'];
			$numAnnee =substr( $mois,0,4);
			$numMois =substr( $mois,4,2);
			$lesMois["$mois"]=array(
		     "mois"=>"$mois",
		    "numAnnee"  => "$numAnnee",
			"numMois"  => "$numMois"
             );
			$laLigne = $res->fetch(); 		
		}
		return $lesMois;
	}

	//retourne les six derniers mois présents en base de donnée
	public static function getLesMoisDisponiblesComptable($date){
		$req = "select fichefrais.mois as mois from fichefrais where fichefrais.mois < '$date' 
		order by fichefrais.mois desc ";
		$res = Database::get_monPdo()->query($req);
		$lesMois =array();
		$laLigne = $res->fetch();
		$i=0;
		while($laLigne != null and $i<6 ) {
			$mois = $laLigne['mois'];
			$numAnnee =substr( $mois,0,4);
			$numMois =substr( $mois,4,2);
			$lesMois["$mois"]=array(
		     "mois"=>"$mois",
		    "numAnnee"  => "$numAnnee",
			"numMois"  => "$numMois"
             );
			$laLigne = $res->fetch();
			$i++; 
		}
		return $lesMois;
	}

	//retourne les mois pour lesquels les fiches sont clôturées
	public static function getLesMoisClotures() {
		$req = "select fichefrais.mois as mois from fichefrais where fichefrais.idEtat ='CL' ";
		//la requête est bonne c'est la suite qui pose problème
		$res = Database::get_monPdo()->query($req);
		$lesMoisValides =array();
		$laLigne = $res->fetch();
		while($laLigne != null)	{
			$mois = $laLigne['mois'];
			$numAnnee =substr( $mois,0,4);
			$numMois =substr( $mois,4,2);
			$lesMoisValides["$mois"]=array(
		     "mois"=>"$mois",
		    "numAnnee"  => "$numAnnee",
			"numMois"  => "$numMois"
             );
			$laLigne = $res->fetch(); 		
		}
		//le tableau est vide du coup PARCE QUE JE SUIS CON EN FAIT
		return $lesMoisValides;
	}

	public static function getMoisEtVisiteursClotures() {
		$req = "select fichefrais.mois as mois, visiteur.nom as nom, visiteur.prenom as prenom, visiteur.id as id from 
		fichefrais inner join visiteur where fichefrais.idEtat ='CL' and fichefrais.idVisiteur=visiteur.id";
		$res = Database::get_monPdo()->query($req);
		$laLigne = $res->fetchAll();
		return $laLigne;
	}

	//retourne le mois, le nom prenom et l'id du visiteur ayant une ou plusieurs fiche à l'état validé
	public static function getMoisEtVisiteursValides() {
		$req = "select fichefrais.mois as mois, visiteur.nom as nom, visiteur.prenom as prenom, visiteur.id as id from 
		fichefrais inner join visiteur where fichefrais.idEtat ='VA' and fichefrais.idVisiteur=visiteur.id";
		$res = Database::get_monPdo()->query($req);
		$laLigne = $res->fetchAll();
		return $laLigne;
	}

	//Retourne sous forme d'un tableau associatif toutes les lignes de frais au forfait concernées par les deux arguments
	public static function getLesFraisForfait($idVisiteur,$mois) {
		$req = "select fraisforfait.id as idfrais, fraisforfait.libelle as libelle, 
		lignefraisforfait.quantite as quantite from lignefraisforfait inner join fraisforfait 
		on fraisforfait.id = lignefraisforfait.idfraisforfait
		where lignefraisforfait.idvisiteur ='$idVisiteur' and lignefraisforfait.mois='$mois' 
		order by lignefraisforfait.idfraisforfait";	
		$res = Database::get_monPdo()->query($req);
		$lesLignes = $res->fetchAll();
		return $lesLignes; 
	}

	//Retourne sous forme d'un tableau associatif toutes les lignes de frais au forfait concernées par les deux arguments
	public static function getFraisForfait() {
		$req = "select * from fraisforfait";	
		$res = Database::get_monPdo()->query($req);
		$laLigne = $res->fetchAll();
		return $laLigne;
	}

	//Met à jour la table ligneFraisForfait pour un visiteur et un mois donné en enregistrant les nouveaux montants
	public static function majFraisForfait($idVisiteur, $mois, $lesFrais){
		$lesCles = array_keys($lesFrais);
		foreach($lesCles as $unIdFrais){
			$qte = $lesFrais[$unIdFrais];
			$req = "update lignefraisforfait set lignefraisforfait.quantite = $qte
			where lignefraisforfait.idvisiteur = '$idVisiteur' and lignefraisforfait.mois = '$mois'
			and lignefraisforfait.idfraisforfait = '$unIdFrais'";
			Database::get_monPdo()->exec($req);
		}
	}

	//Crée un nouveau frais hors forfait pour un visiteur un mois donné à partir des informations fournies en paramètre
	public static function creeNouveauFraisHorsForfait($idVisiteur,$mois,$libelle,$date,$montant) {
		$dateFr = dateFrancaisVersAnglais($date);
		$req = "insert into lignefraishorsforfait (idVisiteur, mois, libelle, date, montant)  values('$idVisiteur','$mois','$libelle','$dateFr','$montant')";
		Database::get_monPdo()->exec($req);
	}

	//Supprime le frais hors forfait dont l'id est passé en argument
	public static function supprimerFraisHorsForfait($idFrais){
		$req = "delete from lignefraishorsforfait where lignefraishorsforfait.id ='$idFrais'";
		Database::get_monPdo()->exec($req);
	}

	//Retourne sous forme d'un tableau associatif toutes les lignes de frais hors forfait concernées par les deux arguments
	//La boucle foreach ne peut être utilisée ici car on procède à une modification de la structure itérée - transformation du champ date-
	public static function getLesFraisHorsForfait($idVisiteur,$mois){
	    $req = "select * from lignefraishorsforfait where lignefraishorsforfait.idvisiteur ='$idVisiteur' 
		and lignefraishorsforfait.mois = '$mois' ";	
		$res = Database::get_monPdo()->query($req);
		$lesLignes = $res->fetchAll();
		$nbLignes = count($lesLignes);
		for ($i=0; $i<$nbLignes; $i++){
			$date = $lesLignes[$i]['date'];
			$lesLignes[$i]['date'] =  dateAnglaisVersFrancais($date);
		}
		return $lesLignes; 
	}

	//Retourne le nombre de justificatif d'un visiteur pour un mois donné
	public static function getNbjustificatifs($idVisiteur, $mois){
		$req = "select fichefrais.nbjustificatifs as nb from  fichefrais where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
		$res = Database::get_monPdo()->query($req);
		$laLigne = $res->fetch();
		return $laLigne['nb'];
	}

	//Retourne tous les id de la table FraisForfait
	public static function getLesIdMontantFrais(){
		$req = "select fraisforfait.id as idfrais, fraisforfait.montant as montantfrais from fraisforfait order by fraisforfait.id";
		$res = Database::get_monPdo()->query($req);
		$lesLignes = $res->fetchAll();
		return $lesLignes;
	}

	//Retourne les informations d'une fiche de frais d'un visiteur pour un mois donné
	public static function getLesInfosFicheFrais($idVisiteur,$mois){
		$req = "select fichefrais.idVisiteur as idVisiteur, fichefrais.mois as mois, fichefrais.idEtat as idEtat, fichefrais.dateModif as dateModif, fichefrais.nbJustificatifs as nbJustificatifs, 
			fichefrais.montantValide as montantValide, etat.libelle as libEtat from  fichefrais inner join etat on fichefrais.idEtat = etat.id 
			where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
		$res = Database::get_monPdo()->query($req);
		$laLigne = $res->fetch();
		return $laLigne;
	}

	//me faut une fonction qui retourne la fiche de frais pour l'idvisiteur et le mois sélectionné et avec l'état clôturé
	public static function getLesInfosFraisValide($idVisiteur,$mois) {
		try {
			$cnx = Database::get_monPdo();
			$req = $cnx->prepare("select fichefrais.idVisiteur as idVisiteur, fichefrais.mois as mois, fichefrais.idEtat as idEtat, fichefrais.dateModif as dateModif, fichefrais.nbJustificatifs as nbJustificatifs, 
			fichefrais.montantValide as montantValide, etat.libelle as libEtat from  fichefrais inner join etat on fichefrais.idEtat = etat.id 
			where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois' and fichefrais.idEtat = 'CL'");
			$req->execute();
			$resultat = $req->fetch(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
            print "Erreur !: " . $e->getMessage();
            die();
        }
        return $resultat;
	}

	//met à jour l'état d'une fiche de frais
	public static function majFicheFrais($idVisiteur,$mois,$etat,$montantValide) {
		$req = "update ficheFrais set idEtat = '$etat', dateModif = now(), montantValide = $montantValide 
		where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
		Database::get_monPdo()->exec($req);
	}

	public static function reporterFraisHorsForfait($idFrais,$mois) {
		$req = "update lignefraishorsforfait set mois = '$mois', dateModif = now() 
		where lignefichefraishorsforfait.id ='$idFrais'";
		Database::get_monPdo()->exec($req);
	}

	public static function dernierMoisSaisi($idVisiteur){
		$req = "select max(mois) as dernierMois from fichefrais where fichefrais.idvisiteur = '$idVisiteur'";
		$res = Database::get_monPdo()->query($req);
		$laLigne = $res->fetch();
		$dernierMois = $laLigne['dernierMois'];
		return $dernierMois;
	}

	public static function getLibelleFraisHorsForfait($id) {
		$req = "select libelle as libelle from lignefraishorsforfait where lignefraishorsforfait.id ='$id'";
		$res = Database::get_monPdo()->query($req);
		$laLigne = $res->fetch();
		return $laLigne['libelle'];
	}

	public static function majLibelleFraisHorsForfait($libelle,$id) {
		$req = "update lignefraishorsforfait set libelle = '$libelle' 
		where lignefraishorsforfait.id ='$id'";
		Database::get_monPdo()->exec($req);
	}

	public static function getLeFraisHorsForfaitById($id) {
		$req = "select lignefraishorsforfait.idVisiteur as idVisiteur, lignefraishorsforfait.mois as mois 
		from lignefraishorsforfait where lignefraishorsforfait.id = '$id'";
		$res = Database::get_monPdo()->query($req);
		$laLigne = $res->fetch();
		return $laLigne;
	}

	public static function getLesQuantites($idVisiteur,$mois) {
        $req = "select lignefraisforfait.quantite, lignefraisforfait.idFraisForfait from lignefraisforfait 
		where lignefraisforfait.idVisiteur = '$idVisiteur' and lignefraisforfait.mois = '$mois'";
		$res = Database::get_monPdo()->query($req);
		$lesLignes = $res->fetchAll();
		return $lesLignes;
    }

	public function majEtatFicheFrais($idVisiteur,$mois,$etat){
		$req = "update ficheFrais set idEtat = '$etat', dateModif = now() 
		where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
		$res = Database::get_monPdo()->exec($req);
	}
}

?>