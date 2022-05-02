<div id="contenu">
<h2>Validation des fiches de frais</h2>
<form action="index.php?uc=validerFrais&action=voirFicheAValider" method="post">
      <div class="corpsForm">
         
      <p>
	 
        <label for="lstVisiteur" accesskey="n">Visiteur : </label>
        <select id="lstVisiteur" name="lstVisiteur">
            <?php
			foreach ($tabLesVisiteurs as $value)
			{
				$nom =  $value->get_nom();
				$prenom =  $value->get_prenom();
				if($nom == $visiteurASelectionner) {
				?>
				<option selected value="<?php echo $nom ?>"><?php echo  $nom." ".$prenom ?> </option>
				<?php 
				}
				else { ?>
				<option value="<?php echo $nom ?>"><?php echo  $nom." ".$prenom ?> </option>
				<?php 
				}
			
			}
           
		   ?>    
            
        </select>
      </p>
      <p>
	 
        <label for="lstMois" accesskey="n">Mois : </label>
        <select id="lstMois" name="lstMois">
            <?php
			foreach ($lesMois as $unMois)
			{
			  	$moisnb = $unMois['mois'];
				$numAnnee =  $unMois['numAnnee'];
				$numMois =  $unMois['numMois'];
				$mois = obtenirLibelleMois($numMois);
				if($mois == $moisASelectionner){
				?>
				<option selected value="<?php echo $moisnb ?>"><?php echo  $mois." ".$numAnnee ?> </option>
				<?php 
				}
				else{ ?>
				<option value="<?php echo $moisnb ?>"><?php echo  $mois." ".$numAnnee ?> </option>
				<?php 
				}
			
			}
           
		   ?>    
            
        </select>
      </p>
      <div class="piedForm">
      <p>
        <input id="ok" type="submit" value="Valider" size="20" />
      </p> 
      </div>
      </div>
        
      </form>