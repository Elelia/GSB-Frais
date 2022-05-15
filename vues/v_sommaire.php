<div id="menuGauche">
      <ul id="menuList">
			<li >
            <?php if($utilisateur->get_role()=="v") {
               echo "Visiteur : ";?>
            <b><?php echo $utilisateur->get_prenom()."  ".$utilisateur->get_nom();?></b>
         </li>
         <li class="smenu">
            <a href="index.php?uc=gererFrais&action=saisirFrais" title="Saisie fiche de frais ">Saisie fiche de frais</a>
         </li>
         <li class="smenu">
            <a href="index.php?uc=etatFrais&action=selectionnerMois" title="Consultation de mes fiches de frais">Mes fiches de frais</a>
         </li>
 	      <li class="smenu">
            <a href="index.php?uc=deconnexion" title="Se déconnecter">Déconnexion</a>
         </li>
      </ul>
         <?php }
            else {
               echo "Comptable :";?>
            <b><?php echo $utilisateur->get_prenom()."  ".$utilisateur->get_nom()  ?></b>
         </li>
         <li class="smenu">
            <a href="index.php?uc=validerFrais&action=selectionnerMoisetVisiteur" title="Valider fiche de frais">Valider les fiches de frais</a>
         </li>
         <li class="smenu">
            <a href="index.php?uc=suiviFrais&action=selectionnerPaiement" title="Suivi fiches de frais">Suivi des fiches de frais</a>
         </li>
 	      <li class="smenu">
            <a href="index.php?uc=deconnexion" title="Se déconnecter">Déconnexion</a>
         </li>
      </ul>
            <?php } ?>
        
</div>

<div id="contenu">
   <h2>Bienvenue sur l'intranet Galaxy Swiss-Bourdin</h2>
    