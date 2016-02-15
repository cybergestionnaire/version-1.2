<?php
/*
2013 Modification
Fichier servant � modifier/cr�er la programmation d'un atelier

*/
if ($mess !="")
{
  echo $mess;
}

$id  = $_GET["idatelier"];
//recup�rer les sujets d'atelier
$atelier=getAllSujet();
//recup�rer les animateurs
$allanim=getAllAnim();
//r�cup�rer les salles
$allsalles=getAllSalleAtelier();



if (FALSE == isset($id))
{  // creation
  $post_url = "index.php?a=12&m=1";
  $label_bouton = "Programmer" ;
		$public="Tous public";
		$anim=$_SESSION["iduser"];
		//statut de l'atelier
$stateAtelier = array(
	0=> "En cours",
	1=> "En programmation"
	//2=> "Clotur�",
	//3=> "Annul�"
	);
		
}
else
{ // modification
        $post_url = "index.php?a=14&m=2&idatelier=".$id;
        $label_bouton = "Modifier l'atelier" ;
		
        $row = getAtelier($id);
		
        //Informations matos
        $date = $row["date_atelier"];
        $heure = $row["heure_atelier"];
        $nbplace = $row["nbplace_atelier"];
		$duree= $row["duree_atelier"];
		$public =$row["public_atelier"];
		$anim =$row["anim_atelier"];
		$sujet =$row["id_sujet"];
		$statut=$row["statut_atelier"];
		$salle=$row["salle_atelier"];
		$tarif=$row["tarif_atelier"];
	//statut de l'atelier
$stateAtelier = array(
	0=> "En cours",
	1=> "En programmation",
	2=> "Clotur�",
	3=> "Annul�"
	);
}

//recuperation des tarifs categorieTarif(5)=forfait atelier
$tarifs=getTarifsbyCat(5);

//pas de programmation possible su aucun sujet d'atelier n'a �t� rentr�
if(FALSE==$atelier){
?>
<div class="row"><div class="col-md-6">
	<div class="alert alert-warning alert-dismissable"><i class="fa fa-warning"></i>
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>&nbsp;Avant d'�tablir une programmation, vous devez cr�er au moins un sujet d'atelier.</div>
	</div>
	<div class="col-md-6">
	<div class="alert alert-info alert-dismissable"><i class="fa fa-warning"></i>
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>&nbsp;<a href="index.php?a=15">Cr�er un nouveau sujet</a></div>
	</div>

</div>

<?php	
}else{

?>

<div class="row">
 <!-- Left col -->  <div class="col-md-6">
<div class="box box-success">
	<div class="box-header"><h3 class="box-title">Planification d'un atelier</h3></div>
	<form method="post" action="<?php echo $post_url; ?>" role="form">
	<div class="box-body">
	
	<div class="form-group"><label><span class="text-red">Sujet*</span></label>
   	 <select name="sujet" class="form-control">
	    <?php
		foreach ($atelier AS $key=>$value)
		{
		    if ($sujet == $key)
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
	
         
     
   
	<div class="row">
		<div class="col-lg-6"><label><span class="text-red">Places disponibles*</span></label>
		 <div class="input-group">
			<input type="text" name="nbplace" value="<?php echo $nbplace;?>" class="form-control">
		</div></div>
	
		<div class="col-lg-6"><label>Dur&eacute;e</label>
		<div class="input-group">
			<!--<input type="text" name="duree" value="<?php echo $duree;?>" class="form-control" placeholder="en min">-->
			<select name="duree" class="form-control">
			<option value="30" selected>30 min</option>
			<option value="60" >1h</option>
			<option value="90" >1h30 min</option>
			<option value="120" >2h</option>
			<option value="150" >2h30 min</option>
			<option value="180" >3h</option>
    		</select>
		</div></div>
	</div>
	  <br>
	<div class="row">
		<div class="col-lg-6"><label><span class="text-red">Date*</span></label>
                      <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-calendar"></i></span>
			<input name="date" id="dt0" placeholder="Prenez une date"  value="<?php echo $date; ?>" class="form-control">
		</div></div>
		
		
		<div class="col-lg-6"><label><span class="text-red">Heure*</span></label>
                      <div class="input-group"><span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
			<input  id="dt1" name="heure" value="<?php echo $heure;?>" class="form-control" placeholder="10h">
		</div><!-- /.input group -->
		</div>
	</div>

	</div><!-- /box-body -->
</div><!-- /box -->	
</div><!-- /col -->

  <div class="col-md-6">	
  <div class="box box-success"><div class="box-header"><h3 class="box-title"></h3></div>
	<div class="box-body">
	
   
	<div class="form-group"><label>Public concern&eacute;</label>
        <input type="text" name="public" value="<?php echo $public;?>" class="form-control"></div>
        
        <div class="form-group"><label>Salle</label>
        	<select name="salle" class="form-control">
       	<?php
        foreach ($allsalles AS $key=>$value)
        {
            if ($salle == $key)
            {
                echo "<option value=\"".$key."\" selected>".$value."</option>";
            }
            else
            {
                echo "<option value=\"".$key."\">".$value."</option>";
            }
        }
		
    ?></select></div>
    
	<div class="form-group"><label>Anim&eacute; par </label>
		 <select name="anim" class="form-control">
	    <?php
		foreach ($allanim AS $key=>$value)
		{
		    if ($anim == $key)
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
	<div class="form-group"><label>Tarif</label>
	 <!-- tools box -->
	    <div class="pull-right box-tools">
		<button class="btn btn-primary btn-sm" data-toggle="tooltip" title="Si un atelier fait partie d'une tarification sp�ciale, choisissez-l� ici, sinon laissez le 'sans tarif' par d�faut, le d�compte des ateliers se fera en fonction de ce qui a �t� pay� par l'adh�rent."><i class="fa fa-info-circle"></i></button>
	    </div><!-- /. tools -->
	
  		<select name="tarif" class="form-control" >
		<?php
			foreach ($tarifs AS $key=>$value)
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
		</select></div>	
    
    
<div class="form-group"><label>Statut </label>
    <select name="statut" class="form-control">
    <?php
        foreach ($stateAtelier AS $key=>$value)
        {
            if ($statut == $key)
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

    <div class="box-footer"> <input type="submit" name="submit_atelier" value="<?php echo $label_bouton; ?>"  class="btn btn-primary"></div>
</form>
</div><!-- /box -->	
</div><!-- /col -->
</div><!-- /row -->
	

<script src='rome-master/dist/rome.js'></script>
<script src='rome-master/example/atelier.js'></script>


<?php } ?>