<?php
///FICHER DE FONCTIONS POUR LES MISES A JOURS
function backupbdd()
{
 include ("./connect_db.php");
new BackupMySQL(array(
	'username' => $userdb,
	'passwd' => $passdb,
	'dbname' => $database
	));
	
$sql="INSERT INTO `tab_logs`(`id_log`, `log_type`, `log_date`, `log_MAJ`, `log_valid`, `log_comment`) 
	VALUES ('','bac' ,NOW(), '0.8','1', 'Backup integral de la base effectue') ";
	
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
 
 if(FALSE==$result){ 
	return FALSE;
	}else{
		return TRUE;
	}
}

function getTab_config()
{
 include ("./connect_db.php");
 //debug($database);
//modification d'un champs
$sql="SELECT COLUMN_NAME as col FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".$database."' AND TABLE_NAME = 'tab_config' ";
//ajout des champs supplementaires
$sql1="ALTER TABLE `tab_config` ADD `inscription_usagers_auto` enum('0','1') NOT NULL";
$sql2="ALTER TABLE `tab_config` ADD  `message_inscription` text NOT NULL";

$db=opendb();
$result = mysqli_query($db,$sql);
$result1 = mysqli_query($db,$sql1);
$result2 = mysqli_query($db,$sql2);
 closedb($db);
 
 if(FALSE==$result){ 
	$rowr[0]="FALSE"; 
 }else{ 
	while($row = mysqli_fetch_array($result)){
            $output[] = $row['col'];                
        }
		//debug($output);
	if(in_array("activer_console",$output)){
		 $rowr[0]="OK"; 
		 }else{
		 $ss="alter table tab_config CHANGE ".$output[1]." activer_console int(11)  not null default 1";
		 $db=opendb();
		 $result = mysqli_query($db,$ss);
		closedb($db);
	}
	 
	 $rowr[0]="OK"; 
 }
 
   if(FALSE==$result1){$rowr[1]="ECHEC"; }else{ $rowr[1]="OK"; }
   if(FALSE==$result2){$rowr[2]="ECHEC"; }else{ $rowr[2]="OK"; }
return $rowr;
}

function getTab_sessionuserMAJ()
{

$sql="ALTER TABLE `rel_session_user` ADD `id_datesession` INT NOT NULL AFTER `id_session`";

$db=opendb();
$result = mysqli_query($db,$sql);

closedb($db);
if(FALSE==$result){	$row[0]="ECHEC"; }else{ $row[0]="OK"; }

return $row;
}

function createDatessessionMAJ()
{
$sql = "DROP TABLE IF EXISTS `tab_session_dates`";
$sql1= "CREATE TABLE `tab_session_dates` (
`id_datesession` int(11) NOT NULL AUTO_INCREMENT,
  `id_session` int(11) NOT NULL,
`date_session` datetime NOT NULL,
  `statut_datesession` int(11) NOT NULL,
  PRIMARY KEY (`id_datesession`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;
";
$db=opendb();
$result = mysqli_query($db,$sql);
$result1 = mysqli_query($db,$sql1);
closedb($db);
if(FALSE==$result){	$row[0]="ECHEC"; }else{ $row[0]="OK"; }
if(FALSE==$result1){$row[1]="ECHEC"; }else{ $row[1]="OK"; }
return $row;
}

function changeUserForfaitMAJ()
{
$sql="ALTER TABLE  `rel_user_forfait` CHANGE  `id_atelier`  `total_atelier` INT( 11 ) NOT NULL, CHANGE  `id_session`  `depense` INT( 11 ) NOT NULL, CHANGE `id_tarif` `id_transac` INT( 11 ) NOT NULL";
$sql1="TRUNCATE TABLE `rel_user_forfait` ";

$db=opendb();
$result = mysqli_query($db,$sql);
$result1 = mysqli_query($db,$sql1);

 closedb($db);
 if(FALSE==$result){$row[0]="ECHEC"; }else{ $row[0]="OK"; }
if(FALSE==$result1){$row[1]="ECHEC"; }else{ $row[1]="OK"; }

return $row;
}

//modification de la table des transactions
function changeTabTransacMAJ()
{
$sql="ALTER TABLE `tab_transactions` ADD `type_transac` VARCHAR(20) NOT NULL AFTER `id_transac`";	
$db=opendb();
$result = mysqli_query($db,$sql);
 closedb($db);
 if(FALSE==$result){$row="ECHEC"; }else{ $row="OK"; }
return $row;	
	
}


function tabComputerMAJ()
{
include ("./connect_db.php");
//modification d'un champs
$sql="SELECT COLUMN_NAME as col FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".$database."' AND TABLE_NAME = 'tab_computer'";
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
 
 if(FALSE==$result){ 
 $row="FALSE"; 
 }else{ 
	while($row = mysqli_fetch_array($result)){
            $output[] = $row['col'];   }
	if(in_array("fonction_computer",$output)==TRUE){
		$sql1="ALTER TABLE `tab_computer` CHANGE `fonction_computer` `fonction_computer` VARCHAR( 50 ) NOT NULL DEFAULT '0'";
		 }else{
		 //ajout des champs supplementaires
		$sql1="ALTER TABLE `tab_computer` ADD `fonction_computer`  VARCHAR( 50 ) NOT NULL DEFAULT '0' AFTER `usage_computer`";
		}
	  $db=opendb();
	$result = mysqli_query($db,$sql1);
	closedb($db);
	 $row="OK"; 
 }

 return $row;
}


function modiftabUserMAJ()
{
$sql="ALTER TABLE `tab_user` CHANGE `login_user` `login_user` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '', 
CHANGE `pass_user` `pass_user` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
CHANGE `tel_user` `tel_user` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
CHANGE `mail_user` `mail_user` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
CHANGE `equipement_user` `equipement_user` VARCHAR(60) CHARACTER SET latin1 COLLATE latin1_general_ci
";
$db=opendb();
$result = mysqli_query($db,$sql);
 closedb($db);
 if(FALSE==$result){ return $row="ECHEC"; }else{ return $row="OK"; }

}

function veriftabUserMAJ()
{
include ("./connect_db.php");
//modification d'un champs
$sql="SELECT COLUMN_NAME as col FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".$database."' AND TABLE_NAME = 'tab_user'";
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
if(FALSE==$result){ 
 $row="FALSE"; 
 }else{ 
	while($row = mysqli_fetch_array($result)){
            $output[] = $row['col'];   }
	
	if(in_array('epn_user',$output)==TRUE){
		 $row="OK"; 
	}else{
		if(in_array('id_epn',$output)==TRUE){
			$sql1="ALTER TABLE `tab_user` CHANGE  `id_epn` `epn_user` INT(11) NOT NULL";
		}else{
			$sql1="ALTER TABLE `tab_user` ADD `epn_user` INT NOT NULL AFTER `dateRen_user`";
		}
		 $db=opendb();
		 $result1 = mysqli_query($db,$sql1);
		closedb($db);
		$row="OK";
	}
	} 
	return $row; 
	
}

///MODIFICATION DES SESSIONS
function getDatesSessionMAJ()
{
// recupere le champs des dates dans tab_session
$sql="SELECT `id_session`,`session_dates`,`nbre_dates_sessions`,`heure_session`, status_session FROM `tab_session`";
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
if(FALSE==$result){
	return FALSE;
}else{
	return $result;
}

}

//retrouver les inscrits
function getRelUsersessionMAJ()
{
$sql="SELECT `id_rel_session` , `rel_session_user`.`id_session` , `id_user` , `status_rel_session` , nbre_dates_sessions
	FROM `rel_session_user` , tab_session
	WHERE `rel_session_user`.`id_session` = tab_session.`id_session`";
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
	if(FALSE==$result){
		return FALSE;
	}else{
		return $result;
	}
}

function getDatesMAJ($id)
{
$sql="SELECT `id_datesession` FROM `tab_session_dates` WHERE `id_session`=".$id;
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
if(FALSE==$result){
	return FALSE;
}else{
	while($row = mysqli_fetch_array($result)){
            $output[] = $row['id_datesession'];   }
	return $output;
}
}

function modifRelMAJ($id,$date)
{
$sql="UPDATE `rel_session_user` SET `id_datesession`=".$date." WHERE `id_rel_session`=".$id;
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
if(FALSE==$result){
	return FALSE;
}else{
	return TRUE;
}
}

function insertRelUserMAJ($user,$idsession,$iddate,$statut)
{
$sql="INSERT INTO `rel_session_user`(`id_rel_session`, `id_session`, `id_datesession`, `id_user`, `status_rel_session`) 
VALUES ('','".$idsession."','".$iddate."','".$user."','".$statut."')
";
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
if(FALSE==$result){
	return FALSE;
}else{
	return TRUE;
}

}

function getStatsessionMAJ()
{
$sql="SELECT `ids_presents` , id_datesession
FROM `tab_session_stat` , tab_session_dates
WHERE `tab_session_stat`.`date_session` = DATE( tab_session_dates.date_session )
AND `tab_session_stat`.`id_session` = tab_session_dates.`id_session` ";
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
if(FALSE==$result){
	return FALSE;
}else{

	return $result;
}



}

function updateRelUserStatut($user,$iddate)
{
$sql="UPDATE `rel_session_user` SET `status_rel_session`=1 WHERE `id_user`=".$user." AND `id_datesession`=".$iddate ;
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
if(FALSE==$result){
	return FALSE;
}else{
	return TRUE;
}
}

//**
function updateAtelierStatutMAJ()
{
$sql="";
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
if(FALSE==$result){
	return FALSE;
}else{
	return TRUE;
}

}
//***
function updateStatutdatesessionMAJ($iddate)
{
$sql="UPDATE `tab_session_dates` SET `statut_datesession`=1 WHERE `id_datesession`=".$iddate." ";
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
	if(FALSE==$result){
		return FALSE;
	}else{
		return TRUE;
	}
}

function createTabLogMAJ()
{
$sql = "DROP TABLE IF EXISTS `tab_logs`";
$sql1="CREATE TABLE `tab_logs` (
`id_log` int(11) NOT NULL auto_increment,
 `log_type` varchar(20) COLLATE latin1_general_ci NOT NULL,
`log_date` datetime,
  `log_MAJ` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `log_valid` int(11) NOT NULL,
  `log_comment` text COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id_log`)
) ENGINE=MyISAM ;";
$db=opendb();
$result = mysqli_query($db,$sql);
$result1 = mysqli_query($db,$sql1);
closedb($db);
	if(FALSE==$result1){
		return FALSE;
	}else{
		return TRUE;
	}

}

function modifNumMAJ($value)
{
$sql="UPDATE `tab_config` SET`name_config`=".$value;

$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
	if(FALSE==$result){
		return FALSE;
	}else{
		return TRUE;
	}

}

function InsertLogMAJ($type,$version,$date,$comment)
{
$sql="INSERT INTO `tab_logs`(`id_log`, `log_type`, `log_date`, `log_MAJ`, `log_valid`, `log_comment`) 
VALUES ('', '".$type."','".$date."','".$version."','1','". $comment."') ";

$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
	if(FALSE==$result){
		return FALSE;
	}else{
		return TRUE;
	}

}


//getnbASUserEncours  calcule le nombre d'atelier et de session dont l'inscription est  validée
function getnbASUserEncoursMAJ($iduser)
{
$sql="SELECT count(`id_rel_atelier_user`)as atelier FROM `rel_atelier_user` WHERE `status_rel_atelier_user`=1 AND `id_user`=".$iduser;
$sql2="SELECT count(`id_rel_session`) as session FROM `rel_session_user` WHERE `status_rel_session`=1 AND `id_user`=".$iduser."  ";

$db=opendb();
 $result = mysqli_query($db,$sql);
 $result2 = mysqli_query($db,$sql2);
 closedb($db);
	  if ((FALSE == $result) AND (FALSE==$result2))
	  {
		  return FALSE ;
	  }
	  else
	  {
		$row= mysqli_fetch_array($result);
		$row2= mysqli_fetch_array($result2);
		$nbre=$row["atelier"]+$row2["session"];
		return $nbre;
	  }

}


//modification des transactions
function viderRelAtelierTransacMAJ()
{
$sql="DROP TABLE IF EXISTS `rel_user_forfait` ";
$sql1="CREATE TABLE `rel_user_forfait` (
`id_forfait` int(11) NOT NULL auto_increment,
  `id_user` int(11) NOT NULL,
  `id_transac` int(11) NOT NULL,
  `total_atelier` int(11) NOT NULL,
  `depense` int(11) NOT NULL,
  `statut_forfait` int(11) NOT NULL,
  PRIMARY KEY (`id_forfait`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci 
";
$db=opendb();
$result = mysqli_query($db,$sql);
$result1 = mysqli_query($db,$sql1);

closedb($db);
	if(FALSE==$result1){
		return FALSE;
	}else{
		return TRUE;
	}
}

function alterTransactionMAJ($t,$cat)
{
$sql="UPDATE `tab_transactions`, tab_tarifs  SET `type_transac`= '".$cat."' 
WHERE  tab_transactions.id_tarif= tab_tarifs.id_tarif
AND  tab_tarifs.`categorie_tarif`=".$t."
";	
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
	if(FALSE==$result){
		return "FALSE";
	}else{
		return "TRUE";
	}

}

//insérer lese transactions nouvelles
function insertRelTransacMAJ($iduser,$idtransac,$nombre,$depense,$statut)
{
$sql="INSERT INTO `rel_user_forfait`(`id_forfait`, `id_user`, `id_transac`, `total_atelier`, `depense`, `statut_forfait`) 
VALUES ('','".$iduser."','".$idtransac."','".$nombre."','".$depense."','".$statut."')
";
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
	if(FALSE==$result){
		return FALSE;
	}else{
		return TRUE;
	}

}


//recuperer les données des transactions
function getTransactionsMAJ()
{
$sql="SELECT `id_transac` , `id_user` , `nbr_forfait` , `nb_atelier_forfait`
FROM `tab_transactions` , tab_tarifs
WHERE `tab_transactions`.`id_tarif` = tab_tarifs.`id_tarif` 
AND `type_transac` = 'for'
ORDER BY `id_user` ASC";
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
	if(FALSE==$result){
		return FALSE;
	}else{
		return $result;
	}

}
//chnger le type du champs statut atelier
function changeStatutAMAJ()
{
$sql="ALTER TABLE `tab_atelier` CHANGE `statut_atelier` `statut_atelier` int(11) NOT NULL ";
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
	if(FALSE==$result){
		return FALSE;
	}else{
		return TRUE;
	}

}



///modifier les ateliers et présences dans la tab rel
function getPresentsMAJ()
{
$sql="SELECT `id_atelier`,`ids_presents` FROM `tab_atelier_stat`";
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
	if(FALSE==$result){
		return FALSE;
	}else{
		return $result;
	}
}

function updatestatutAMAJ($iduser,$idatelier)
{
$sql="UPDATE `rel_atelier_user` SET `status_rel_atelier_user`=1 WHERE `id_user`=".$iduser." AND `id_atelier`=".$idatelier." AND `status_rel_atelier_user`<2";
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
	if(FALSE==$result){
		return FALSE;
	}else{
		return TRUE;
	}
}

//retourne la liste des atleiers validés
function getAteliervalidIDMAJ()
{
$sql="SELECT id_atelier FROM tab_atelier_stat";
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
	if(FALSE==$result){
		return FALSE;
	}else{
		return $result;
	}


}
//cloturer les ateliers validés par les stats
/*0=> "En cours",
	1=> "En programmation",
	2=> "Cloturé",
	3=> "Annulé"
*/
function updateValidAtelierMAJ($id,$statut)
{
$sql="UPDATE `tab_atelier` SET `statut_atelier`=".$statut." WHERE `id_atelier`=".$id;
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
	if(FALSE==$result){
		return FALSE;
	}else{
		return TRUE;
	}

}


function suppressTabStatMAJ()
{
$sql="DROP TABLE IF EXISTS `tab_atelier_stat`";
$sql1="DROP TABLE IF EXISTS `tab_session_stat`";
$db=opendb();
$result = mysqli_query($db,$sql);
$result1 = mysqli_query($db,$sql1);
closedb($db);
	if((FALSE==$result) AND (FALSE==$result1)){
		return FALSE;
	}else{
		return TRUE;
	}

}

function createtabStatMAJ()
{
$sql="CREATE TABLE `tab_AS_stat` (
`id_stat` int(11) NOT NULL auto_increment,
  `type_AS` VARCHAR(11) ,
  `id_AS` int(11) NOT NULL,
  `date_AS` datetime ,
  `inscrits` int(11) NOT NULL,
  `presents` int(11) NOT NULL,
  `absents` int(11) NOT NULL,
  `attente` int(11) NOT NULL,
  `nbplace` int(11) NOT NULL,
  `statut_programmation` int(11) NOT NULL,
   `id_anim` int(11) NOT NULL,
  `id_epn` int(11) NOT NULL,
   PRIMARY KEY (`id_stat`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci 
";
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
	if(FALSE==$result){
		return FALSE;
	}else{
		return TRUE;
	}
}


function InsertStatMAJ($type,$id,$date,$inscrits,$presents,$absents,$attente,$nbplace,$statut)
{
$sql="INSERT INTO `tab_AS_stat`(`id_stat`, `type_AS`, `id_AS`, `date_AS`, `inscrits`, `presents`, `absents`, `attente`,  `nbplace`,`statut_programmation`) 
VALUES ('','".$type."','".$id."','".$date."','".$inscrits."','".$presents."', '".$absents."','".$attente."','".$nbplace."','".$statut."') ";
$db=opendb();
$result = mysqli_query($db,$sql);
$id=mysqli_insert_id($db);
closedb($db);
	if(FALSE==$result){
		return FALSE;
	}else{
		return $id;
	}
}

//recuperation des stats atelier
function getStatAtelierMAJ()
{
$sql="SELECT `nombre_presents` , `nombre_inscrits` , tab_atelier.`date_atelier` , `heure_atelier` , `nbplace_atelier` , `statut_atelier` , tab_atelier.`id_atelier`
FROM `tab_atelier_stat` , tab_atelier
WHERE `tab_atelier_stat`.`id_atelier` = tab_atelier.`id_atelier`";
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
	if(FALSE==$result){
		return FALSE;
	}else{
		return $result;
	}
}

function getUserAttenteMAJ($id)
{
$sql="SELECT count(`id_user`) as NB FROM `rel_atelier_user` WHERE `id_atelier`=".$id." AND `status_rel_atelier_user`=2";
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
	if(FALSE==$result){
		return FALSE;
	}else{
		$row=mysqli_fetch_array($result);
		return $row['NB'];
	}

}

function getStatSessionmodifMAJ()
{
$sql="SELECT tab_session.`id_session` , `nombre_presents` , `nombre_inscrits` , tab_session_dates.`date_session` , `nbplace_session` , `status_session` , `statut_datesession` , id_datesession
FROM `tab_session_stat` , tab_session, tab_session_dates
WHERE tab_session.id_session = `tab_session_stat`.id_session
AND DATE( tab_session_dates.`date_session` ) = tab_session_stat.`date_session`  ";
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
	if(FALSE==$result){
		return FALSE;
	}else{
		return $result;
	}

}

function getUserAttenteSMAJ($iddate)
{
$sql="SELECT count(`id_user`) as NB FROM `rel_session_user` WHERE `status_rel_session`=2 AND `id_datesession`=".$iddate;
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
	if(FALSE==$result){
		return FALSE;
	}else{
		$row=mysqli_fetch_array($result);
		return $row['NB'];
	}
}

//retourne les infos des ateliers annulés pour les mettre dans les stats
function getAteliersAnnule()
{
$sql="SELECT `id_atelier`,`date_atelier`,`heure_atelier`,`nbplace_atelier` FROM `tab_atelier` WHERE `statut_atelier`=3 ";
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
	if(FALSE==$result){
		return FALSE;
	}else{
		return $result;
	}

}
//retroune les id des ateliers en programmation statut=3
function getAtelierPrograMAJ($x)
{
$sql="SELECT `id_atelier` FROM `tab_atelier` WHERE `statut_atelier`=".$x." ";
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
	if(FALSE==$result){
		return FALSE;
	}else{
		return $result;
	}
}

//retourne le nombre d'usagers inscrits à un atelier 
function getUserAtelierInscritsMAJ($id)
{
$sql="SELECT count( `id_user` ) AS NB
FROM `rel_atelier_user`
WHERE `id_atelier` =".$id."
AND `status_rel_atelier_user` =0";
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
	if(FALSE==$result){
		return FALSE;
	}else{
		$row=mysqli_fetch_array($result);
		return $row['NB'];
	}


}

//
//supprimer les champs heure et session_dates de la table_session
function suppressTabSession2MAJ()
{
$sql="ALTER TABLE `tab_session`
  DROP `heure_session`,
  DROP `session_dates`";

$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
	if(FALSE==$result){
		return FALSE;
	}else{
		return TRUE;
	}


}

///mettre a jour les adherents pour leur date de renouvellemment abonnement
function updateUserStatutMAJ()
{
$sql="UPDATE `tab_user` SET `status_user`=2 WHERE `status_user`=1 AND `dateRen_user`< DATE(NOW())";

$db=opendb();
$result = mysqli_query($db, $sql);
closedb($db);
    if ($result == FALSE )
    {
        return FALSE ;
    }
    else
    {
		return TRUE ;
    }

}


?>