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
 template Copyright &copy; 2011 Website Admin Theme by <a href="http://www.medialoot.com">MediaLoot</a>

 include/menu.php V0.1
*/

// menu niveau 1
?>
   
 <section class="sidebar"><!-- Sidebar user panel -->
    <div class="user-panel">
      
	<?php
	
	$rowa = getAvatar($_SESSION["iduser"]);
	$avatar=$rowa["anim_avatar"];
	?>
             <div class="pull-left image"> <img src="img/avatar/<?php echo $avatar; ?>" class="img-circle" alt="User Image" />  </div>            
         <div class="pull-left info"><p>Salut, <?php echo $row["prenom_user"]; ?></p>
				<a href="#"><i class="fa fa-circle text-success"></i> Connecté</a>
          </div>
     </div>
 
 <!-- module de recherche rapide -->
	 <?php if ($_SESSION['status']==3 OR $_SESSION['status']==4){ ?>
	 <form  method="post" action="index.php?a=1" class="sidebar-form">
		  <div class="input-group">
			<input type="text" name="term" value="Recherche Adhérent" onfocus="if(!this._haschanged){this.value=''};this._haschanged=true;" class="form-control">
			 <span class="input-group-btn"> <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i></button>
			</span></div>
		</form>
		
		
	 <?php } ?>
	<!-- /.search form -->
	
	
	 <?php include ("include/calendrier.php")?>
    
	  
	
	  
<?php
switch($_SESSION['status'])
{
    case 1:
		$statut ="Actif"; //Utilisateur standart
		?>
		<ul class="sidebar-menu">
        	<li class="active treeview">
			<a href="index.php"><i class="fa fa-home"></i> <span>Tableau de bord</span></a></li>
		
			<li><a href="index.php?m=2"><i class="fa fa-edit" ></i><span>Mon compte</span></a></li>
			<li><a href="index.php?m=5"><i class="fa fa-bookmark-o" ></i><span>Mes liens favoris</span></a></li>
			<li><a href="index.php?m=6"><i class="fa fa-keyboard-o" ></i><span>Mes formations</span></a></li>
			<li><a href="index.php?m=8"><i class="fa fa-calendar"></i><span>Mes reservations</span></a></li>
			<!--<li><a href="index.php?m=20"><span>Mes impressions</span></a></li>-->
		</ul>
		
        <?php
    break;	
	case 3:
        $statut ="Animateur";
        ?>
	<ul class="sidebar-menu">
		<li class="active treeview"><a href="index.php"><i class="fa fa-home"></i><span>Accueil</span></a></li>
		<?php 
		$consolemode=getConfigConsole($_SESSION["idepn"]);
		if ($consolemode==1){
			echo '<li><a href="index.php?a=45"><i class="fa fa-dashboard"></i><span>Console</span></a></li>';
		}
		?>
	
		<li class="treeview">
			<a href="#"><i class="fa fa-group"></i> <span>Les adhérents</span>
                         <i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu">
			<li><a href="index.php?a=1"><i class="fa fa-angle-double-right"></i>Liste des Adh&eacute;rents</a></li>
			<li ><a href="index.php?a=1&b=1"><i class="fa fa-angle-double-right"></i>Cr&eacute;er Adh&eacute;rent</a></li>
			<li ><a href="index.php?a=24"><i class="fa fa-angle-double-right"></i>Pr&eacute;inscriptions</a></li>
			<li ><a href="index.php?a=23"><i class="fa fa-angle-double-right"></i>Gestion des Animateurs</a></li>
		</ul></li>
		
		<li class="treeview">
			<a href="#"><i class="fa fa-print"></i> <span>Les transactions</span>
				<i class="fa fa-angle-left pull-right"></i></a>
				<ul class="treeview-menu">
			<li ><a href="index.php?a=21"><i class="fa fa-angle-double-right"></i>Impressions</a></li>
			<li ><a href="index.php?a=8"><i class="fa fa-angle-double-right"></i>Abonnements</a></li>
		</ul></li>
		
		<li class="treeview">
			<a href="#"><i class="fa fa-keyboard-o"></i> <span>Ateliers</span>
                         <i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu">
			<li ><a href="index.php?a=11"><i class="fa fa-angle-double-right"></i>Programmation en cours</a></li>
			<li ><a href="index.php?a=12"><i class="fa fa-angle-double-right"></i>Planifier un atelier</a></li>
			<li ><a href="index.php?a=15"><i class="fa fa-angle-double-right"></i>Cr&eacute;er un sujet</a></li>
			<li ><a href="index.php?a=17"><i class="fa fa-angle-double-right"></i>Modifier un sujet</a></li>
			<li ><a href="index.php?a=22"><i class="fa fa-angle-double-right"></i>Archives</a></li>
			
			<!-- <li class="icn_mail"><a href="index.php?a=33"> Gestion des courriers</a></li>-->
			</ul></li>
		
		<li class="treeview">
			<a href="#"><i class="fa fa-table"></i> <span>Sessions</span>
                         <i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu">
			<li ><a href="index.php?a=37"><i class="fa fa-angle-double-right"></i>Sessions en cours</a></li>
			<li ><a href="index.php?a=31&m=1"><i class="fa fa-angle-double-right"></i>Planifier une session</a></li>
			<li ><a href="index.php?a=34"><i class="fa fa-angle-double-right"></i>Cr&eacute;er un sujet</a></li>
			<li ><a href="index.php?a=35"><i class="fa fa-angle-double-right"></i>Modifier un sujet</a></li>
			<li ><a href="index.php?a=36"><i class="fa fa-angle-double-right"></i>Archives</a></li>
			
			</ul></li>
		
		<!--
		<li class="treeview">
			<a href="#"><i class="fa fa-bar-chart-o"></i> <span>Statistiques</span>
                         <i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu">
			<li ><a href="index.php?a=5&b=1"><i class="fa fa-angle-double-right"></i>Adh&eacute;rents</a></li>
			<li ><a href="index.php?a=5&b=2" disabled><i class="fa fa-angle-double-right"></i>R&eacute;servations</a></li>
			<li ><a href="index.php?a=5&b=3" disabled><i class="fa fa-angle-double-right"></i>Impressions</a></li>
			<li ><a href="index.php?a=5&b=5" disabled><i class="fa fa-angle-double-right"></i>Sessions</a></li>
			<li><a href="index.php?a=5&b=4" disabled><i class="fa fa-angle-double-right"></i>Ateliers</a></li>
		</ul></li>
		-->
			
		<li class="treeview">
			<a href="#"><i class="fa fa-gears"></i> <span>Gestion de l'espace</span>
                         <i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu">
			<li ><a href="index.php?a=7"><i class="fa fa-angle-double-right"></i>Paramètres ateliers</a></li>
			<li ><a href="index.php?a=3"><i class="fa fa-angle-double-right"></i>Interventions</a></li>
			<li ><a href="index.php?a=4"><i class="fa fa-angle-double-right"></i>Br&egrave;ves</a></li>
			<li ><a href="index.php?a=10"><i class="fa fa-angle-double-right"></i>Liens</a></li>
		</ul></li>
      </ul> 
        <?php
    break;
	
    case 4:
        $statut ="Administrateur";
        ?>
	<ul class="sidebar-menu">
		<li class="active"><a href="index.php"><i class="fa fa-home"></i><span>Accueil</span></a></li>
		<?php 
		$consolemode=getConfigConsole($_SESSION["idepn"]);
		if ($consolemode==1){
			echo '<li><a href="index.php?a=45"><i class="fa fa-dashboard"></i><span>Console</span></a></li>';
		}
		?>
			<li><a href="index.php?a=41"><i class="fa fa-gears"></i> <span>Configuration</span> </a></li>
		
		<li class="treeview"><a href="#"><i class="fa fa-group"></i> <span>Adhérents</span><i class="fa fa-angle-left pull-right"></i></a>
		    <ul class="treeview-menu">
				<li><a href="index.php?a=1"><i class="fa fa-angle-double-right"></i>Liste des Adh&eacute;rents</a></li>
				<li ><a href="index.php?a=1&b=1"><i class="fa fa-angle-double-right"></i>Cr&eacute;er Adh&eacute;rent</a></li>
				<li ><a href="index.php?a=24"><i class="fa fa-angle-double-right"></i>Pr&eacute;inscriptions</a></li>
				<li ><a href="index.php?a=23"><i class="fa fa-angle-double-right"></i>Gestion des Admin/Anim</a></li>
		</ul></li>
		
		<li class="treeview"><a href="#"><i class="fa fa-money"></i><span>Transactions</span><i class="fa fa-angle-left pull-right"></i></a>
			<ul class="treeview-menu">
				<li><a href="index.php?a=21"><i class="fa fa-angle-double-right"></i>Impressions</a></li>
				<li><a href="index.php?a=8"><i class="fa fa-angle-double-right"></i>Abonnements</a></li>
		</ul></li>
		
		<li class="treeview"><a href="#"><i class="fa fa-keyboard-o"></i> <span>Ateliers</span><i class="fa fa-angle-left pull-right"></i></a>
			<ul class="treeview-menu">
				<li ><a href="index.php?a=11"><i class="fa fa-angle-double-right"></i>Programmation en cours</a></li>
				<li ><a href="index.php?a=12"><i class="fa fa-angle-double-right"></i>Planifier un atelier</a></li>
				<li ><a href="index.php?a=15"><i class="fa fa-angle-double-right"></i>Cr&eacute;er un atelier</a></li>
				<li ><a href="index.php?a=17"><i class="fa fa-angle-double-right"></i>Modifier un atelier</a></li>
				<li ><a href="index.php?a=22"><i class="fa fa-angle-double-right"></i>Archives</a></li>
				
			<!-- <li class="icn_mail"><a href="index.php?a=33"> Gestion des courriers</a></li>-->
			</ul></li>
		
		<li class="treeview"><a href="#"><i class="fa fa-ticket"></i> <span>Sessions</span><i class="fa fa-angle-left pull-right"></i></a>
			<ul class="treeview-menu">
				<li ><a href="index.php?a=37"><i class="fa fa-angle-double-right"></i>Sessions en cours</a></li>
				<li ><a href="index.php?a=31&m=1"><i class="fa fa-angle-double-right"></i>Planifier une session</a></li>
				<li ><a href="index.php?a=34"><i class="fa fa-angle-double-right"></i>Cr&eacute;er une session</a></li>
				<li ><a href="index.php?a=35"><i class="fa fa-angle-double-right"></i>Modifier une session</a></li>
				<li ><a href="index.php?a=36"><i class="fa fa-angle-double-right"></i>Archives</a></li>
			</ul></li>
		
		
		<li class="treeview"><a href="#"><i class="fa fa-bar-chart-o"></i> <span>Statistiques</span><i class="fa fa-angle-left pull-right"></i></a>
			<ul class="treeview-menu">
				<li ><a href="index.php?a=5&b=1"><i class="fa fa-angle-double-right"></i>Adh&eacute;rents</a></li>
				<li ><a href="index.php?a=5&b=2" disabled><i class="fa fa-angle-double-right"></i>R&eacute;servations</a></li>
				<li ><a href="index.php?a=5&b=3" disabled><i class="fa fa-angle-double-right"></i>Impressions</a></li>
				<li ><a href="index.php?a=5&b=5" disabled><i class="fa fa-angle-double-right"></i>Sessions</a></li>
				<li><a href="index.php?a=5&b=4" disabled><i class="fa fa-angle-double-right"></i>Ateliers</a></li>
		</ul></li>
		
			
		<li class="treeview"><a href="#"><i class="fa fa-gear"></i> <span>Gestion de l'espace</span><i class="fa fa-angle-left pull-right"></i></a>
			<ul class="treeview-menu">
			<li ><a href="index.php?a=7"><i class="fa fa-angle-double-right"></i>Paramètres ateliers</a></li>
				<li ><a href="index.php?a=3"><i class="fa fa-angle-double-right"></i>Interventions</a></li>
				<li ><a href="index.php?a=4"><i class="fa fa-angle-double-right"></i>Br&egrave;ves</a></li>
				<li ><a href="index.php?a=10"><i class="fa fa-angle-double-right"></i>Liens</a></li>
		</ul></li>
		
	
	
	</ul>
		
		<?php
    break;
    
}
?>
	
	
		<li ><span class="pull-right"><a href="index.php?a=60" ><small>Cr&eacute;dits&nbsp;&nbsp;&nbsp;--&nbsp;&nbsp;&nbsp;</small></a></span></li>
		<li ><span class="pull-right"><a href="#"><small>CyberGestionnaire V.<?php echo getVersion($_SESSION["idepn"]); ?>&nbsp;&nbsp;&nbsp;--&nbsp;&nbsp;&nbsp;</small></a></span></li>
	
	
	</section>

	  
