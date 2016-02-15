<?php
/*
     This file is part of Cybermin.

    Cybermin is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    Cybermin is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Cybermin; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

 2006 Namont Nicolas
 

*/

// affichage  -----------

//chargement du tarif
$tarif_r=$_POST['ptarif']; //bouton d&eacute;roulant
$ptarif=$_GET['catTarif']; //bouton modification

if(isset($ptarif)){
		$tarif_r=$ptarif;
		}
		
if (isset($tarif_r)){
	$tarif=$tarif_r;
}else{
	$tarif=1; //tarif par d&eacute;faut 
	$tarif_r=1;
	$ptarif=1;
	}


$mesno= $_GET["mesno"];
if ($mesno !="")
{
  echo getError($mesno);
}

$espaces = getAllepn();
// tableau unit&eacute;s pour les ateliers
$dureetype=array(0=>'Illimit&eacute;e', 
1=>'An(s)', 
2=>'Mois', 
3=>'Jour(s)' );


// Tableau des unité de durée forfait
    $tab_unite_duree_forfait = array(
           1=> "Jour",
           2=> "Semaine",
           3=> "Mois",
					 4=> "Illimit&eacute;e"
    );
	
	// Tableau des unité d'affectation
    $tab_unite_temps_affectation = array(
           1=> "Minutes",
           2=> "Heures"
    );
	
	// Tableau des fréquence d'affectation
    $tab_frequence_temps_affectation = array(
           1=> "par Jour",
           2=> "par Semaine",
           3=> "par Mois"
    );


?>
<!-- DIV acces direct aux autres parametres-->
 <div class="row">
   <div class='col-md-12'>
 <div class="box collapsed-box">
		<div class="box-header ">
			<h3 class="box-title">Param&eacute;trages</h3>
			<div class="box-tools pull-right"><button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button> </div>
			</div>
		
		<div class="box-body">
			<a class="btn btn-app" href="index.php?a=41"><i class="fa fa-cloud"></i> VILLES <a>
			<a class="btn btn-app " href="index.php?a=43"><i class="fa fa-home"></i> EPN</a>
			<a class="btn btn-app" href="index.php?a=44"><i class="fa fa-square"></i> SALLES</a>
			<a class="btn btn-app" href="index.php?a=42"><i class="fa fa-clock-o"></i> HORAIRES</a>
			<a class="btn btn-app disabled" href="index.php?a=47"><i class="fa fa-eur"></i> TARIFS</a>
			<a class="btn btn-app" href="index.php?a=2"><i class="fa fa-desktop"></i> MATERIEL</a>
			<a class="btn btn-app" href="index.php?a=48"><i class="fa fa-user-md"></i> USAGES</a>
			<a class="btn btn-app" href="index.php?a=46"><i class="fa fa-caret-square-o-up"></i> USAGES POSTES</a>
			<a class="btn btn-app" href="index.php?a=23"><i class="fa fa-users"></i> ADMIN/ANIM</a>
			<a class="btn btn-app" href="index.php?a=49"><i class="fa fa-database"></i> BDD</a>
			<a class="btn btn-app" href="index.php?a=25&act=0"><i class="fa fa-unlock-alt"></i> EPN-CONNECT</a>
			
		</div><!-- /.box-body -->
</div><!-- /.box -->
</div>
</div>
 <div class="row">
<!-- Accordeon sur les nouveaux tarifs  -->	
<div class='col-md-4'>
	<div class="box box-solid box-warning">
		<div class="box-header with-border"><i class="glyphicon glyphicon-plus"></i><h3 class="box-title">Nouveau Tarif</h3>
			<div class="box-tools pull-right"><button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button> </div>
			</div>
		<div class="box-body">
		
		<!-- id 1 : les impressions -->
		<div class="box-group" id="accordion">
                    <!-- we are adding the .panel class so bootstrap.js collapse plugin detects it -->
			<div class="panel box box-primary">
			  <div class="box-header with-border">
				<h4 class="box-title"><a data-toggle="collapse" data-parent="#accordion" href="#1impression"> Impressions / Adh&eacute;sion / Divers</a></h4>
			  </div>
			  <div id="1impression" class="panel-collapse collapse">
				<div class="box-body">
				 <form method="post" action="index.php?a=47&actarif=1&typetarif=1" class="form">
				<div class="form-group"><input type="text" class="form-control" placeholder="Nom du tarif*" name="newnomtarif"></div>
				<div class="form-group"><input type="text" class="form-control" placeholder="prix*  0.15" name="newprixtarif"></div>
				<div class="form-group"><textarea class="form-control" placeholder="description" name="newdescriptiontarif"></textarea></div>
				<div class="form-group"><label >Cat&eacute;gorie *:</label><select name="catTarif" class="form-control">
						
						<?php
						$categorieTarif=array(
							1=>"impression",
							2=>"adhesion",
							3=>"consommables",
							4=>"Divers"
							);
							
							foreach ($categorieTarif AS $key=>$value)
							{
								if ($catTarif == $key)
								{
									echo "<option value=\"".$key."\" selected>".$value."</option>";
								}
								else
								{
									echo "<option value=\"".$key."\">".$value."</option>";
								}
							}
							
						?></select></div>
					<div class="form-group">
						<label >Espace *:</label>
						<select name="espace[]" multiple class="form-control">
						<?php
								foreach ($espaces AS $key=>$value)
								{
									if ($espace == $key)
									{
										echo "<option value=\"".$key."\" selected>".$value."</option>";
									}
									else
									{
										echo "<option value=\"".$key."\">".$value."</option>";
									}
								}
							?>
							</select></div>
				</div>
				<div class="box-footer"><a type="submit" value="Ajouter"><button class="btn btn-primary">Ajouter</button></a></div></form>
				</div>
			  </div><!-- panel -->
        </div><!-- accordeon -->
		<!-- FIN IMPRESSIONS -->
		
		<!-- id 2 : les Ateliers -->
		<div class="box-group" id="accordion">
                    <!-- we are adding the .panel class so bootstrap.js collapse plugin detects it -->
		<div class="panel box box-success">
		  <div class="box-header with-border">
			<h4 class="box-title"><a data-toggle="collapse" data-parent="#accordion" href="#2atelier"> Ateliers</a></h4>
		  </div>
		  <div id="2atelier" class="panel-collapse collapse">
                        <div class="box-body"><form method="post" action="index.php?a=47&actarif=1&typetarif=2" class="form">
			 <div class="form-group"><input type="text" class="form-control" placeholder="Nom du tarif*" name="newnomtarifa"></div>
			 <div class="form-group"><textarea rows="2" class="form-control" placeholder="description" name="newdescriptiontarifa"></textarea></div>
			<div class="form-group"><input type="text" class="form-control" placeholder="prix*  0.15" name="newprixtarifa"></div>
			<div class="form-group"><label>Nombre d'ateliers*</label><input type="number" class="form-control" value="0" min="0" name="newnumbertarifa"></div>
				<div class="form-group"><label>Limite de validit&eacute;*</label>
					 <div class="row">
						<div class="col-xs-5">
							<input type="number" class="form-control"  value="0" min="0" name="dureetarifa"></div>
						<div class="col-xs-5">
							<select type="text" class="form-control"  name="typedureetarifa">
							<?php
								foreach ($dureetype AS $key=>$value)
						{
							if ($duree == $key)
							{
								echo "<option value=\"".$key."\" selected>".$value."</option>";
							}
							else
							{
								echo "<option value=\"".$key."\">".$value."</option>";
							}
						}
							?>	
							</select></div></div>
				</div>
			<div class="form-group">
				<label >Espace *:</label>
				<select name="espace[]" multiple class="form-control">
				<?php
						foreach ($espaces AS $key=>$value)
						{
							if ($espace == $key)
							{
								echo "<option value=\"".$key."\" selected>".$value."</option>";
							}
							else
							{
								echo "<option value=\"".$key."\">".$value."</option>";
							}
						}
					?>
					</select></div>
		 
		 </div>
		 <div class="box-footer"><a type="submit" value="Ajouter"><button class="btn btn-primary">Ajouter</button></a></div></form>
	   </div>
	  </div>
	</div>
		
		
		
		<!-- id 3 : les consultations -->
		<div class="box-group" id="accordion">
                    <!-- we are adding the .panel class so bootstrap.js collapse plugin detects it -->
                    <div class="panel box box-danger">
					<div class="box-header with-border">
                        <h4 class="box-title"><a data-toggle="collapse" data-parent="#accordion" href="#3consult"> Consultation</a></h4>
                      </div>
                      <div id="3consult" class="panel-collapse collapse">
                        <div class="box-body">
						<form method="post" action="index.php?a=47&actarif=1&typetarif=3" class="form">
							<div class="form-group"><input type="text" class="form-control" placeholder="Nom du tarif*" name="nom_forfait"></div>
							<div class="form-group"><input type="text" class="form-control" placeholder="prix*  0=gratuit" name="prix_forfait"></div>
							<div class="form-group"><textarea class="form-control" placeholder="Description" name="comment_forfait"></textarea></div>
							<div class="form-group"><label>Limite de validit&eacute; du forfait *</label>
								 <div class="row">
									<div class="col-xs-5">
										<input type="number" value="0" min="0" class="form-control"  name="nombre_duree_forfait"></div>
									<div class="col-xs-5">
										<select  class="form-control"  name="unite_duree_forfait">
											<?php
													foreach ($tab_unite_duree_forfait AS $key=>$value)
											{
												if ($unite == $key)
												{
													echo "<option value=\"".$key."\" selected>".$value."</option>";
												}
												else
												{
													echo "<option value=\"".$key."\">".$value."</option>";
												}
											}
												?>	
										</select></div>
								</div><br>
								</div>
							<div class="form-group"><label>Dur&eacute;e de la consultation *</label>
								 <div class="row">
									<div class="col-xs-3">
										<input class="form-control" type="number" value="0" min="0" name="nombre_temps_affectation"></div>
									<div class="col-xs-4">
										<select type="text" class="form-control"  name="unite_temps_affectation">
											<?php
													foreach ($tab_unite_temps_affectation AS $key=>$value)
											{
												if ($unite == $key)
												{
													echo "<option value=\"".$key."\" selected>".$value."</option>";
												}
												else
												{
													echo "<option value=\"".$key."\">".$value."</option>";
												}
											}
												?>	
											
										</select></div>
									
									<div class="col-xs-5">
										<select  class="form-control"  name="frequence_temps_affectation">
											<?php
													foreach ($tab_frequence_temps_affectation AS $key=>$value)
											{
												if ($freq == $key)
												{
													echo "<option value=\"".$key."\" selected>".$value."</option>";
												}
												else
												{
													echo "<option value=\"".$key."\">".$value."</option>";
												}
											}
												?>	
										</select></div>
								</div>
								</div>
								
								<div class="form-group"><label> ou Affect. occasionelle (en min)&nbsp;<small class="badge bg-blue" data-toggle="tooltip" title="Pour la consultation de type one shot ! La validit&eacute; est expir&eacute;e après d&eacute;pense."><i class="fa fa-info"></i></small></label><input type="number" value="" min="0" class="form-control" name="temps_affectation_occasionnel"></div>	
								
								<div class="form-group">
									<label >Espace *:</label>
									<select name="espace[]" multiple class="form-control">
									<?php
											foreach ($espaces AS $key=>$value)
											{
												if ($espace == $key)
												{
													echo "<option value=\"".$key."\" selected>".$value."</option>";
												}
												else
												{
													echo "<option value=\"".$key."\">".$value."</option>";
												}
											}
										?>
										</select></div>	
										
						
					</div>	
						<div class="box-footer"><a type="submit" value="Ajouter"><button class="btn btn-primary">Ajouter</button></a></div>
						</form> </div>
                    </div>
		</div>
		</div>
				
		</div>
</div>


<!-- MODIFICATION DES TARIFS -->
 
 <div class='col-md-8'>
 
  <div class="box box-default">
  <div class="box-header with-border"><h3 class="box-title">Tous les tarifs par cat&eacute;gorie </h3>
  <div class="box-tools pull-right">
      <div class="has-feedback"><form action="index.php?a=47" method="post" role="form" >
				<div class="input-group input-group-sm">
			<select name="ptarif"  class="form-control pull-right" style="width: 200px;">
       	<?php
        $categorieTarif=array(
							1=>"Impression",
							2=>"Adh&eacute;sion",
							5=>"Atelier",
							6=>"Consultation",
							3=>"Consommables",
							4=>"Divers"
							);
							
							foreach ($categorieTarif AS $key=>$value)
							{
								if ($tarif == $key)
								{
									echo "<option value=\"".$key."\" selected>".$value."</option>";
								}
								else
								{
									echo "<option value=\"".$key."\">".$value."</option>";
								}
							}
		?>
							</select>
	
		<span class="input-group-btn"><button type="submit" name="submit" value="Valider" class="btn btn-default"><i class="fa fa-repeat"></i></button></span>
	</form></div>
	
	<!--<button class="btn bg-blue btn-sm"  data-toggle="tooltip" title="Si votre EPN fait payer les ateliers, déclarez le tarif correspondant, le décompte sera automatiquement effectué en fonction des achats de vos adhérents."><i class="fa fa-info"></i></button>-->
		
		</div></div></div></div>
	
 
<?php
///*** gestion des tarifs AJOUT 2014
if($tarif<6)
{

$tarifbycat=getTarifs($tarif);
$nbt=mysqli_num_rows($tarifbycat);

 
if ($nbt==0)
    {
        echo '<div class="col-md-6">
				<div class="alert alert-info alert-dismissable"><i class="fa fa-info"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                 <b>Pas de tarifs encore !</b></div></div>' ;
    }else{
	
	$categorieTarif=array(
	1=>"Impression",
	2=>"Adhesion",
	3=>"Consommables",
	4=>"Divers",
	5=>"Forfait Atelier"
	);
			
			for ($i=0 ; $i<$nbt ; $i++)
			{
			$row=mysqli_fetch_array($tarifbycat);
			$catTarif=$row['categorie_tarif'];
			$id_tarif=$row['id_tarif'];
			$espace=explode('-',$row['epn_tarif']);
		//	debug($espace);
			
			?>
 <div class="box box-warning">
	<div class="box-header"><h3 class="box-title"><?php echo $row['nom_tarif']; ?></h3></div>
	 <div class="box-body no-padding">  <table class="table">
		<form  method="post" class="form" action="index.php?a=47&actarif=2&idtarif=<?php echo $row['id_tarif'] ; ?>" >
		<tr>
		<td  width="100%">
	
<?php 
	if($catTarif==5){
		$dureenumarray=explode('-',$row["duree_tarif"]);
		$duree2=$dureenumarray[1];
		?>
		
		<div class="input-group" >
		 <div class="row">	<div class="col-xs-5"><label>Libell&eacute;</label>
		 <input type="hidden" name="catTarif" value="<?php echo $catTarif; ?> ">
		 <input type="hidden" name="ptarif" value="<?php echo $catTarif; ?> "><input type="text" class="form-control" name="nomtarif" value="<?php echo $row['nom_tarif']; ?> "></div>
						<div class="col-xs-4"><label>Prix</label><input type="text"  class="form-control" name="prixtarif" value="<?php echo $row['donnee_tarif']; ?>  "></div>
						<div class="col-xs-3"><label>Nbre d'ateliers</label><input type="text" class="form-control" name="numberatelier" value="<?php echo  $row["nb_atelier_forfait"] ?>"></div></div>
		
		<div class="row">	<div class="col-xs-5"><label>Commentaire</label><textarea rows="2" class="form-control" name="descriptiontarif" ><?php echo stripslashes($row['comment_tarif']); ?></textarea></div>
						<div class="col-xs-4"><label><br>Limite de validit&eacute;</label><br>
							<input type="number"  name="dureetarif" value="<?php echo $dureenumarray[0] ?>" style="width:50px;">
								<select type="text"   name="typedureetarif">
								<?php		
							foreach ($dureetype AS $key=>$value)
									{
								
										if ($key==$duree2)
										{
											echo "<option value=\"".$key."\" selected>".$value."</option>";
										}
										else
										{
											echo "<option value=\"".$key."\">".$value."</option>";
										}
									} ?>
							</select></div>
							
				<div class="col-xs-3"><label>Espaces</label><select name="espace[]" multiple class="form-control">
							<?php
								
									foreach ($espaces AS $key=>$value)
									{
										
										if (in_array($key,$espace))
										{
											echo "<option value=\"".$key."\" selected>".$value."</option>";
										}
										else
										{
											echo "<option value=\"".$key."\">".$value."</option>";
										}
									}
								?>
								</select></div>
		</div>
	</div>
	<?php	
	
	 } else {	?>
	
	<div class="form-group">
		 <div class="row"><div class="col-xs-6"><label>&nbsp;</label><input type="hidden" name="catTarif" value="<?php echo $catTarif; ?>"><input type="text" class="form-control" name="nomtarif" value="<?php echo $row['nom_tarif']; ?> "></div>
						<div class="col-xs-6"><label>Prix</label><input type="text"  class="form-control" name="prixtarif" value="<?php echo $row['donnee_tarif']; ?>  "></div>
						</div>	
		
		<div class="row"><div class="col-xs-6"><label>&nbsp;</label><textarea rows="2" class="form-control" name="descriptiontarif" ><?php echo stripslashes($row['comment_tarif']); ?></textarea></div>
						<div class="col-xs-6"><label>Espaces</label><select name="espace[]" multiple class="form-control">
							<?php
								
									foreach ($espaces AS $key=>$value)
									{
										
										if (in_array($key,$espace))
										{
											echo "<option value=\"".$key."\" selected>".$value."</option>";
										}
										else
										{
											echo "<option value=\"".$key."\">".$value."</option>";
										}
									}
								?>
								</select></div>
						</div>	
		</div>			
	<?php } 	?>
		
		
	</td><td><button class="btn btn-success btn-sm"  type="submit" value="modifier"><i class="fa fa-edit"></i></button>&nbsp;<a href="index.php?a=47&actarif=3&idtarif=<?php echo $row['id_tarif']; ?>"><button type="button" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></a></td>
	</tr>
</form>
</table></div></div>
					 
			<?php		 
			} // end FOR cat tarif 1 to 5
		}
		
	}else{
		// Affichage du tarif consultation (6)
		$consultation=getTarifConsult();
		$nbc=mysqli_num_rows($consultation);
	
		if($nbc==0)
    {
        echo '<br><div class="col-md-6">
				<div class="alert alert-info alert-dismissable"><i class="fa fa-info"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                 <b>Pas de tarifs encore !</b></div></div>' ;
    }else{
			/// affichage de l'array des consultations
			for($y=0;$y<$nbc;$y++){
			$rowconsult=mysqli_fetch_array($consultation);
			//debug($rowconsult);
		//$nombre_temps_affectation = $rowconsult["nombre_temps_affectation"];
		$unite_temps_affectation = $rowconsult["unite_temps_affectation"];
		$frequence_temps_affectation= $rowconsult["frequence_temps_affectation"];
			
		if ($rowconsult["temps_forfait_illimite"]=='1'){
				$unite_duree_forfait = 4;
		}else{
				$unite_duree_forfait= $rowconsult["unite_duree_forfait"];
		}
		
		$epnC=getAllRelForfaitEspace($rowconsult['id_forfait']);
		//debug($epnC);
		
			?>
	<div class="box box-warning">
	<div class="box-header"><h3 class="box-title"><?php echo $rowconsult['nom_forfait']; ?></h3></div>
	 <div class="box-body no-padding">  <table class="table">
			<form  method="post" class="form" action="index.php?a=47&actarif=2&idtarif=<?php echo $rowconsult['id_forfait'] ; ?>" >
			<tr><td>
					<div class="form-group">
							<div class="row"><div class="col-xs-4"><label>Libell&eacute;</label>
							<input type="hidden" name="catTarif" value="6">
							<input type="hidden" name="id_forfait" value="<?php echo $rowconsult['id_forfait']; ?>">
							<input type="hidden" name="ptarif" value="6">
							<input type="text" class="form-control" name="nom_forfait" value="<?php echo $rowconsult['nom_forfait']; ?> "></div>
							<div class="col-xs-3"><label>Prix (&euro;)</label><input type="text"  class="form-control" name="prix_forfait" value="<?php echo $rowconsult['prix_forfait']; ?>  "></div>
							<div class="col-xs-5"><label>Description</label><textarea rows="2" class="form-control" name="commentaire_forfait"><?php echo $rowconsult['commentaire_forfait']; ?></textarea></div>
						</div></div>
					
					<div class="form-group">
								 <div class="row">
									<div class="col-xs-2"><label>Validit&eacute;</label><input type="number" min="0" class="form-control"  value="<?php echo $rowconsult['nombre_duree_forfait']; ?>" name="nombre_duree_forfait"></div>
									<div class="col-xs-3"><label>&nbsp;</label>
										<select  class="form-control"  name="unite_duree_forfait">
											<?php
													foreach ($tab_unite_duree_forfait AS $key=>$value)
											{
												if ($unite_duree_forfait == $key)
												{
													echo "<option value=\"".$key."\" selected>".$value."</option>";
												}
												else
												{
													echo "<option value=\"".$key."\">".$value."</option>";
												}
											}
												?>	
										</select></div>
									<div class="col-xs-5"><label>Affect. occasionelle (en min)&nbsp;<small class="badge bg-blue" data-toggle="tooltip" title="Pour la consultation de type one shot ! La validit&eacute; est expir&eacute;e après d&eacute;pense."><i class="fa fa-info"></i></small></label><input type="number" min="0" class="form-control" placeholder="en min" name="temps_affectation_occasionnel" value="<?php echo $rowconsult['temps_affectation_occasionnel']; ?>"></div>
								</div><br>
								</div>
								
					<div class="form-group"><label>Dur&eacute;e de la consultation</label>
								<div class="row">
									<div class="col-xs-2">
										<input class="form-control" type="number" min="0" name="nombre_temps_affectation" value="<?php echo $rowconsult['nombre_temps_affectation']; ?>"></div>
									<div class="col-xs-2">
										<select type="text" class="form-control"  name="unite_temps_affectation">
											<?php
													foreach ($tab_unite_temps_affectation AS $key=>$value)
											{
												if ($unite_temps_affectation == $key)
												{
													echo "<option value=\"".$key."\" selected>".$value."</option>";
												}
												else
												{
													echo "<option value=\"".$key."\">".$value."</option>";
												}
											}
												?>	
										</select></div>
									
									<div class="col-xs-3">
										<select  class="form-control"  name="frequence_temps_affectation">
											<?php
													foreach ($tab_frequence_temps_affectation AS $key=>$value)
											{
												if ($frequence_temps_affectation == $key)
												{
													echo "<option value=\"".$key."\" selected>".$value."</option>";
												}
												else
												{
													echo "<option value=\"".$key."\">".$value."</option>";
												}
											}
												?>	
										</select></div>
							
								
								<div class="col-xs-5">
									<select name="espace[]" multiple class="form-control">
									
									<?php
									
									foreach ($espaces AS $key=>$value)
									{
										
										if (in_array($key,$epnC))
										{
											echo "<option value=\"".$key."\" selected>".$value."</option>";
										}
										else
										{
											echo "<option value=\"".$key."\">".$value."</option>";
										}
									}
										?>
										</select></div>	
										</div>	
						
					</div>	
				
						
			</td><td><button class="btn btn-success btn-sm"  type="submit" value="modifier"><i class="fa fa-edit"></i>
			</button>&nbsp;<a href="index.php?a=47&actarif=3&typetarif=3&idtarif=<?php echo $rowconsult['id_forfait']; ?>"><button type="button" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></a></td>
			</tr>
		</form>
	</table></div></div>
	
	<?php
			}
		
		}
	
	}

?>


</div></div>

