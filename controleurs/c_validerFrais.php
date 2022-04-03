<?php
//validation des frais pour les comptables
require_once("modele/class.database.inc.php");
require_once("modele/class.unePersonne.inc.php");
require_once("modele/class.personne.inc.php");
require_once("modele/class.fichedefrais.inc.php");
require_once("modele/class.uneFichedefrais.inc.php");
require_once("modele/class.fraisforfait.inc.php");
require_once("modele/class.lignefraisforfait.inc.php");
require_once("modele/class.lignefraishorsforfait.inc.php");
require_once("include/fct.inc.php");

//on récupère les données de la personne connectée
$utilisateur=$_SESSION['visiteur'];
$login=$utilisateur->get_login();
$mdp=$utilisateur->get_mdp();
$visiteur = unePersonne::getInfosVisiteur($login,$mdp);
include("vues/v_sommaire.php");

$action = $_REQUEST['action'];
switch($action) {
    case 'selectionnerMoisetVisiteur': {
        $role="v";
        $lesVisiteurs=unePersonne::getVisiteurByRole($role);
        $tabLesVisiteurs=new arrayObject();
        foreach($lesVisiteurs as $unVisiteur) {
            $tabLesVisiteurs->append(new Personne($unVisiteur["id"],$unVisiteur["nom"],$unVisiteur["prenom"],$unVisiteur['login'], $unVisiteur['mdp'], $unVisiteur["role"]));
        }
        var_dump($tabLesVisiteurs);
        $lesCles=array_keys($lesVisiteurs);
        $clesVi=array_keys((array)$tabLesVisiteurs);
        $visiteurASelectionner=$clesVi[0];

        $date=substr(date("Ymd"),0,6);
        $lesMois=uneFichedefrais::getLesMoisDisponiblesComptable($date);
        $lesClesMois = array_keys($lesMois);
        $moisASelectionner = $lesClesMois[0];
		include("vues/v_listeAValider.php");
		break;
	}
    case 'voirFicheAValider': {
        $leMois = $_REQUEST['lstMois']; 
        $date=substr(date("Ymd"),0,6);
        $lesMois=uneFichedefrais::getLesMoisDisponiblesComptable($date);
		$moisASelectionner = $leMois;
        var_dump($leMois);
        $leVisiteur = $_REQUEST['lstVisiteur'];
        $role="v";
        $lesVisiteurs=unePersonne::getVisiteurByRole($role);
        $visiteurASelectionner=$leVisiteur;
        $idV=unePersonne::get_IdVisiteurByNom($leVisiteur);
        $idVisiteur=implode("",$idV);
        var_dump($idVisiteur);
        include("vues/v_listeAValider.php");

        $lesFraisHorsForfait = uneFichedefrais::getLesFraisHorsForfait($idVisiteur,$leMois);
		$lesFraisForfait= uneFichedefrais::getLesFraisForfait($idVisiteur,$leMois);
        var_dump($lesFraisForfait);
		$lesInfosFicheFrais = uneFichedefrais::getLesInfosFraisValide($idVisiteur,$leMois);
        //j'essaye de faire un tableau d'objet ligneFraisForfait mais j'ai que des valeurs null
        $tabLesFraisForfait = new arrayObject();
        foreach($lesFraisForfait as $unFraisForfait) {
            $tabLesFraisForfait->append(new ligneFraisForfait($idVisiteur,$leMois,$unFraisForfait["idfrais"],$unFraisForfait["quantite"]));
        }
        var_dump($tabLesFraisForfait);
        //création de l'objet fiche de frais
        $tabLesInfosFichesFrais=new arrayObject();
        $tabLesInfosFichesFrais->append(new Fichedefrais($lesInfosFicheFrais["idVisiteur"],$lesInfosFicheFrais["mois"],$lesInfosFicheFrais["idEtat"],$lesInfosFicheFrais["dateModif"],$lesInfosFicheFrais["nbJustificatifs"],$lesInfosFicheFrais["montantValide"],$lesInfosFicheFrais["libEtat"]));
        var_dump($tabLesInfosFichesFrais);
        if ($lesInfosFicheFrais == null) {
            echo "Il n'y a pas de fiche clôturé pour le visiteur et le mois sélectionné.";
        }
        else {
		$numAnnee =substr( $leMois,0,4);
		$numMois =substr( $leMois,4,2);
        //je récupère les quantités et l'id
        $lesQuantites = uneFichedefrais::getLesQuantites($idVisiteur,$leMois);
        var_dump($lesQuantites);
        //je récupère les montants et l'id
        $lesMontants = uneFichedefrais::getLesIdMontantFrais();
        var_dump($lesMontants);
        //et ici je parcours mes deux tableaux et je calcul en fonction de l'id
        //mais faut que mes deux tableaux soient en objet pour faire mes get dessus
        $montantTotalValide=0;
        /*for($i=0;count($lesQuantites);$i++) {
            for($j=0;count($lesMontants);$j++) {
                if($lesQuantites)
            }
        }*/
        //faudrait que je puisse multiplier une quantité par le montant de fichefrais mais je dois parcourir les deux
        //Quantité (lignefraisfortait) * montant (fraisfortait) lié à l'id fraisforfait
        $libEtat = $lesInfosFicheFrais['libEtat'];
		$montantValide = $lesInfosFicheFrais['montantValide'];
		$nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
		$dateModif =  $lesInfosFicheFrais['dateModif'];
		$dateModif =  dateAnglaisVersFrancais($dateModif);
        include("vues/v_editionFrais.php");
        }
        break;
    }
    case 'modifierFichefrais': {
        //on récupère les données pour modifier la fiche de frais
        $idVisiteur=$_REQUEST['idVisiteur'];
        $mois=$_REQUEST['leMois'];
        $lesFrais = $_REQUEST['lesFrais'];
		if(lesQteFraisValides($lesFrais)) {
			uneFichedefrais::majFraisForfait($idVisiteur,$mois,$lesFrais);
            echo 'La fiche de frais a bien été modifiée';
		}
		else {
			ajouterErreur("Les valeurs des frais doivent être numériques");
			include("vues/v_erreurs.php");
		}

        //on récupère les données pour afficher le menu déroulant
        $leMois = $_REQUEST['leMois']; 
        $date=substr(date("Ymd"),0,6);
        $lesMois=uneFichedefrais::getLesMoisDisponiblesComptable($date);
		$moisASelectionner = $leMois;
        $idVisiteur = $_REQUEST['idVisiteur'];
        $leVisiteur=unePersonne::getVisiteurById($idVisiteur);
        $role="v";
        $lesVisiteurs=unePersonne::getVisiteurByRole($role);
        $visiteurASelectionner=$leVisiteur;
        include("vues/v_listeAValider.php");
        
        //on récupère les données pour afficher la fiche de frais
        $lesFraisHorsForfait = uneFichedefrais::getLesFraisHorsForfait($idVisiteur,$leMois);
		$lesFraisForfait= uneFichedefrais::getLesFraisForfait($idVisiteur,$leMois);
		$lesInfosFicheFrais = uneFichedefrais::getLesInfosFicheFrais($idVisiteur,$leMois);
		$numAnnee =substr( $leMois,0,4);
		$numMois =substr( $leMois,4,2);
		$libEtat = $lesInfosFicheFrais['libEtat'];
		$montantValide = $lesInfosFicheFrais['montantValide'];
		$nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
		$dateModif =  $lesInfosFicheFrais['dateModif'];
		$dateModif =  dateAnglaisVersFrancais($dateModif);
        include("vues/v_editionFrais.php");
        break;
    }
    case 'validerFichefrais': {
        //on modifie l'état de la fiche pour la passer à l'état validée
        $idVisiteur=$_REQUEST['idVisiteur'];
        $leMois=$_REQUEST['leMois'];
        $etat='VA';
        uneFichedefrais::majEtatFicheFrais($idVisiteur,$leMois,$etat);
        echo 'La fiche a été validée !';

        //on récupère les données pour afficher le menu déroulant
        $date=substr(date("Ymd"),0,6);
        $lesMois=uneFichedefrais::getLesMoisDisponiblesComptable($date);
		$moisASelectionner = $leMois;
        $idVisiteur = $_REQUEST['idVisiteur'];
        $leVisiteur=unePersonne::getVisiteurById($idVisiteur);
        $role="v";
        $lesVisiteurs=unePersonne::getVisiteurByRole($role);
        $visiteurASelectionner=$leVisiteur;
        include("vues/v_listeAValider.php");
        break;
    }
    case 'supprimerFraishorsforfait': {
        //on récupère l'id du frais hors forfait et on ajoute la mention REFUSE au libellé
        $idselect=$_REQUEST['idFrais'];
        $leLibelle=uneFichedefrais::getLibelleFraisHorsForfait($idselect);
        $nouveauLibelle='REFUSE : '.$leLibelle;
        uneFichedefrais::majLibelleFraisHorsForfait($nouveauLibelle,$idselect);

        //on récupère le frais hors forfait avec le libellé modifié
        $IdVetMois=uneFichedefrais::getLeFraisHorsForfaitById($idselect);
        $idVisiteur=$IdVetMois['idVisiteur'];
        $leMois=$IdVetMois['mois'];

        //on récupère les données pour afficher le menu déroulant
        $date=substr(date("Ymd"),0,6);
        $lesMois=uneFichedefrais::getLesMoisDisponiblesComptable($date);
		$moisASelectionner = $leMois;
        $leVisiteur=unePersonne::getVisiteurById($idVisiteur);
        $role="v";
        $lesVisiteurs=unePersonne::getVisiteurByRole($role);
        $visiteurASelectionner=$leVisiteur;
        include("vues/v_listeAValider.php");

        //on récupère les données pour afficher la fiche de frais
        $lesFraisHorsForfait = uneFichedefrais::getLesFraisHorsForfait($idVisiteur,$leMois);
		$lesFraisForfait= uneFichedefrais::getLesFraisForfait($idVisiteur,$leMois);
		$lesInfosFicheFrais = uneFichedefrais::getLesInfosFicheFrais($idVisiteur,$leMois);
		$numAnnee =substr( $leMois,0,4);
		$numMois =substr( $leMois,4,2);
		$libEtat = $lesInfosFicheFrais['libEtat'];
		$montantValide = $lesInfosFicheFrais['montantValide'];
		$nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
		$dateModif =  $lesInfosFicheFrais['dateModif'];
		$dateModif =  dateAnglaisVersFrancais($dateModif);
        include("vues/v_editionFrais.php");
        //on supprime le frais hors forfait en base de données
        uneFichedefrais::supprimerFraisHorsForfait($idselect);
        break;
    }
    case 'reporterFraishorsforfait': {
        //on récupère les données du frais hors forfait sélectionné
        $idSelect=$_REQUEST['idFrais'];
        $leFrais=uneFichedefrais::getLeFraisHorsForfaitById($idSelect);
        $mois=$leFrais['mois'];
        $numAnnee=substr($mois,0,4);
        $numMois=substr($mois,4,2);
        $nouveauMois=reporterLeMois($numMois);
        //si le mois est 12 on passe à l'année suivante
        if ($nouveauMois=='12') {
            $numAnnee=intval($numAnnee)+1;
            $nouveauNumAnnee=strval($numAnnee);
            $moisUpdate=$nouveauNumAnnee.$nouveauMois;
        } 
        else {
            $moisUpdate=$numAnnee.$nouveauMois;
        }
        //on utilise la méthode pour reporter la fiche de frais au mois suivant
        uneFichedefrais::reporterFraisHorsForfait($idSelect,$moisUpdate);
        echo ("La fiche a bien été reporté ! ");
        
        //On récupère les données pour afficher le menu déroulant
        $date=substr(date("Ymd"),0,6);
        $lesMois=uneFichedefrais::getLesMoisDisponiblesComptable($date);
		$moisASelectionner = $leMois;
        $leVisiteur=unePersonne::getVisiteurById($idVisiteur);
        $role="v";
        $lesVisiteurs=unePersonne::getVisiteurByRole($role);
        $visiteurASelectionner=$leVisiteur;
        include("vues/v_listeAValider.php");

        //on récupère les données pour afficher la fiche de frais
        $lesFraisHorsForfait = uneFichedefrais::getLesFraisHorsForfait($idVisiteur,$leMois);
		$lesFraisForfait= uneFichedefrais::getLesFraisForfait($idVisiteur,$leMois);
		$lesInfosFicheFrais = uneFichedefrais::getLesInfosFicheFrais($idVisiteur,$leMois);
		$numAnnee =substr( $leMois,0,4);
		$numMois =substr( $leMois,4,2);
		$libEtat = $lesInfosFicheFrais['libEtat'];
		$montantValide = $lesInfosFicheFrais['montantValide'];
		$nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
		$dateModif =  $lesInfosFicheFrais['dateModif'];
		$dateModif =  dateAnglaisVersFrancais($dateModif);
        include("vues/v_editionFrais.php");
        break;
    }
}

?>