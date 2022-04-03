<table class="listeLegere">
<caption>Classement des frais forfait</caption>
             <tr>
                <th class="rang">Rang</th>
				<th class="nom">Nom</th>  
                <th class="prenom">Prenom</th>  
                <th class="montant">Montant</th>              
             </tr>
<?php
                $rang=0;
				foreach ($leClassement as $unClassement)
				{
					$rang++;
					$nom=$unClassement['nom'];
					$prenom=$unClassement['prenom'];
                    $montant=$unClassement['montantrembourse'];
			?>
					<tr>
                        <td><?php echo $rang ?></td>
						<td><?php echo $nom ?></td>
                        <td><?php echo $prenom ?></td>
                        <td><?php echo $montant ?></td>
                    </tr>
			
			<?php
				}
			?>
</table>