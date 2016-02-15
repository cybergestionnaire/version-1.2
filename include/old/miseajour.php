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
 Formulaire de mise à jour de version, détection et modifications dans la base de donnees
*/

 //declencher les MAJ, verifier la version dans la tab_config
 $version=getConfigVersion($_SESSION["idepn"]);
 $versionactuelle="0.9";
 
 ///Mise à jour des statuts adhérents
 if(isset($_POST["submit"])){
	 $updateA=updateUserStatutMAJ();
	//ajout d'un log 
	if($updateA==TRUE){ //maj type 1 == update tab_user
		$logadh=addLog(date('Y-m-d H:i'),"adh",$updateA,'Mise à jour des adhesions adherents du jour');
		header("Location: ./index.php?mesno=14");
	}
 }
 
 
 
?>
<div class="row"><div class="col-md-6">
 <div class="box box-danger"><div class="box-header"> 
	<i class="fa fa-warning"></i><h3 class="box-title">Mise à jour de version depuis la version <?php echo $version; ?> vers la <?php echo $versionactuelle; ?></h3></div>
	<div class="box-body">
	
	<p class="text-blue">Enregistrement de votre base actuelle</p>
	<p>Avant de modifier irrémédiablement vos données , elles vont etre automatiquement sauvegardées dans un fichier zippé :
	
	<?php
	
	$log=createTabLogMAJ();
	echo '<p>* Ajout de la table des logs </p>';
	
	//Sauvegarde de la base actuelle en fichier zippe
	if($log==TRUE){
		$bdd=backupbdd();
		
		$error="";
	}else{
		echo 'Impossible de faire la mise à jour, veuillez vérifier que votre base est accessible et ouverte en écriture !';
		$error="mise à jour impossible, base sql inaccessible" ;
	}
	
if($bdd==TRUE){
	//lancer le reste des modifs
	
	echo'<p class="text-blue">Vérification de la base</p>	';
	
	//0. verification et modification des champs dans la base 
	//tab_config

	$row1=getTab_config();
	echo "* activation de la console=".$row1[0]." <br> 
	* Ajout de l'inscription automatique = ".$row1[1]." <br> 
	* Ajout du message inscription= ".$row1[2]."
	";
	if ($row1[0]=="FALSE"){ $error.= "Echec la tab_config n'existe pas"." \r\n"; }
	if ($row1[1]=="ECHEC"){ $error.= "Echec impossible d'ajouter `inscription_usagers_auto`"." \r\n"; }
	if ($row1[2]=="ECHEC"){ $error.= "Echec impossible d'ajouter `message_inscription`"." \r\n"; }
	
	
	$row2=getTab_sessionuserMAJ();
	echo "<br>* Ajout du champs session dans la relation=".$row2[0]."
		";
	if ($row2[0]=="ECHEC"){ $error.= "Echec impossible d'ajouter `id_datesession`"." \r\n"; }
	
	$row3=createDatessessionMAJ();
	echo "<br> * Creation de la table des dates session=".$row3[1]." ";
	if ($row3[1]=="ECHEC"){ $error.= "Echec impossible d'ajouter la table des dates de session"." \r\n"; }
	
	$row4=changeUserForfaitMAJ();
	echo "<br> * Modification de la gestion des forfaits=".$row4[0]."/".$row4[1]." ";
	if ($row4[0]=="ECHEC"){ $error.= "Echec impossible de modifier la table `rel_user_forfait`"." \r\n"; }
	if ($row4[1]=="ECHEC"){ $error.= "Echec impossible de vider la table `rel_user_forfait`"." \r\n"; }
	
	$row4b=changeTabTransacMAJ();
	echo "<br> * Modification de la table des transactions=".$row4b;
	if ($row4b=="ECHEC"){ $error.= "Echec impossible d'ajouter le champs `type_transac`"." \r\n"; }
	
	$row5=tabComputerMAJ();
	echo "<br> * Modification de la table des ordinateurs=".$row5." ";
	if ($row5=="FALSE"){ $error.= "Echec impossible de modifier `fonction_computer`"." \r\n"; }
	
	//tab_user
	$row6=modiftabUserMAJ();
	echo "<br> * Modification de la table user=".$row6." ";
	if ($row6=="ECHEC"){ $error.= "Echec impossible de modifier la table user"." \r\n"; }
	
	$row7=veriftabUserMAJ();
	echo "<br> * Vérification de la table user=".$row7."</p> ";
	if ($row7=="FALSE"){ $error.= "Echec impossible de verifier le champs epnuser de la table user"." \r\n"; }
	////-------------------------------------verifie a partir d-ici

	echo '<p class="text-blue">Modification de la base Ateliers et Sessions pour ajustement</p>';
	
	// 1.ajouter les dates des sessions dans la tab_session_dates
	$row8=getDatesSessionMAJ();
	$nd=mysqli_num_rows($row8);
	//mouliner les données à envoyer dans la nouvelle table
	for($i=0;$i<$nd;$i++){
		$arraysession=mysqli_fetch_array($row8);
		$session=$arraysession["id_session"];
		$listdate=explode(",",$arraysession["session_dates"]);
		$nbre_dates=$arraysession["nbre_dates_sessions"];
		$heure=str_replace("h", ":", $arraysession["heure_session"]);
		$statut=$arraysession["status_session"];
		//envoyer les lignes de dates
		for($s=0;$s<$nbre_dates;$s++){
			$datex=	$listdate[$s]." ".$heure;	
			$row8x=insertDateSessions($session,$datex,$statut);
			}
	}
	echo '<p> * Dates des sessions transformées </p>';
	if ($row8x==FALSE){ $error.= "Echec impossible d'ajouter des dates à la table session_dates"." \r\n"; }

	// 2. Modifier les id de la rel_session correspondants: mettre les id_datesession et dupliquer

	$row9=getRelUsersessionMAJ();
	$nbu=mysqli_num_rows($row9);

	for($i=0;$i<$nbu;$i++){
		$arrayuser=mysqli_fetch_array($row9);
		$nbda=$arrayuser['nbre_dates_sessions'];
		$id=$arrayuser['id_rel_session'];
		$user=$arrayuser['id_user'];
		$statut=$arrayuser['status_rel_session'];
		//retrouver l'array des dates par session
		$datesses=getDatesMAJ($arrayuser['id_session']);
		//modifier l'id source
		modifRelMAJ($id,$datesses[0]);
		for($y=1;$y<$nbda;$y++){ //dupliquer avec les id
			$row8y= insertRelUserMAJ($user,$arrayuser['id_session'],$datesses[$y],$statut);
			}
	}
	if ($row8y==FALSE){ $error.= "Echec impossible d'ajouter les relations user à la table rel_session"." \r\n"; }
	// 3. modifier le statut pour les dates cloturées
	$rowpresence=getStatsessionMAJ();
	$nbp=mysqli_num_rows($rowpresence);
		
	for($z=0;$z<$nbp;$z++){
		$presencesession=mysqli_fetch_array($rowpresence);
		$presents=explode(";", $presencesession['ids_presents' ]);
		$presents=array_filter($presents);//enlever les données vides
		//debug($presencesession);
	for($d=0;$d<count($presents);$d++){
			if(FALSE==updateRelUserStatut($presents[$d],$presencesession['id_datesession'])){
			 $row9="ECHEC";
			 }else{
			 $row9="OK";
			 }
		}
		if(FALSE==updateStatutdatesessionMAJ($presencesession['id_datesession'])){
			$row10="ECHEC";
		}else{
			 $row10="OK";
			 }
		
	}
	if ($row9=="ECHEC"){ $error.= "Echec impossible de modifier les relations user à la table rel_session"." \r\n"; }
	if ($row10=="ECHEC"){ $error.= "Echec impossible de modifier les statuts des dates des sessions"." \r\n"; }
	
	echo '<p> * Présences des adhérents aux sessions modifiées ='.$row9.'</p>
			<p>* Statut des dates des session modifiées = '.$row10.' </p>';
		
	//4.modifier les statuts des ateliers tab_atelier et rel_user_atelier
	$rowpresents=getPresentsMAJ();
	$nbp=mysqli_num_rows($rowpresents);
	for($n=0;$n<$nbp;$n++){
		$presents=mysqli_fetch_array($rowpresents);
		$ids=explode(';',$presents['ids_presents']);
		$ids=array_filter($ids);
		$idatelier=$presents['id_atelier'];
		for ($j=0;$j<count($ids);$j++){
			$row10x=updatestatutAMAJ($ids[$j],$idatelier);
		}
	
	}
	echo '<p> *Mise à jour des présences aux ateliers effectuées</p>';
	if ($row10x==FALSE){ $error.= "Echec impossible de modifier les statuts des rel_atelier_user"." \r\n"; }
	
	//5. modifier les transactions forfait 
	//vider la table des rel_transac
	$row11=viderRelAtelierTransacMAJ();
	//rajouter le type de transaction en fonction du tarif
	$categorieTarif=array(
		1=>"Imp",
		2=>"Adh",
		3=>"Con",
		4=>"Div",
		5=>"For"
	);
	
	for($t=1;$t<6;$t++){
		$row11b=alterTransactionMAJ($t,$categorieTarif[$t]);
	}
	echo "<p>* Mise à jour de la table des transactions ".$row11b."</p>";
	if ($row11b=="FALSE"){ $error.= "Echec impossible de modifier les transactions, les types de categories"." \r\n"; }
	
	//retrouver les données à inserer
	$rowtransac=getTransactionsMAJ();
	$nbtransac=mysqli_num_rows($rowtransac);
	if($nbtransac>0){
		$reste=0;
		$iduser="";
		for($h=0;$h<$nbtransac;$h++){
			$result=mysqli_fetch_array($rowtransac);
			$idtransac=$result['id_transac'];
			$nombre=$result['nb_atelier_forfait']*$result['nbr_forfait'];
			$user=$result['id_user'];	
			$nbatelier=getnbASUserEncoursMAJ($user); //retrouver les ateliers valide pour mettre le bon montant dépensé
		//debug($nbatelier);	
		$depense=$nbatelier;
			
		if(($tt>0) AND($iduser==$user)){
				
				if($reste<$nombre){
					$statut=1;
					$depense=$reste;
					$iduser="";
					$tt=0;
				}elseif($reste>=$nombre){
					$tt++;
					$iduser=$user;
					$reste=$reste-$nombre;
					$depense=$nombre;
					$statut=2;
				}
			
			}else{
			if($depense<$nombre){
					$statut=1;
					$depense=$depense;
				}elseif ($depense>=$nombre){
					$tt=1;				
					$reste=$nbatelier-$nombre;
					$depense=$nombre;
					$statut=2;
					$iduser=$user;
					
					
				}
			}
		
		//insérer les données
			$row12= insertRelTransacMAJ($user,$idtransac,$nombre,$depense,$statut);
		}
	}else{ //si aucune transaction n'a été enregistrée
	$row12=FALSE;
	}
	if($row12==TRUE){ echo '<p> * Modification des forfaits effectuée </p>';}else{ echo "<p>* Aucune transaction a modifier</p>";}
	
	if ($row12==FALSE){ $error.= "Echec impossible de modifier les transactions"." \r\n"; }
	
	//6. Suppression des tables de stats inutiles, creation de la table de stat mutualisée
	//changer le type du statut dans la base passer en integer
	$statutAtelierchange=changeStatutAMAJ();

	//modifier le statut des ateliers en programmation et en cours
	$rowatelierencours= getAtelierPrograMAJ(1);
	$nbpa=mysqli_num_rows($rowatelierencours);
	if($nbpa>0){
		for($k=0;$k<$nbpa;$k++){
			$rtt=mysqli_fetch_array($rowatelierencours);
			updateValidAtelierMAJ($rtt['id_atelier'],0);
		}
	}

	$rowatelierprogra= getAtelierPrograMAJ(2);
	$nbpa=mysqli_num_rows($rowatelierprogra);
	if($nbpa>0){
		for($k=0;$k<$nbpa;$k++){
			$rtt=mysqli_fetch_array($rowatelierprogra);
			updateValidAtelierMAJ($rtt['id_atelier'],1);
		}
	}

	$rowctab=createtabStatMAJ();
	if ($rowctab==FALSE){ $error.= "Echec impossible de creer la nouvelle table des stats"." \r\n"; }
	//inserer les stats, retrouver les paramètres depuis tables stat ateliers VALIDES et ARCHIVES
	$statatelier=getStatAtelierMAJ();
		$nbastat=mysqli_num_rows($statatelier);
	if($nbastat>0){
		for($z=0;$z<$nbastat;$z++){
		$rowstatA=mysqli_fetch_array($statatelier);
		$attente=getUserAttenteMAJ($rowstatA['id_atelier']);
		$absents=$rowstatA['nombre_inscrits']-$rowstatA['nombre_presents'];
		$datea=$rowstatA['date_atelier']." ".str_replace("h", ":", $rowstatA['heure_atelier']);
		$row13=InsertStatMAJ("a",$rowstatA['id_atelier'],$datea,$rowstatA['nombre_inscrits'],$rowstatA['nombre_presents'],$absents,$attente,$rowstatA['nbplace_atelier'],1);
		updateValidAtelierMAJ($rowstatA['id_atelier'],2);
		}
	}else{
		$row13=FALSE;
	}
	//cas des ateliers ANNULES : statut=3, précédente version statut=3
	$ateliersannulesarray=getAteliersAnnule();
	$nbannule=mysqli_num_rows($ateliersannulesarray);
	if($nbannule>0){
		for($g=0;$g<$nbannule;$g++){
			$rowannule=mysqli_fetch_array($ateliersannulesarray);
			$attente=getUserAttenteMAJ($rowannule['id_atelier']);
			$inscrits=getUserAtelierInscritsMAJ($rowannule['id_atelier']);
			$datea=$rowannule['date_atelier']." ".str_replace("h", ":", $rowannule['heure_atelier']);
			$row13b=InsertStatMAJ("a",$rowannule['id_atelier'],$datea,$inscrits,0,0,$attente,$rowannule['nbplace_atelier'],3);
			//$validID=updateValidAtelierMAJ($rowannule['id_atelier'],3);
		}
	}else{
		$row13b=FALSE;
	}
	if ($row13==FALSE){ $error.= "Echec de la modification des stats ou aucune stat a modifier"." \r\n"; }
	if ($row13b==FALSE){ $error.= "Echec de la modification des stats  ou aucune stat a modifier"." \r\n"; }
	
	///Les stats des sessions

	$statsession=getStatSessionmodifMAJ();
	$nbsstat=mysqli_num_rows($statsession);
	
	for($z=0;$z<$nbsstat;$z++){
	$rowstatS=mysqli_fetch_array($statsession);
	$attente=getUserAttenteSMAJ($rowstatS['id_datesession']);
	$absents=$rowstatS['nbplace_session']-$rowstatS['nombre_presents'];
	
	$row14=InsertStatMAJ("s",$rowstatS['id_session'],$rowstatS['date_session'],$rowstatS['nombre_inscrits'],$rowstatS['nombre_presents'],$absents,$attente,$rowstatS['nbplace_session'],1);
	}

	
	if($row13==TRUE){ echo ' <p>* Modification des tables statistiques atelier</p> ';}else{ echo ' <p>Echec de la modification des stats ou aucune stat a modifier</p>';}
	if($row14==TRUE){ echo '  <p>* Modification des tables statistiques session</p> ';}else{ echo ' <p>Echec de la modification des stats  ou aucune stat a modifier</p>';}
	
	
	if ($row14==FALSE){ $error.= "Echec de la modification des stats  ou aucune stat a modifier"." \r\n"; }
	//suppression des anciennes tables de stat
	$rowstat=suppressTabStatMAJ();
	if ($rowstat==FALSE){ $error.= "Echec impossible de supprimer les tables de stats d'origine"." \r\n"; }
	
	//supprimer les champs en trop dans tab_session
	$row15=suppressTabSession2MAJ();
	if ($row15==FALSE){ $error.= "Echec impossible de supprimer champs de la table tab_session"." \r\n"; }
	//X. Finalisation creation des logs et fichiers connexes ?
	
	if($log==TRUE){
		$version=modifNumMAJ($versionactuelle);
	}
	echo '<p>* Modification du numero de version </p>';
	if($version==TRUE){	
		$finale=InsertLogMAJ('maj',$versionactuelle,date('Y-m-d H:i'),"Mise à jour de version 0.9 effectuee");
	}
	echo '<p>* Votre mise à jour est terminée ! </p>';
	//debug($finale);
	
	//fichier de log
	if ($finale==FALSE){ $error.= "Echec impossible d'ajouter à la table des logs"." \r\n"; }
	//inscrire l'ensemble des erreurs dans le fichier log de la version 
	if($error!=""){
		gFilelog($error,"log_majv08.txt");
	}

	
}	

?>
	
	
	</div>
</div>
</div>
<?php 
if($finale==TRUE){
?>

<div class="col-md-6">
 <div class="box box-danger"><div class="box-header"> 
	<i class="fa fa-warning"></i><h3 class="box-title">Mise à jour de la base Adhérent</h3></div>
	<div class="box-body">
	<p class="text-blue">
	<b>N.B : A la prochaine ouverture de session sur le CyberGestionnaire par un animateur ou un administrateur,  le statut des adhérents sera automatiquement mis à jour en fonction de la date de renouvellement :</b>
	les adhérents dont l'adhésion est en cours reste "actif", les adhérents dont le renouvellement est passé du jour deviennent "inactifs".
	</p>
	<p>
	La date de renouvellement de vos adhérents n'a pas encore été mise à jour, souhaitez-vous changer les statuts des adhérents maintenant ?<br>
	</p>
	
	<form method="post" action="index.php?a=61" role="form">
	<input type="submit" value="Renouveler" name="submit" class="btn btn-success">
	</form>
	
	</div>
	<div class="box-footer">
		<a href="index.php"><button class="btn btn-default"> <i class="fa fa-arrow-circle-left"></i> Retour à l'accueil</button></a></div>
</div>
</div>
<?php
}
?>
</div>






