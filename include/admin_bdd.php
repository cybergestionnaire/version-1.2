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
 

 include/admin_bdd.php V0.1
*/

// Gestion de la base de donn�es

$total_usagers=$_POST["total_usagers"];
$total_echec=$_POST["total_echec"];
$total_actif=$_POST["total_actif"];
$total_inactif=$_POST["total_inactif"];

$tariftemps=getTarifsTemps();

if ($mess==1)
{
	echo "<div>le repertoire de stockage 'SQL' &agrave; la racine de l'application Cybermin n'est pas accessible en &eacute;criture.<br /> Vous devez autoriser l'acc&egrave;s pour pouvoir sauvegarder le fichier</div>";
}
?>
<!-- DIV acc�s direct aux autres param�tres-->
<div class="row">
<div class="col-lg-12">
 <div class="box">
		<div class="box-header">
			<h3 class="box-title">Param�trages</h3>
				<div class="box-tools pull-right"><button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button> </div>
		</div>
		<div class="box-body">
			
			<?php 
			//debug($_GET["a"]);
			echo configBut($_GET["a"]) ;
		
			?>
			
		</div><!-- /.box-body -->
</div><!-- /.box -->
</div>
 <!-- Left col --><div class="col-lg-6">
  
<!-- liste des espaces existants-->
 <div class="box box-warning"><div class="box-header"><h3 class="box-title">Sauvegarde des donn&eacute;es</h3></div>
 <div class="box-body">
      
       <form method="post" action="index.php?a=49&act=save" role="form">
       <div class="form-group">
           <input type="radio" name="action" value="2" checked >
           <label> Transmettre dans un fichier :</label><input type="text" name="fichier" value="<?php echo date('ymd');?>_gestionnaire.cyb"></div>
           <div class="form-group">
           <input type="radio" name="action" value="1"><label> Afficher le contenu de la sauvegarde</label></div>
           <div class="form-group">
           <input type="submit" value="Sauvegarder les donn&eacute;es"  class="btn btn-default"></div>
           
           <?php
           if ($save !="")
           {
               echo "<div class=\"form-group\"><textarea style=\"font-size:9pt;width:100%\"  rows=\"4\" class=\"form-control\">".$save."</textarea></div>" ;
           }
           ?>
          
       </form>
<?php
// Restauration, cette fonction est desactiv� pour des raison de s�curit�
/*
       <tr>
           <td>&nbsp;</td></tr>
       <tr class="list_title">
           <td>Restauration de la base de donn&eacute;es</td></tr>
       <form method="post" action="index.php?a=8&act=restore" enctype="multipart/form-data">
       <tr class="list">
           <td>Fichier de sauvegarde : <input type="file" name="restore_file">&nbsp;&nbsp;<input type="submit" value="Restaurer les donn&eacute;es"></td></tr>
       </form>

*/
?>     <div class="box-footer"><p>
           Pour restaurer les donn&eacute;es merci de passer par phpmyadmin pour r�importer un fichier de sauvegarde.<br></p></div>
</div>
</div>

<!-- aide restauration-->
 <div class="box box-info"><div class="box-header"><h3 class="box-title">Aide � l'import de la base des adh�rents depuis un CSV Cybanim</h3></div>
 <div class="box-body">
      <p class="text-info">Restauration de la base de donn&eacute;es usagers provenant de Cybanim seulement</p>
	<p class="text-red">Avant de commencer l'importation assurez-vous que :</p>
	<p>* Le fichier ait �t� export� en Windows-Latin1 ou UTF-8<br>* Le s�parateur de colonne choisi est bien le <b>";"</b>.
	<br>Pour �tre sur, ouvrez le fichier avec votre tableur (excel ou calc), quand il vous demande la conversion, essayez ';' ou tabulation, et pour l'encodage des carat�res choisissez Windows-Latin1 ou UTF-8, 
	si les caract�res ne s'affichent pas correctement, essayez Latin-1 ou celui qui convient. 
	Puis suivez la proc�dure suivante :</p>
	<ol><li>Enregistrez le fichier au format excel ou calc</li>
	<li>V�rifiez que tous les champs correspondent aux en-t�tes de colonnes (que la date d'inscription ne soit pas remplie avec du texte par exemple)</li>
	<li><span class="text-red">Remplacez les ent�tes de colonne suivantes : <b>"date d'inscription" par "inscription"</b> (sans les "), et <b>"Espace d'origine" par "Espace"</b>  (sans les "); Assurez-vous ne pas avoir de double quote ('') � la place des simple (') pour les apostrophes.</span> Car il semblerait que le traitement de l'apostrophe n'est pas le m�me suivant les diff�rents exports que j'ai pu traiter depuis cybanim (!!).</li>
	<li>Enregistrez sous le fichier au format CSV, dans la boite de dialogue suivante : </li>
	<li>Choisissez bien Windows-Latin1 ou UTF-8 pour l'encodage des caract�res puis le ";" pour la s�paration des colonnes, <span class="text-red">important ! veuillez d�cocher toutes les autres options propos�es, ne pas choisir la tabulation, car elle fausse les donn�es !</span></li>
	<li>Enregistrez ce nouveau fichier.</li>
	<li>Vous pourrez maintenant lancer l'importation dans CyberGestionnaire, ci-dessous.</li></ol>
	
	<p class="text-red">N'oubliez pas de choisir une option pour g�n�rer un nouveau mot de passe, puis la ville par d�faut au cas o� aucune n'aurait �t� renseign�e, ainsi que le tarif par d�faut que vous souhaitez appliquer � vos adh�rents.</p>
	<p>La date de renouvellement du compte usager est par d�faut import�e au m�me mois et m�me jour que la date d'inscription ajust�e � l'ann�e en cours, sauf si l'adh�rent est inactif.</p>

</div></div>

</div>

<!-- Restauration, cette fonction permet de restaur� la bdd usagers cybanim -->
<div class="col-lg-6">
  
<!-- liste des espaces existants-->
 <div class="box box-warning"><div class="box-header"><h3 class="box-title">Restauration Cybanim</h3></div>
 <div class="box-body">
	
	<?php
			if (FALSE == isset($total_usagers))
			{	
				?>
       <form method="post" action="index.php?a=49&act=restore" enctype="multipart/form-data" role="form">
       	
		<div class="form-group"><label> Fichier de sauvegarde au format CSV</label><input type="file" name="restore_file" ></div>
         <br>
		<div class="form-group"><label>Tarif consultation accord� aux usagers automatiquement</label>
		<select name="temps_user" class="form-control" >
		<?php
			foreach ($tariftemps AS $key=>$value)
			{
				if ($temps == $key)
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
		
		<!--<input type="text" name="temps_user" class="form-control"/></div>-->
		<div class="form-group">
			<label>Choisir la ville par d�faut (quand aucune n'a �t� renseign�e)  <small class="badge bg-blue" data-toggle="tooltip" title="Pensez a cr�er une ville 'Non renseign�e' "><i class="fa fa-info"></i></small>
			</label><select name="ville" class="form-control" >
			<?php
			// recupere les villes
				$city =  getAllCityname();
				
				foreach ($city AS $key=>$value)
			{
				if ($ville == $key)
				{
					echo "<option value=\"".$key."\" selected>".$value."</option>";
				}
				else
				{
					echo "<option value=\"".$key."\">".$value."</option>";
				}
			}
			?></select></div>
		<div class="form-group"><label>Tarif par d�faut qui sera appliqu� � l'ensemble des usagers</label>
		
		<?php
		//recuperer les tarifs
		$tarifs=getTarifsbyCat(2); ?>
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
	<div class="form-group"><label>G�n�ration du nouveau mot de passe*</label>
		 <div class="radio"> <label><input type="radio" name="mdp" value="1"   checked>&nbsp; nom.prenom<label></div>
		 <div class="radio"> <label><input type="radio" name="mdp" value="2"  >&nbsp; AAAAnom (� partir de la date de naissance)<label></div>
		  <div class="radio"> <label><input type="radio" name="mdp" value="3" >&nbsp; AAAAMMDD  (� partir de la date de naissance)<label></div>
	</div>
        		<?php
			}
			?>
	
	</div><!-- /body-->
	<div class="box-footer">
		 <?php if(isset($total_usagers)==FALSE){ echo '<div class="input-group"><input align="absbottom" type="submit" value="Restaurer les donn&eacute;es" class="btn bg-red"></div> ';     } ?>
       </form>
       
 <?php
	if (FALSE != isset($total_usagers))
	{
		?>
		<h4 class="box-title">Resultat de l'importation</h4>
		<p><?php echo $total_echec; ?>  adh�rents non-inscrit(s) suite � un �chec � l'importation dans la bdd, sur <?php echo $total_usagers; ?> usagers.<br>
		<?php echo $total_actif; ?> usagers actif sur <?php echo $total_usagers; ?> usagers.<br>
		<?php echo $total_inactif; ?> usagers inactif sur <?php echo $total_usagers; ?> usagers.</p>
		<p>Vous pouvez consulter le log d'importation pour voir les erreurs dans le fichier situ� dans le dossier /log, et r�cup�rer les usagers qui n'ont pu �tre import�s.</p>
        <?php
	}
?>
	</div>
	</div><!-- /box-->

 </div><!-- /col-->
