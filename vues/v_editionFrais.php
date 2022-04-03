<!-- A modifier -->
<h3>Fiche de frais du mois <?php echo $numMois."-".$numAnnee?> : 
    </h3>
    <div class="encadre">
    <p>
        Etat : <?php echo $libEtat?> depuis le <?php echo $dateModif?> <br> Montant validé : <?php echo $montantValide?>€
              
                     
    </p>
    <!-- <form id="validerFrais" method="POST" action="index.php?uc=validerFrais&action=validerFichefrais"> -->
    <form id="majFrais" method="POST" action="index.php?uc=validerFrais&action=modifierFichefrais">
  	<table class="listeLegere">
  	   <caption>Eléments forfaitisés </caption>
        <tr>
         <?php
         foreach($lesFraisForfait as $unFraisForfait) 
		 {
      $idFrais = $unFraisForfait['idfrais'];
			$libelle = $unFraisForfait['libelle'];
		?>	
			<th> <?php echo $libelle?></th>
		 <?php
        }
		?>
		</tr>
        <tr>
        <?php
          foreach($lesFraisForfait as $unFraisForfait)
		  {
        $idFrais = $unFraisForfait['idfrais'];
				$quantite = $unFraisForfait['quantite'];
		?>
      <input id="id" name="idVisiteur" type="hidden" value="<?php echo $idVisiteur ?>">
      <input id="mois" name="leMois" type="hidden" value="<?php echo $leMois ?>">
      <td class="qteForfait"><input type="text" id="idFrais" name="lesFrais[<?php echo $idFrais?>]" size="10" maxlength="5" value="<?php echo $quantite?>"></td>
		 <?php
          }
		?>
		</tr>
    </table>
    <div class="piedForm">
      <p>
        <input id="maj" type="submit" value="Valider" form="majFrais" size="20" />
        <input id="annuler" type="reset" value="Effacer" form="majFrais" size="20" />
      </p> 
      </div>
    </form>

  	<table class="listeLegere">
  	   <caption>Descriptif des éléments hors forfait -<input type="text" id="nbJustificatifs" name="nbJustificatifs" size="10" maxlength="5" value="<?php echo $nbJustificatifs ?>"> justificatifs reçus -
       </caption>
             <tr>
                <th class="date">Date</th>
                <th class="libelle">Libellé</th>
                <th class='montant'>Montant</th>
                <th class='action' colspan="2">Action</th>                
             </tr>
        <?php      
          foreach ( $lesFraisHorsForfait as $unFraisHorsForfait ) 
		  {
			$date = $unFraisHorsForfait['date'];
			$libelle = $unFraisHorsForfait['libelle'];
			$montant = $unFraisHorsForfait['montant'];
      $id = $unFraisHorsForfait['id'];
      if(strpos($libelle,'REFUSE') !== false) { 
        ?>
             <tr style="background-color:#980101">
                <td><?php echo $date ?></td>
                <td><?php echo $libelle ?></td>
                <td><?php echo $montant."€" ?></td>
                <td><a href="index.php?uc=validerFrais&action=supprimerFraishorsforfait&idFrais=<?php echo $id ?>" 
				onclick="return confirm('Voulez-vous vraiment supprimer ce frais?');">Supprimer</a></td>
                <td><a href="index.php?uc=validerFrais&action=reporterFraishorsforfait&idFrais=<?php echo $id ?>" 
				onclick="return confirm('Voulez-vous vraiment reporter ce frais?');">Reporter</a></td>
             </tr>
             <?php 
             } 
             else { ?>
              <tr>
              <td><?php echo $date ?></td>
                <td><?php echo $libelle ?></td>
                <td><?php echo $montant."€" ?></td>
                <td><a href="index.php?uc=validerFrais&action=supprimerFraishorsforfait&idFrais=<?php echo $id ?>" 
				onclick="return confirm('Voulez-vous vraiment supprimer ce frais?');">Supprimer</a></td>
                <td><a href="index.php?uc=validerFrais&action=reporterFraishorsforfait&idFrais=<?php echo $id ?>" 
				onclick="return confirm('Voulez-vous vraiment reporter ce frais?');">Reporter</a></td>
             </tr>
             <?php 
             }
          } 
          ?>
    </table>
  </div>
  <form id="validerFrais" method="POST" action="index.php?uc=validerFrais&action=validerFichefrais">
    <input id="id" name="idVisiteur" type="hidden" value="<?php echo $idVisiteur ?>">
    <input id="mois" name="leMois" type="hidden" value="<?php echo $leMois ?>">
  <div class="piedForm">
      <p>
        <input id="ok" type="submit" value="Valider la fiche" form="validerFrais" size="20" />
      </p> 
      </div>
        </form>
  </div>