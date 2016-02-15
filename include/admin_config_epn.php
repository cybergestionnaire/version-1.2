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
 

  include/admin_materiel.php V0.1
*/

// Fichier de gestion des salles ...
$mesno= $_GET["mesno"];
if ($mesno !="")
{
  echo getError($mesno);
}

?>

<!-- DIV accès direct aux autres paramètres-->
 <div class="box">
		<div class="box-header">
			<h3 class="box-title">Paramétrages</h3>
		</div>
		<div class="box-body">
		
		
			<?php 
			//debug($_GET["a"]);
			echo configBut($_GET["a"]) ;
		
			?>
			
		</div><!-- /.box-body -->
</div><!-- /.box -->


<div class="row">
 <!-- Left col --><section class="col-lg-9 connectedSortable">
  
<!-- liste des espaces existants-->
<div class="box box-solid box-warning"><div class="box-header"><h3 class="box-title">Liste des espaces</h3></div>
	 <div class="box-body no-padding"> <table class="table">
			<thead> <tr> 
   				<th>Nom de l'espace</th> 
				<th>Adresse</th>
				<th>Ville</th>
				<th>Mail</th>
				<th>&nbsp;</th>
				</tr></thead><tbody>
    <?php
    $result=getAllEspace();
    if (FALSE == $result)
    {
        echo getError(30) ;
    }
    else
    {
			$nb=mysqli_num_rows($result);
			if ($nb == 0)
			{
				echo getError(30);
			}
			else
			{
			  	for ($i=0;$i<$nb;$i++)
			  	{
					$row = mysqli_fetch_array($result);
    				$ville=getCity($row["id_city"]);
    				
            		?>
                <tr>
                    <td><?php echo $row["nom_espace"]; ?></td>
                    <td><?php echo $row["adresse"]; ?></td>
                    <td><?php echo $ville; ?></td>
                    <td><?php echo $row["mail_espace"]; ?></td>
                    <td ><a href="index.php?a=43&b=2&idespace=<?php echo $row["id_espace"];?>"><button class="btn btn-success btn-sm"  type="submit" value="modifier"><i class="fa fa-pencil-square-o"></i></button></a>
                <?php if($row["id_espace"]>1){  //suppression de l\'epn de reference impossible 
				?>
					<a href="index.php?a=43&b=3&act=3&idespace=<?php echo $row["id_espace"];?>"><button type="button" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></a>
				<?php }  ?>
				</td></tr>
            	<?php
          	}
        }
    }

        ?>
<tbody></table></div></div>

    </section><!-- /.Left col -->

 <section class="col-lg-3 connectedSortable"> 
<!-- bouton nouvel espace-->
<div class="small-box bg-light-blue">
                <div class="inner"><h3>&nbsp;</h3><p>Nouvel EPN</p></div>
		<div class="icon"><i class="ion ion-home"></i></div>
	<a href="index.php?a=43&b=1" class="small-box-footer">Ajouter <i class="fa fa-arrow-circle-right"></i></a>
</div>
</section>

</div>
