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
 2012 florence DAUVERGNE

 include/user_accueil.php V0.1
*/

// Page d'accueil sur le compte animateur ou administrateur

// admin --- Utilisateur
include ("post_reservation-rapide.php");
include("fonction_stat.php");
$term   = $_POST["term"];
$mesno  = $_GET["mesno"];

//Tous les utilisateurs, inscription de la connexion dans la tab_connexion(user,date,type=1=login,MACADRESS,Navigateur, System)
$exploitation=operating_system_detection();
$ua=getBrowser();
$navig=$ua['name'] . " " . $ua['version'] ;
$macadress="inconnue pour l\'instant";
$cx=enterConnexionstatus($_SESSION['iduser'],date('Y-m-d H:i:s'),1,$macadress,$navig,$exploitation);



 //***** -------------fonctions pour administrateur & animateurs   
if ($_SESSION["status"]=="3" OR $_SESSION["status"]=="4")
{
//liste des ateliers/evenements de la journee
 $listeWeekAtelier=getWeekAteliers(date('Y-m-d'),$_SESSION["idepn"]);
  $nbTA=mysqli_num_rows($listeWeekAtelier);

 // verifier les abonnements des adherent et mettre a jour le statut actif
 $majadh=getLogUser('adh');
 if (mysqli_num_rows($majadh)==0){
	$listAdhinactifs=getAdhInactif(date('Y-m-d'));
	$updateA=updateUserStatut(); // les usagers dont la date de renouvellement est du jour.
	//ajout d'un log 
	if($updateA<>FALSE){ //maj type 1 == update tab_user
		$logadh=addLog(date('Y-m-d H:i'),"adh",'1','Mise � jour des adhesions adherents du jour');
		}
	
  }
  $espaces = getAllepn(); 

if ($mesno !="")
{
  echo getError($mesno);
  
}

?>

<div class="row">

<?php
//*****   Mises � jour des adh�rents dont le forfait arrive a expiration ///
 
if ($logadh==TRUE){ echo '<div class="col-md-4"> <div class="alert alert-success alert-dismissable"><i class="fa fa-check-square"></i>
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Mise � jour des adh�rents effectu�e. '.$updateA.' adh�rents inactifs !</div></div>';}

//***** Fonctions administrateur ONLY MAJ + Backup *****///
if($_SESSION["status"]=="4"){
 $version=getConfigVersion($_SESSION["idepn"]);
$newversion=1.2;
if($version<>$newversion){ ?>
 <!--DIV Mises � jour -->
  <div class="col-md-4"><div class="box box-danger"><div class="box-header"> <i class="fa fa-warning"></i><h3 class="box-title">Mise � jour de version</h3></div>
	<div class="box-body">
	Une nouvelle version demande bien quelques efforts, cliquez sur le bouton ci-dessous pour faire la mise � jour imm�diatement !
	</div>
	 <div class="box-footer"><a href="index.php?a=61"><input type="submit" name="mises � jour" value="Faire la mise � jour" class="btn btn-danger"></a>
			</div>
 </div></div>
 <!-- / MAJ -->
 <?php 
  }
 
 //*** Backup base automatique 1 fois par mois pour les admins qui se connectent! ***///
if(TRUE==getLogBackup()){
			 
	?>
	<div class="col-md-4"><div class="box box-danger"><div class="box-header"> <i class="fa fa-warning"></i><h3 class="box-title">Sauvegarde de la base de donn�e</h3></div>
	<div class="box-body">
		Cela fait un mois que la base de donn�e n'a pas �t� sauvegard�e, cliquez sur le bouton pour la lancer !
	</div>
	 <div class="box-footer"><a href="index.php?a=62&maj=0"><input type="submit" name="sauvegarde" value="Lancer la sauvegarde" class="btn btn-danger"></a>
			</div>
 </div></div>
	
	<?php 
	}
 } //***** FIN Fonctions administrateur MAJ + Backup *****///
 
 
  //debug($_session["idepn"]);
  ?>
 </div>
 
 <!-- Info boxes Statistiques-->
          <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bg-aqua" style="padding-top:18px"><i class="ion ion-ios-time"></i></span>
                <div class="info-box-content">
								<?php 
								$rowresastatmois=getStatResa(date('m'),date('Y'),$_SESSION["idepn"]);
								$resamois=$rowresastatmois["nb"];
								
								$datehier=date('Y-m')."-".(date('d')-1);
								$rowresahier=getStatResaByDay($datehier,$_SESSION["idepn"]);
								
								$resahier=$rowresahier["nb"]." (".getTime($rowresahier["duree"]).")";
								
								?>
                  <span class="info-box-text">R�servations</span>
                  <span class="info-box-number"><?php echo $resahier; ?><small> hier</small><br><?php echo $resamois; ?><small> ce mois</small></span>
									
                </div><!-- /.info-box-content -->
              </div><!-- /.info-box -->
            </div><!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bg-red" style="padding-top:18px"><i class="fa ion-university"></i></span>
                <div class="info-box-content">
								<?php 
								$rownbateliersstat=getStatAtelierByMonth(date('Y'),date('m'),1,1);
								$nbateliersstat=$rownbateliersstat["atelier"];
								$nbsessionstat=getSessionbyMonth(date('Y'),date('m'));
								?>
                  <span class="info-box-text">Ateliers programm�s<br>(ce mois)</span>
                  <span class="info-box-number"><?php echo $nbateliersstat; ?> <small>Ateliers</small>  /<?php echo $nbsessionstat; ?> <small>Sessions</small></span>
                </div><!-- /.info-box-content -->
              </div><!-- /.info-box -->
            </div><!-- /.col -->

            <!-- fix for small devices only -->
            <div class="clearfix visible-sm-block"></div>

            <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bg-green" style="padding-top:18px"><i class="ion ion-printer"></i></span>
                <div class="info-box-content">
								<?php
								$rowstatimpression=getStatPages(date('m'),date('Y'),$_SESSION["idepn"]);
								$pages=$rowstatimpression["pages"];
								$montant=$rowstatimpression["montant"];
								?>
                  <span class="info-box-text">Impressions</span>
                  <span class="info-box-number"><?php echo $pages; ?> pages (<?php echo $montant; ?> &euro;)</span>
                </div><!-- /.info-box-content -->
              </div><!-- /.info-box -->
            </div><!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bg-yellow" style="padding-top:18px"><i class="ion ion-ios-people"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Nouveaux membres<br>(Mois en cours)</span>
                  <span class="info-box-number"><?php echo getNewMemberNum(); ?></span>
                </div><!-- /.info-box-content -->
              </div><!-- /.info-box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
 
 
 
<div class="row">
<div class="col-md-6"> <!-- colonne 1-->
	 <!-- DIV TIMELINE evenements de la semaine -->
<div class="box"><div class="box-header"><h3 class="box-title">Au programme cette semaine � l'EPN</h3></div>
		<div class="box-body">
			 <!-- The time line -->
			
				<?php
			if($nbTA>0){	
				  for ($g=1; $g<=$nbTA; $g++){
					$rowTA=mysqli_fetch_array($listeWeekAtelier);
					if($rowTA["tab_origine"]=="tab_atelier"){
						$idatelier=$rowTA["id"];
						$rowAtelier=getAtelier($idatelier);
							$result=getSujetById($rowAtelier["id_sujet"]);
							$rowsujet=mysqli_fetch_array($result);
						$titre=$rowsujet["label_atelier"];
						$heureAS=$rowAtelier["heure_atelier"];
						$dateAS=$rowAtelier["date_atelier"];
						$anim=getUserName($rowAtelier["anim_atelier"]);
						$inscrits=countPlace($rowAtelier["id_atelier"]);
						$salle=mysqli_fetch_array(getSalle($rowAtelier["salle_atelier"]));
						$nomsalle=$salle["nom_salle"]." (".$espaces[$salle["id_espace"]].")";
						$duree=$rowAtelier["duree_atelier"];
						$urlAS="index.php?a=13&b=1&idatelier=".$idatelier;
					
					}elseif($rowTA["tab_origine"]=="tab_session_dates"){
						$idsession=$rowTA["id"];
						$rowSession=getSession($idsession);
						
							$titrearr=getTitreSession($rowSession["nom_session"]);
							$titre=$titrearr["session_titre"];
						$temp=strtotime($rowTA["dateAS"]);
						$heureAS=date('H:i',$temp);
						$dateAS=$rowTA["dateAS"];
						$anim=getUserName($rowSession["id_anim"]);
						$inscrits=countPlaceSession($rowSession["id_session"],0);
						$salle=mysqli_fetch_array(getSalle($rowSession["id_salle"]));
							$nomsalle=$salle["nom_salle"];
						$duree="60";
						$urlAS="index.php?a=30&b=1&idsession=".$idsession;
					
					}
				?>
							<!-- timeline time label -->
				<ul class="timeline"><li class="time-label"><span class="bg-red">&nbsp;<?php echo getDayFr($dateAS); ?>&nbsp;&nbsp;<i class="fa fa-clock-o"></i>&nbsp;<?php echo $heureAS; ?></span></li><!-- /.timeline-label -->
					<li><i class="fa fa-keyboard-o bg-green"></i>
						<div class="timeline-item"><h3 class="timeline-header"><?php echo $titre; ?></h3>
							<div class="timeline-body">
							<small class="badge bg-purple"><?php echo $inscrits; ?></small>&nbsp;&nbsp;Participants enregistr�s<br>
							<small class="badge bg-purple"><i class="fa fa-map-marker"></i></small>&nbsp;&nbsp;<?php echo $nomsalle ; ?><br>
							<small class="badge bg-purple"><i class="fa fa-hourglass"></i></small>&nbsp;&nbsp;<?php echo $duree; ?> min<br>
							<small class="badge bg-purple"><i class="fa fa-user"></i></small>&nbsp;&nbsp;<?php echo $anim; ?>
							</div>
							<div class='timeline-footer'>
								<a href="<?php echo $urlAS; ?>" class="btn btn-success btn-xs">Voir le d�tail</a>
							</div>
						</div>
					</li>
					
					<?php } ?>
											
				<li><i class="fa fa-clock-o"></i></li>
			</ul>
		<?php } else{ echo "<p>aucun &eacute;v&eacute;nement enregistr&eacute; pour cette semaine !</p>"; } ?>
		</div>
	</div>
	

</div><!-- /colonne 1 -->
	

<div class="col-md-6"> <!-- colonne 2-->

  
<!-- message board -->
 <div class="box box-primary direct-chat direct-chat-primary"><div class="box-header with-border"><i class="fa fa-comments-o"></i><h3 class="box-title">Messages adh&eacute;rents</h3>
	 <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
      </div>
	</div>
   <div class="box-body"> <div class="direct-chat-messages">
       <!-- chat item -->
           
		<?php
		
		
		if($_SESSION["status"]==4){
			$listeMessage=readMessage(); //tous les messages pour l'admin
		}else if ($_SESSION["status"]==3){
			$listeMessage=readMyMessage($_SESSION["iduser"]); //messages pour les anims
		}
		$nb=mysqli_num_rows($listeMessage);
		$urlRedirect="index.php";
		
		for ($i=1; $i<=$nb; $i++){
			$rowmessage = mysqli_fetch_array($listeMessage) ;
			$auteur=$rowmessage["mes_auteur"];
			$rowdest=getUser($rowmessage["mes_destinataire"]);
			$rowauteur=getUser($rowmessage["mes_auteur"]);
			$nomessage=$rowauteur['prenom_user']." ".$rowauteur['nom_user'];
			
			if($auteur==$_SESSION["iduser"]){
				$classchat1="direct-chat-msg right";
				$classchat2='direct-chat-name pull-right';
				$classchat3='direct-chat-timestamp pull-left';
				$rowa = getAvatar($_SESSION["iduser"]);
				$photoavatar="img/avatar/".$rowa["anim_avatar"];
			}else{
			//reponse � droite
				$classchat1="direct-chat-msg";
				$classchat2='direct-chat-name pull-left';
				$classchat3='direct-chat-timestamp pull-right';
				$filenamephoto = "img/photos_profil/".trim($rowauteur["nom_user"])."_".trim($rowauteur["prenom_user"]).".jpg" ;
				if (file_exists($filenamephoto)) {
					$photoavatar=$filenamephoto;
				}else{
					if($rowauteur["sexe_user"]=='M'){
					$photoavatar="img/avatar/male.png";
					}else{
					$photoavatar="img/avatar/female.png";
					}
				}
				
			}
			
			$datemes=date_format(date_create($rowmessage['mes_date']), '\l\e d/m/y \� G:i ');
			
			
				?>
		
		<div class="<?php echo $classchat1; ?>">
        <div class='direct-chat-info clearfix'>
          <span class='<?php echo $classchat2; ?>'><?php echo $nomessage ;?></span>
					<span class='<?php echo $classchat3; ?>'><?php echo $datemes;?> pour <?php echo $rowdest["nom_user"]." ".$rowdest["prenom_user"]; ?> </span></div>
		   
		<img src="<?php echo $photoavatar; ?>"  class="direct-chat-img" />
			  <div class="direct-chat-text"><?php echo stripslashes($rowmessage['mes_txt']);?></div></div>
			  
	<?php 
	
	}	?>      
       
        </div><!-- /.chat -->
				 </div> 
        <div class="box-footer"><form method="post" action="<?php echo $urlRedirect; ?>">
		<div class="input-group"><label>R&eacute;pondre A :</label>
				 <select name="chatdestinataire" class="form-control pull-right">
	    <?php
		if($_SESSION["status"]==3){
		$listeAdhreponse=getListReponse($_SESSION["iduser"]);
		}else{
		$listeAdhreponse=getListRepAdmin();
		}
		foreach ($listeAdhreponse AS $key=>$value)
		{
		    if ($adhreponse == $key)
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
            <div class="input-group">
               <input value="test" type="hidden" name="tags_message">
							<input value="<?php echo date('Y-m-d H:i:s'); ?>" type="hidden" name="chatdate">
							<input value="<?php echo $_SESSION['iduser']; ?>" type="hidden" name="chatadh">	
							<input type="text" name="chattxt_message" class="form-control" onfocus="if(!this._haschanged){this.value=''};this._haschanged=true;"/>	
                <div class="input-group-btn"><button class="btn btn-primary btn-flat" type="submit" value="message_submit" name="message_submit"><i class="fa fa-paper-plane"></i>&nbsp;</button></div>
            </div><!-- /.input group -->  </form>
        </div><!-- /.box footer -->  
    
</div><!-- /.box (chat box) -->

<!-- RACCOURCIS RAPIDES -->
<div class="box"><div class="box-header"><h3 class="box-title">Raccourcis</h3></div>
		<div class="box-body">
			<a class="btn btn-app" href="index.php?a=1"><i class="fa fa-group"></i>Adh�rents</a>
			<a class="btn btn-app" href="index.php?a=1&b=1"><i class="fa fa-user"></i>+ Adh�rent</a>
			<a class="btn btn-app" href="index.php?a=11"><i class="fa fa-keyboard-o"></i>Ateliers</a>
			<a class="btn btn-app" href="index.php?a=37"><i class="fa fa-ticket"></i>Sessions</a>
			<a class="btn btn-app" href="index.php?a=21"><i class="fa fa-print"></i>Impressions</a>
			<?php
			if ($_SESSION["status"]==4){
			echo '<a class="btn btn-app" href="index.php?a=41"><i class="fa fa-gear"></i>Configuration</a>';
			}
			?>
		</div>
	</div>


</div><!-- /colonne 3-->


</div><!-- /row -->

  


<!-- LES BREVES --->

<div class="row"> 
<div class="col-md-8">


<?php

//affichage breve admin anim
$result = getAllBreve(0);  
if ($result == FALSE)
	{
  echo getError(0);
	}else
	{
	  $nb = mysqli_num_rows($result);
	  if ($nb==0)
		{
		  //echo getError(10);
		}
	  else
		{
		  for ($i=1;$i<=$nb;$i++)
		  {
			  $row = mysqli_fetch_array($result);
			  echo "<div class=\"box box-success\"><div class=\"box-header\"><h3 class=\"box-title\">".$row["titre_news"]."</h3></div>
						<div class=\"box-body\">
						<p>".$row["comment_news"]."</p>
						</div></div>
					";
		  }
		}
	}
?>
</div></div>
<?php
// *********************Page d'accueil sur le compte utilisateur
//if ligne 37 !
} else {

//si inscrit -->vos prochains ateliers
// bouton faire une reservation de postes
// bouton consulter mon historique de resa
// bouton consulter mon historique des ateliers
// bouton envoyer message � animateur

//charger la liste des evenements du reseau
$listeWeekAtelier=getWeekAteliers(date('Y-m-d'),0);
  $nbe=mysqli_num_rows($listeWeekAtelier);
$arrayinscrip=array(
	2=>"Sur la liste d'attente",
	0=>"Je suis d�j� inscrit"
);

 //****UTILISATEUR ACTIF
 
if($_SESSION["status"]=="1"){
	
?>
<div class="row">
<div class="col-md-4"> <!-- colonne 1-->
	 <!-- DIV TIMELINE evenements de la semaine -->
<div class="box"><div class="box-header"><h3 class="box-title">Au programme cette semaine � l'EPN</h3></div>
		<div class="box-body">
			<?php
			if($nbe>0){	
				  for ($g=1; $g<=$nbe; $g++){
					$rowTA=mysqli_fetch_array($listeWeekAtelier);
					if($rowTA["tab_origine"]=="tab_atelier"){
						$idatelier=$rowTA["id"];
						$rowAtelier=getAtelier($idatelier);
						$result=getSujetById($rowAtelier["id_sujet"]);
						$rowsujet=mysqli_fetch_array($result);
						$titre=$rowsujet["label_atelier"];
						$heureAS=$rowAtelier["heure_atelier"];
						$dateAS=$rowAtelier["date_atelier"];
						$anim=getUserName($rowAtelier["anim_atelier"]);
						$inscrits=countPlace($rowAtelier["id_atelier"]);
						$salle=mysqli_fetch_array(getSalle($rowAtelier["salle_atelier"]));
						$nomsalle=$salle["nom_salle"]." (".$espaces[$salle["id_espace"]].")";
						$nomespace=mysqli_fetch_array(getEspace($rowTA["id_espace"]));
						$duree=$rowAtelier["duree_atelier"];
						$testinscription=getTestInscript($_SESSION["iduser"],$idatelier,"a");
					
						
						if($testinscription=="FALSE"){
						$urlAS="index.php?m=6&b=1&idatelier=".$idatelier;
							$boutoninscr="s'inscrire";
							$couleurb="btn btn-success btn-xs";
						}else{
							
							$urlAS="";
							$boutoninscr=$arrayinscrip[$testinscription["statut"]];
							$couleurb="btn btn-warning btn-xs";
						}
						
					
					}elseif($rowTA["tab_origine"]=="tab_session_dates"){
						$idsession=$rowTA["id"];
						$rowSession=getSession($idsession);
						$titrearr=getTitreSession($rowSession["nom_session"]);
						$titre=$titrearr["session_titre"];
						$temp=strtotime($rowTA["dateAS"]);
						$heureAS=date('H:i',$temp);
						$dateAS=$rowTA["dateAS"];
						$anim=getUserName($rowSession["id_anim"]);
						$inscrits=countPlaceSession($rowSession["id_session"],0);
						$salle=mysqli_fetch_array(getSalle($rowSession["id_salle"]));
							//$nomsalle=$salle["nom_salle"];
						$nomespace=mysqli_fetch_array(getEspace($rowTA["id_espace"]));
						$duree="60";
						$testinscription=getTestInscript($_SESSION["iduser"],$idsession,"s");
											
						if($testinscription=="FALSE"){
							
							$urlAS="index.php?m=6&b=1&idsession=".$idsession;
							$boutoninscr="s'inscrire";
							$couleurb="btn btn-success btn-xs";
						}else{
							$urlAS="#";
							$boutoninscr=$arrayinscrip[$testinscription["statut"]];
							$couleurb="btn btn-warning btn-xs";
						}
					
					}
				//	debug($testinscription);
				
				?>
							<!-- timeline time label -->
				<ul class="timeline"><li class="time-label"><span class="bg-red"> <?php echo getDayFr($dateAS); ?> � <?php echo $heureAS; ?></span></li><!-- /.timeline-label -->
					<li><i class="fa fa-keyboard-o bg-green"></i>
						<div class="timeline-item"><h3 class="timeline-header"><?php echo $titre; ?></h3>
							
						<div class="timeline-body">
							<small class="badge bg-purple"><?php echo $inscrits; ?></small> participants enregistr�s<br>
							<small class="badge bg-purple"><i class="fa fa-map-marker"></i></small> <?php echo $salle["nom_salle"] ; ?> (<?php echo $nomespace["nom_espace"];?>)<br>
							<small class="badge bg-purple"><i class="fa fa-clock-o"></i></small> <?php echo $duree; ?> min<br>
							<small class="badge bg-purple"><i class="fa fa-user"></i></small> Anim&eacute; par  <?php echo $anim; ?>
							</div>
						<div class='timeline-footer'><a href="<?php echo $urlAS; ?>" class="<?php echo $couleurb;?>"><?php echo $boutoninscr; ?></a></div>
						</div>
					</li>
					
					<?php } ?>
											
				<li><i class="fa fa-clock-o"></i></li>
			</ul>
		<?php } else{ echo "<p>aucun &eacute;v&eacute;nement enregistr&eacute; pour cette semaine !</p>"; } ?>
		</div>
	</div>
	

</div><!-- /colonne 1 -->
<div class="col-md-4"> <!-- colonne 2-->
<?php
$result = getAllBreve(1);  
if ($result == FALSE)
	{
  echo getError(0);
	}else
	{
	  $nb = mysqli_num_rows($result);
	  if ($nb==0)
		{
		  echo getError(10);
		}
	  else
		{
		  for ($i=1;$i<=$nb;$i++)
		  {
			  $row = mysqli_fetch_array($result);
			  echo "<div class=\"box box-success\"><div class=\"box-header\"><h3 class=\"box-title\">".$row["titre_news"]."</h3></div>
						<div class=\"box-body\">
						<p>".$row["comment_news"]."</p>
						</div>
				</div>";
		  }
		}
	}
	?>
	
	
	
	<div class="box box-warning"><div class="box-header"><h3 class="box-title">Acc&eacute;der &agrave; vos historiques</h3></div>
	<div class="box-body">
	<!--	<a class="btn btn-block btn-social bg-green" href="index.php?m=6"><i class="fa fa-graduation-cap"></i> Voir ma participation aux ateliers</a>-->
		<a class="btn btn-block btn-social btn-tumblr" href="index.php?m=20"><i class="fa fa-print"></i> Voir mes impressions</a>
		<a class="btn btn-block btn-social btn-foursquare" href="index.php?m=8"><i class="fa fa-clock-o"></i> Voir mes r&eacute;servations</a>
  </div>
  
  </div>
  </div><!-- /col -->
  <div class="col-md-4"> <!-- colonne 3-->
   <div class="box box-primary direct-chat direct-chat-primary"><div class="box-header with-border"><i class="fa fa-comments-o"></i><h3 class="box-title">Messages aux animateurs</h3>
	 <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
      </div>
	</div>
   <div class="box-body"> <div class="direct-chat-messages">
       <!-- chat item -->
           
		<?php
		
		$listeAnim=getAllAnim();
		$listeMessage=readMyMessage($_SESSION["iduser"]);
		$nb=mysqli_num_rows($listeMessage);
		$urlRedirect="index.php";
		
		for ($i=0; $i<$nb; $i++){
			$rowmessage = mysqli_fetch_array($listeMessage) ;
			$auteur=$rowmessage["mes_auteur"];
			$rowdest=getUser($rowmessage["mes_destinataire"]);
			$rowauteur=getUser($rowmessage["mes_auteur"]);
			$nomessage=$rowauteur['prenom_user']." ".$rowauteur['nom_user'];
			
			if($auteur==$_SESSION["iduser"]){
				$classchat1="direct-chat-msg right";
				$classchat2='direct-chat-name pull-right';
				$classchat3='direct-chat-timestamp pull-left';
				$filenamephoto = "img/photos_profil/".trim($rowauteur["nom_user"])."_".trim($rowauteur["prenom_user"]).".jpg" ;
				if (file_exists($filenamephoto)) {
					$photoavatar=$filenamephoto;
				}else{
					if($rowauteur["sexe_user"]=='M'){
					$photoavatar="img/avatar/male.png";
					}else{
					$photoavatar="img/avatar/female.png";
					}
				}
				
			}else{
			//reponse � droite
				$classchat1="direct-chat-msg";
				$classchat2='direct-chat-name pull-left';
				$classchat3='direct-chat-timestamp pull-right';
				$rowa = getAvatar($auteur);
				$photoavatar="img/avatar/".$rowa["anim_avatar"];
				
			}
			
			$datemes=date_format(date_create($rowmessage['mes_date']), '\l\e d/m/y \� G:i ');
			
			
			
			
				?>
		
		 <div class="<?php echo $classchat1; ?>">
        <div class='direct-chat-info clearfix'>
          <span class='<?php echo $classchat2; ?>'><?php echo $nomessage ;?></span>
					<span class='<?php echo $classchat3; ?>'><?php echo $datemes;?> pour <?php echo $rowdest["nom_user"]." ".$rowdest["prenom_user"]; ?></span></div>
		   
		<img src="<?php echo $photoavatar; ?>"  class="direct-chat-img" />
			  <div class="direct-chat-text"><?php echo stripslashes($rowmessage['mes_txt']);?></div></div>
	<?php }	
	?>      
       
        </div><!-- /.chat -->
				 </div> 
        <div class="box-footer"><form method="post" action="<?php echo $urlRedirect; ?>">
		<div class="input-group"><label>A :
				 <select name="chatdestinataire" class="form-control">
	    <?php
		foreach ($listeAnim AS $key=>$value)
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
    </select></label></div>
            <div class="input-group">
               <input value=" " type="hidden" name="tags_message">
							<input value="<?php echo date('Y-m-d H:i:s'); ?>" type="hidden" name="chatdate">
							<input value="<?php echo $_SESSION['iduser']; ?>" type="hidden" name="chatadh">	
							<input type="text" name="chattxt_message" class="form-control" onfocus="if(!this._haschanged){this.value=''};this._haschanged=true;"/>	
                <div class="input-group-btn"><button class="btn btn-primary btn-flat" type="submit" value="message_submit" name="message_submit"><i class="fa fa-paper-plane"></i>&nbsp;</button></div>
            </div><!-- /.input group -->  </form>
        </div><!-- /.box footer -->  
    
</div><!-- /.box (chat box) -->
	</div>
	
</div>	
<?php	
	}else if ($_SESSION["status"]=="2"){ //***UTILISATEUR INACTIF
		?>
		
		<div class="row">
            <div class="col-md-6">
              <div class="box box-default">
                <div class="box-header with-border">
                  <i class="fa fa-exclamation-triangle"></i>
                  <h3 class="box-title">Attention</h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                  
                  <div class="callout callout-warning">
                    <h4>Votre compte est d&eacute;sactiv&eacute; !</h4>
                    <p>Votre adh&eacute;sion n'est probablement plus &agrave; jour, veuillez vous rapprocher d'un animateur pour la renouveller.</p>
                  </div>
                  
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- /.col -->
   </div>
		
		<?php
	}



}
?>



