<?php

// fichier de gestion des impressions
$month=date('m');
	
if (TRUE==checkPrint($_SESSION['iduser']))
{
	//$row=getPrint($_SESSION['iduser']);
	
		// infos impressions
		$credituser=getCreditUser($_SESSION['iduser']);
		$totalprint=getDebitUser($_SESSION['iduser']);
		$restant=$credituser-$totalprint;
?>
<div class="row">

<section class="col-lg-4 connectedSortable">		

<div class="box box-primary">
		<div class="box-header"><h3 class="box-title">Mes impressions</h3></div>
		<div class="box-body"><h4><b><?php echo $totalprint ?></b>  &euro; ont été dépensés.</h4>
			<h4><b><?php echo $credituser; ?></b> &euro; ont été crédités.</h4>
			<br>
		<?php if( ($credituser-$totalprint)>0){
			echo '<h4><span class="text-green">Credit restant sur le compte : '.$restant.' &euro; </span></h4>';
		}else if( ($credituser-$totalprint)<0){
			echo '<h4><span class="text-red">Le compte est débiteur de  '.$restant.' &euro; </span></h4>';
		}else if(($credituser-$totalprint)==0){
			echo '<h4>Aucun cr&eacute;dit restant sur le compte</h4>';
		}
		?>
			</div></div>
</section>
	
	
<section class="col-lg-6 connectedSortable">
	
	
				
<?php

// ARCHIVES DES IMPRESSIONS
	$result = getPrintById($_SESSION['iduser'],$month) ;
	$credituser=getCreditPrintId($id_user);
	if($result!=FALSE)
	{
	?>
	<div class="box box-primary"><div class="box-header"><h3 class="box-title">Historique de vos impressions</h3></div>
				<div class="box-body"><table class="table"> 
				<thead><tr> 
				   <th>Date</th><th>Nbre de pages</th><th>Tarif</th><th>Prix</th>
				   </tr><tbody>
		<?php
				   
		while($row = mysqli_fetch_array($result))
		{
		// retrouver le tarif
		$tarif=mysqli_fetch_array(getPrixFromTarif($row['print_tarif']));
		$prix=round(($row['print_debit'] * $tarif['donnee_tarif']),2);
		$statut=$statutPrint[$row['print_statut']];
				
		echo "<tr><td>".$row['print_date']."</td>
				  <td>".$row['print_debit']."</td>
				  <td>".$tarif['donnee_tarif']."</td>
				  <td>".$prix." &nbsp;&euro;</td>";
			
			if($externe==1){
				echo '<td>'.$nomexterne.'</td>';
			}
			
			if($row['print_statut']==0){ 
				
					echo "<td><p class=\"text-red\">".$statut."</p></td> 
					";
				}else{
					// transaction enregistrée
					echo "<td><p class=\"text-light-blue\">".$statut."</p></td> <td>&nbsp;</td>";
				}
			echo " </tr>";
		}
		echo "</tbody></table></div></div>" ;
	}
}
else
{
	echo "<h4 class=\"alert_info\">Pas d'historique d'impressions</h4>" ;
	
}
?>
</section></div>


<!--
<article class="module_help width_half"><header><h3>Aide</h3></header>
<div class="module_content">
	<p>Si vous désirez imprimer depuis internet, demandez conseil à l'animatrice.</p>
	<p>Les tarifs : <b>0.15&euro;</b> la page noir et blanc, <b>0.30&euro;</b> la page couleur.</p>
	<p>Par défaut l'imprimante est paramétrée pour imprimer en noir et blanc. Pour imprimer en couleur, cliquez sur l'imprimante <a href="#">Couleur</a> pour la sélectionner dans le menu déroulant des imprimantes. Lancer ensuite votre impression.</p>
</div></article>-->
