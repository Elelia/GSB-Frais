<div id="contenu">
      <h2>Fiches des visiteurs et mois à sélectionner</h2>
      <form action="index.php?uc=suiviFrais&action=voirFraisCloture" method="post">
      <div class="corpsForm">
         
      <p>
	 
        <label for="lstPaiement" accesskey="n">Visiteur : </label>
        <select id="lstPaiement" name="lstPaiement">
            <?php
			foreach ($lesMoisClotures as $value) {
			    $mois = $value['mois'];
          $nom = $value['nom'];
          $prenom = $value['prenom'];
          $id=$value['id'];
          $numAnnee =substr($mois,0,4);
          $numMois =substr($mois,4,2);
				if($mois == $moisASelectionner ) {
				?>
				<option selected value="<?php echo $mois.$id ?>"><?php echo  $numMois."/".$numAnnee."-"." ".$nom." ".$prenom ?> </option>
				<?php 
				}
				else { ?>
				<option value="<?php echo $mois.$id ?>"><?php echo  $numMois."/".$numAnnee."-"." ".$nom." ".$prenom ?> </option>
				<?php 
				}
			}
           
		   ?>    
            
        </select>
      </p>
      </div>
      <div class="piedForm">
      <p>
        <input id="ok" type="submit" value="Valider" size="20" />
        <input id="annuler" type="reset" value="Effacer" size="20" />
      </p> 
      </div>
        
      </form>