<h3>Fiche de frais du mois <?php echo $numMois."-".$numAnnee?> : 
    </h3>
    <form action="index.php?uc=suiviFrais&action=passerFicheEnRembourse" method="post">
    <div class="encadre">
    <p>
        Etat : <?php echo $libEtat?> depuis le <?php echo $dateModif?> <br> Montant validé : <?php echo $montantValide?>
              
                     
    </p>
  	<table class="listeLegere">
  	   <caption>Eléments forfaitisés </caption>
        <tr>
         <?php
         foreach ( $tabLeFraisForfait as $unFraisForfait ) 
		 {
			$libelle = $unFraisForfait->get_libelleFraisForfait();
		?>	
			<th> <?php echo $libelle?></th>
		 <?php
        }
		?>
		</tr>
        <tr>
        <?php
          foreach (  $tabLesFraisForfait as $unFraisForfait  ) 
		  {
				$quantite = $unFraisForfait->get_quantiteLigneFraisForfait();
		?>
                <td class="qteForfait"><?php echo $quantite?> </td>
		 <?php
          }
		?>
		</tr>
    </table>
  	<table class="listeLegere">
  	   <caption>Descriptif des éléments hors forfait -<?php echo $nbJustificatifs ?> justificatifs reçus -
       </caption>
             <tr>
                <th class="date">Date</th>
                <th class="libelle">Libellé</th>
                <th class='montant'>Montant</th>                
             </tr>
        <?php      
          foreach ( $lesFraisHorsForfait as $unFraisHorsForfait ) 
		  {
			$date = $unFraisHorsForfait['date'];
			$libelle = $unFraisHorsForfait['libelle'];
			$montant = $unFraisHorsForfait['montant'];
		?>
             <tr>
                <td><?php echo $date ?></td>
                <td><?php echo $libelle ?></td>
                <td><?php echo $montant ?></td>
             </tr>
        <?php 
          }
		?>
    </table>
  <div class="piedForm">
      <p>
        <input id="Id" name="idVisiteur" type="hidden" value="<?php echo $lesInfosFicheFrais['idVisiteur'] ?>">
        <input id="mois" name="moisFiche" type="hidden" value="<?php echo $lesInfosFicheFrais['mois'] ?>">
        <!-- me faut aussi récupérer l'id de l'utilisateur, ou tout récupérer ? a voir mais voilà déjà ça c'est fait MERCI TONY   -->
        <input id="valider" type="submit" value="Valider le remboursement" size="20" />
      </p> 
      </div>
  </div>
        </form>