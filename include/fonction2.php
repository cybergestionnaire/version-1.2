<?php



//-------------------------
// Fonctions additionnelles
function csv_to_array($filename, $delimiter)
{
    if(!file_exists($filename) || !is_readable($filename))
        return FALSE;

    $header = NULL;
    $data = array();
    if (($handle = fopen($filename, 'r')) !== FALSE)
    {
    	$lines = file($filename);
  foreach($lines as $line) {
	    $values = str_getcsv($line, $delimiter, $enclosure='"', $escape='\\');
	    if(!$header) $header = $values;
	    else $data[] = array_combine($header, $values);
	  }

        fclose($handle);
    }
        
    return $data;
}

function gFilelog($texte,$titre)
{
$pathfichier="logs/".$titre;
 $fp = fopen($pathfichier, "a+");
fseek($fp,SEEK_END);
fputs ($fp, $texte);
fclose ($fp);

}

//Retourne le numero de version du cybergestionnaire
function getVersion($id)
{
$sql="SELECT `name_config` FROM `tab_config` WHERE `id_espace`=".$id."";
  $db=opendb();
$result = mysqli_query($db,$sql);
   closedb($db);	
	 if(FALSE == $result)
    {
        return FALSE ;
    }
    else
    {
        $row=mysqli_fetch_array($result);
		return $row["name_config"];
    }


}

//affiche les boutons dans la config suivant la page desactivee
function configBut($page)
{
		$confbut=array(
		array(41,"fa fa-cloud","VILLES"),
		array(43,"fa fa-home","EPN"),
		array(44,"fa fa-square","SALLES"),
		array(42,"fa fa-clock-o","HORAIRES"),
		array(47,"fa fa-eur","TARIFS"),
		array(2,"fa fa-desktop","MATERIEL"),
		array(48,"fa fa-user-md","USAGES"),
		array(46,"fa fa-caret-square-o-up","USAGES POSTES"),
		array(23,"fa fa-users","ADMIN/ANIM"),
		array(49,"fa fa-database","BDD"),
		array(25,"fa fa-unlock-alt","EPN-CONNECT"),
		array(53,"fa fa-user-plus","INSCRIPTIONS"),
	);
	
	for($u=0;$u<count($confbut);$u++){
		if($confbut[$u][0]==$page){ $disab="disabled";}else{$disab="";}
		//debug($confbut[$u][0]);
		$htmlbut.='<a class="btn btn-app '.$disab.'"  href="index.php?a='.$confbut[$u][0].'"><i class="'.$confbut[$u][1].'"></i> '.$confbut[$u][2].'</a>';
		
	}
		
	
	return $htmlbut;
}


///******RESERVATIONS ***///
//Donne la derni�re r�servation d'un adh�rent (page liste user)
function getLastResaUser($id){
	$sql="SELECT  MAX(`dateresa_resa`) as last_resa FROM `tab_resa` WHERE `id_user_resa`=".$id;
	 $db=opendb();
    $result = mysqli_query($db,$sql);
     closedb($db);
    if(NULL == $result)
    {
        return FALSE ;
    }
    else
    {
        $row=mysqli_fetch_array($result) ;
				return $row['last_resa'];
    }
		
}


///liste des resas pour le mois en cours page user_resa
function getUserResaById($id,$m,$y)
{
$sql="SELECT `id_resa`,`dateresa_resa`,`debut_resa`,`duree_resa`,nom_computer FROM tab_resa 
          INNER JOIN tab_computer ON id_computer=id_computer_resa
          WHERE `id_user_resa`=".$id."
			AND `dateresa_resa` BETWEEN '".$y."-".$m."-01' AND '".$y."-".$m."-31'
			ORDER BY `dateresa_resa` ASC , `debut_resa` ASC
	";
    
    $db=opendb();
    $result = mysqli_query($db,$sql);
     closedb($db);
    if(FALSE == mysqli_num_rows($result))
    {
        return FALSE ;
    }
    else
    {
        return $result ;
    }
}

//Liste les adherents qui ont une reservation en cours
function Listadhresa($date1,$date2)
{
$sql="SELECT id_user_resa, nom_user, prenom_user
		FROM tab_resa, tab_user
		WHERE tab_user.id_user=tab_resa.id_user_resa
		AND dateresa_resa BETWEEN '".$date1."' AND '".$date2."'
		AND status_resa !=1
		";
	$db=opendb();
    $result = mysqli_query($db,$sql);
     closedb($db);
    if(FALSE == $result)
    {
        return FALSE ;
    }
    else
    {
        return $result;
    }

}


//fonction qui calcule le temps credite par semaine


// renvoi toutes les reservations des utilisateurs sur 1 semaine
function getAllResaById($date1,$date2)
{
    $sql="SELECT id_resa,`dateresa_resa`,`debut_resa`,`duree_resa`,nom_computer,nom_user, prenom_user
			FROM tab_resa, tab_user,tab_computer
			WHERE tab_user.id_user=tab_resa.id_user_resa
			AND id_computer=id_computer_resa
			AND dateresa_resa BETWEEN '".$date1."' AND '".$date2."'
			ORDER BY `dateresa_resa` ASC , `debut_resa` ASC";
    
    $db=opendb();
    $result = mysqli_query($db,$sql);
     closedb($db);
    if(FALSE == mysqli_num_rows($result))
    {
        return FALSE ;
    }
    else
    {
        return $result ;
    }
}


// fonction qui renvoi le total temps des resa d'un utilisateur par semaine
function getResaUserByWeek($date1,$date2,$iduser)
{
 $sql="SELECT SUM(duree_resa) AS utilise
        FROM tab_resa
        INNER JOIN tab_user ON id_user=id_user_resa
        WHERE id_user_resa=".$iduser."
		AND dateresa_resa BETWEEN '".$date1."' AND '".$date2."'
        ";

    $db=opendb();
    $result = mysqli_query($db,$sql);
     closedb($db);
   if ($result==FALSE)
	{
      return FALSE;
	}
    else
	{
      return  mysqli_fetch_array($result) ;
	}

}



function numToDate($quant, $annee)
{
	 $date = strtotime("+".($quant)." day", mktime(12, 0, 0, 01, 01, $annee));
	return date("Y-m-d", $date);
}



//////FONCTIONS SUR les param�trages des ateliers//////
//
function addCategorie($nom)
{
$sql="INSERT INTO `tab_atelier_categorie`(`id_atelier_categorie`, `label_categorie`) VALUES ('','".$nom."')";
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
if (FALSE==$result)
{
	return FALSE;
}
else{
	return TRUE;
	}
	
}
function modCategorie($id,$nom)
{
$sql="UPDATE `tab_atelier_categorie` SET `label_categorie`='".$nom."' WHERE `id_atelier_categorie`='".$id."' ";
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
if (FALSE==$result)
{
	return FALSE;
}
else{
	return TRUE;
	}

}

function supCategorie($id)
{
$sql="DELETE FROM tab_atelier_categorie WHERE id_atelier_categorie='".$id."' ";
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
if (FALSE==$result)
{
	return FALSE;
}
else{
	return TRUE;
	}
}

///Niveaux d'ateliers
function addNiveau($nom)
{
$sql="INSERT INTO `tab_level`(`id_level`, `code_level`,`nom_level`) VALUES ('','','".$nom."')";
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
if (FALSE==$result)
{
	return FALSE;
}
else{
	return TRUE;
	}
	
}
function modNiveau($id,$nom)
{
$sql="UPDATE `tab_level` SET `nom_level`='".$nom."' WHERE `id_level`='".$id."' ";
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
if (FALSE==$result)
{
	return FALSE;
}
else{
	return TRUE;
	}

}

function supNiveau($id)
{
$sql="DELETE FROM `tab_level` WHERE id_level='".$id."' ";
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
if (FALSE==$result)
{
	return FALSE;
}
else{
	return TRUE;
	}
}

///Categories socioprofessionnelles
function addcsp($nom)
{
$sql="INSERT INTO `tab_csp`(`id_csp`, `csp`) VALUES ('','".$nom."')";
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
if (FALSE==$result)
{
	return FALSE;
}
else{
	return TRUE;
	}
	
}

function modcsp($id,$nom)
{
$sql="UPDATE `tab_csp` SET `csp`='".$nom."' WHERE `id_csp`='".$id."' ";
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
if (FALSE==$result)
{
	return FALSE;
}
else{
	return TRUE;
	}

}

function supcsp($id)
{
$sql="DELETE FROM `tab_csp` WHERE `id_csp`='".$id."' ";
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
if (FALSE==$result)
{
	return FALSE;
}
else{
	return TRUE;
	}
}



////
//
function getAllAnim(){
$sql="SELECT id_user, nom_user, prenom_user FROM tab_user
WHERE  status_user=3
ORDER BY nom_user ASC
";
 $db=opendb();
    $result = mysqli_query($db,$sql);
     closedb($db);
    if ($result == FALSE )
    {
        return FALSE ;
    }
    else
    {
        $sujet = array();
        $nb= mysqli_num_rows($result);
        for ($i=1;$i<=$nb;$i++)
        {
            $row = mysqli_fetch_array($result);
            $sujet[$row["id_user"]] = $row["nom_user"]." ".$row["prenom_user"] ;
        }
        return $sujet ;
    }
}

function getAllSalleAtelier()
{
$sql="SELECT id_salle, nom_salle FROM tab_salle ORDER BY nom_salle ASC";
$db=opendb();
    $result = mysqli_query($db,$sql);
     closedb($db);
    if ($result == FALSE )
    {
        return FALSE ;
    }
    else
    {
        $sujet = array();
        $nb= mysqli_num_rows($result);
        for ($i=1;$i<=$nb;$i++)
        {
            $row = mysqli_fetch_array($result);
            $sujet[$row["id_salle"]] = $row["nom_salle"] ;
        }
        return $sujet ;
    }

}




function searchUserAtelier($exp)
{
    $sql="SELECT `id_user` , `nom_user` , `prenom_user`
        FROM `tab_user`
        WHERE `nom_user` LIKE '%".$exp."%'
        OR `prenom_user` LIKE '%".$exp."%'
		
       
        ORDER BY `nom_user` ASC";
    $db=opendb();
    $result = mysqli_query($db,$sql);
     closedb($db);
    if ($result == FALSE )
    {
        return FALSE ;
    }
    else
    {
        return $result ;
    }
}
// chercher tous les adh�rents inscrits aux ateliers.
function getAllUserAtelier($epn)
{

$sql="SELECT DISTINCT tab_user.id_user, `nom_user` ,  `prenom_user`  
FROM  `rel_atelier_user` , tab_user, tab_atelier
WHERE tab_user.id_user =  `rel_atelier_user`.`id_user`
AND `epn_user`= ".$epn."
AND YEAR(date_atelier)=YEAR(NOW())
UNION 
SELECT DISTINCT tab_user.id_user, `nom_user` ,  `prenom_user` 
FROM  `rel_session_user` , tab_user,tab_session
WHERE tab_user.id_user =  `rel_session_user`.`id_user` 
AND `epn_user`=".$epn."
AND YEAR(date_session)=YEAR(NOW())
ORDER BY nom_user ASC";
  
  $db=opendb();
  $result = mysqli_query($db,$sql);
   closedb($db);
  if (FALSE == $result)
  {
      return FALSE;
  }
  else
  {
      return $result;
  }
}


function getAllUserAtelierMois($y,$m)
{
$an=$y."-".$m."-01";
$anf=$y."-".($m+2)."-31";

$sql="SELECT DISTINCT nom_user,prenom_user, mail_user,tel_user, rel.id_user
	FROM `rel_atelier_user` AS rel, tab_atelier AS atelier, tab_user AS coor
	WHERE rel.`id_atelier`=atelier.`id_atelier`
	AND atelier.date_atelier BETWEEN '".$an."' AND '".$anf."'
	AND rel.id_user=coor.id_user
	ORDER BY nom_user ASC";
  
  $db=opendb();
  $result = mysqli_query($db,$sql);
   closedb($db);
  if (FALSE == $result)
  {
      return FALSE;
  }
  else
  {
      return $result;
  }
}

///Function dans le courrier /// A SUPPRIMER
function MakePDFUserAtelier($y,$m)
{
$an=$y."-".$m."-01";
$anf=$y."-".($m+2)."-31";

$sql="SELECT DISTINCT id_user
	FROM rel_atelier_user AS r
	LEFT JOIN tab_atelier AS a ON r.id_atelier = a.id_atelier
	WHERE a.date_atelier BETWEEN '".$an."' AND '".$anf."'
	";
  
  $db=opendb();
  $result = mysqli_query($db,$sql);
   closedb($db);
  if (FALSE == $result)
  {
      return FALSE;
  }
  else
  {
      //$row=mysqli_fetch_array($result);
	  return $result;
  }
}

function getAttenteAtelier($idatelier)
{
   $sql = "SELECT `id_rel_atelier_user` FROM `rel_atelier_user` WHERE `id_atelier`=".$idatelier." 
	AND `status_rel_atelier_user`= 2 ";
   $db=opendb();
   $result = mysqli_query($db,$sql);
    closedb($db);
   if (FALSE != $result)
   {
      return mysqli_num_rows($result) ;
   }
}

//pages utilisateur les ateliers programmes
function getMyFutAtelier($y, $m, $d)
{
if ($y==date('Y')){
	
	$sql= "SELECT *
			FROM `tab_atelier` 
			WHERE `date_atelier` BETWEEN '".$y."-".$m."-".$d."' AND '".$y."-12-31'
			ORDER BY `date_atelier` ASC";
			
	}
	else if ($year>date('Y')){
	
	$sql="SELECT *
			FROM `tab_atelier` 
			WHERE `date_atelier` BETWEEN '".$y."-01-01' AND '".$y."-12-31'
			ORDER BY `date_atelier` ASC";
	
	}		
		$db=opendb();
	  $result = mysqli_query($db,$sql);
	  closedb($db);
	  if (FALSE == $result)
	  {
		  return FALSE ;
	  }
	  else
	  {
		return $result;
	  }


}

/// Update du statut de l'atelier
function UpdateAtelierStatut($id,$statut)
{
$sql="UPDATE `tab_atelier` SET `statut_atelier`=".$statut." WHERE `id_atelier`=".$id;
$db=opendb();
	  $result = mysqli_query($db,$sql);
	  closedb($db);
	  if (FALSE == $result)
	  {
		  return FALSE ;
	  }
	  else
	  {
		return $result;
	  }

}

///////////***********Transaction sur les ateliers, gestion des forfaits ***************///
///
///les 50 derni�res transactions
function getLastTransactions()
{

$sql="SELECT distinct(`id_user`) FROM `tab_transactions` WHERE `id_tarif`> 1 AND `type_transac`='for' ORDER BY `date_transac` DESC";
$db=opendb();
 $result = mysqli_query($db,$sql);
	  closedb($db);
	  if (FALSE == $result)
	  {
		  return FALSE ;
	  }
	  else
	  {
		return $result;
	  }


}

//Touts les Forfaits actifs d'un utlisateur
function getAllForfaitUser($id,$type)
{

$sql="SELECT * from `tab_transactions` WHERE id_user='".$id."' AND  type_transac='".$type."' ";

$db=opendb();
 $result = mysqli_query($db,$sql);
	  closedb($db);
	  if (FALSE == $result)
	  {
		  return FALSE ;
	  }
	  else
	  {
		return $result;
	  }

}

//gorfait utilise par l'adherent
function getForfaitUtilise($iduser,$tarif)
{
$sql="SELECT COUNT(`id_forfait`) as totalf FROM `rel_user_forfait` WHERE `id_user`='".$iduser."' AND`id_tarif`=".$tarif;
$db=opendb();
 $result = mysqli_query($db,$sql);
	  closedb($db);
	  if (FALSE == $result)
	  {
		  return FALSE ;
	  }
	  else
	  {
		$row=  mysqli_fetch_array($result);
		return $row['totalf'];
	  }

}


//nombre d'atelier inscrit par l'adh�rent
function getInscriptionUser($iduser,$statut)
{
$sql="SELECT COUNT(`id_forfait`) as totalf FROM `rel_user_forfait` WHERE `id_user`='".$iduser."' and `statut_forfait` =".$status;
$db=opendb();
 $result = mysqli_query($db,$sql);
	  closedb($db);
	  if (FALSE == $result)
	  {
		  return FALSE ;
	  }
	  else
	  {
		$row=  mysqli_fetch_array($result);
		return $row['totalf'];
	  }

}
///*****
// DEPRECATED
function getForfaitUserValid($iduser)
{
$sql="SELECT rel.`id_forfait`, `total_atelier`, `depense`, `statut_forfait`,`date_transac`,`status_transac` FROM `rel_user_forfait` as rel, tab_transactions as trans 
WHERE rel.`id_user`=".$iduser." AND rel.`id_transac`=trans.`id_transac`
AND rel.`statut_forfait` =1
";

$db=opendb();
 $result = mysqli_query($db,$sql);

 closedb($db);
    
  if (FALSE == $result)
  {
	  return FALSE ;
  }
  else
  {
	$row=  mysqli_fetch_array($result);
	return $row;
  }


}


function getForfaitAchete($iduser,$type)
{
$sql="SELECT SUM(nbr_forfait*nb_atelier_forfait) AS total
FROM `tab_transactions` , tab_tarifs
WHERE `id_user` ='".$iduser."'
AND type_transac='".$type."'
AND `status_transac` =1
AND tab_transactions.id_tarif = tab_tarifs.id_tarif";
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
///rappel statut transaction, encaiss�=1, en attente=0, termin�=2
  if (FALSE == $result)
	  {
		  return FALSE ;
	  }
	  else
	  {
			
		$row= mysqli_fetch_array($result);
		if($row['total']==NULL){
		return 0;
		}else{
		return $row['total'];
		}
	  }


}
//////*****

//getnbASUserEncours  calcule le nombre d'atelier et de session dont l'inscription est en cours ou valid�e
function getnbASUserEncours($iduser,$statut)
{
$sql="SELECT count(`id_rel_atelier_user`)as atelier FROM `rel_atelier_user` WHERE `status_rel_atelier_user`=".$statut." AND `id_user`=".$iduser;
$sql2="SELECT count(`id_rel_session`) as session FROM `rel_session_user`, tab_session WHERE `status_rel_session`=".$statut." AND tab_session.status_session=".$statut."  AND `id_user`=".$iduser." AND tab_session.id_session = rel_session_user.id_session ";

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

//getnbASUserEncours  calcule le nombre d'atelier et de session dont l'inscription est valid�e
function getnbASUservalidees($iduser)
{
$sql="SELECT count(`id_rel_atelier_user`) as atelier FROM `rel_atelier_user` WHERE `status_rel_atelier_user`=1 AND `id_user`=".$iduser;
$sql2="SELECT count(`id_rel_session`)as session  FROM `rel_session_user` WHERE `status_rel_session`=1   AND `id_user`=".$iduser."  ";

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
//retourne le nombre d'ateliers issus de forfaits d�j� archiv�s
function getFUserArchiv($iduser)
{
$sql="SELECT SUM(`total_atelier`) as nb FROM `rel_user_forfait` WHERE `statut_forfait`=2 AND `id_user`=".$iduser;
$db=opendb();
 $result = mysqli_query($db,$sql);
 closedb($db);
if (FALSE == $result)
{
  return FALSE ;
}
else
{
$row= mysqli_fetch_array($result);
return $row['nb'];
}

}

//nombre d'ateliers par forfait
function getNbatelierbytarif($id)
{
$sql="SELECT `nb_atelier_forfait` FROM `tab_tarifs` WHERE `id_tarif`=".$id;
$db=opendb();
 $result = mysqli_query($db,$sql);
 closedb($db);
	  if (FALSE == $result)
	  {
		  return FALSE ;
	  }
	  else
	  {
		$row= mysqli_fetch_array($result);
		return $row['nb_atelier_forfait'];
	  }

}


//Forfait � modifier
function getForfait($id)
{
$sql="SELECT * from `tab_transactions` WHERE   id_transac='".$id."'  LIMIT 1";
$db=opendb();
 $result = mysqli_query($db,$sql);
 closedb($db);
	  if (FALSE == $result)
	  {
		  return FALSE ;
	  }
	  else
	  {
		return mysqli_fetch_array($result);
	  }


}

//gettransactemps(id)
// retourne la transaction sur forfait temps en cours + id rel pour epnconnect*******************************//////////////////////////

function getTransactemps($id_user)
{
	$type="temps";
	$sql="SELECT `id_transac` , `id_rel_forfait_user`,`date_transac`,`status_transac`,`id_tarif`, nbr_forfait
FROM `tab_transactions` , `rel_forfait_user`
WHERE `type_transac`='".$type."'
AND `tab_transactions`.`id_user`='".$id_user."'
AND `tab_transactions`.`id_tarif`=`rel_forfait_user`.`id_forfait`";
	
	$db=opendb();
 $result = mysqli_query($db,$sql);
 closedb($db);
	  if (FALSE == $result)
	  {
		  return FALSE ;
	  }
	  else
	  {
			return mysqli_fetch_array($result);
	  }
	
}

///getForfaitConsult($iduser)
///**retourne le com du forfait consultation en cours pour l'adherents
function getForfaitConsult($iduser)
{
	$sql="SELECT `nom_forfait`,`status_transac`,`nombre_temps_affectation`,`unite_temps_affectation`,`frequence_temps_affectation` FROM `tab_forfait`,tab_transactions WHERE `type_transac`='temps' AND tab_transactions.`id_tarif`=`tab_forfait`.id_forfait AND `id_user`=".$iduser ;
	$db=opendb();
 $result = mysqli_query($db,$sql);
 closedb($db);
	  if (FALSE == $result)
	  {
		  return FALSE ;
	  }
	  else
	  {
			return mysqli_fetch_array($result);
	  }
	
}


function addrelconsultationuser($type,$tarif_forfait,$id_user)
{
if ($type==1){
	$sql="INSERT INTO `rel_forfait_user`(`id_rel_forfait_user`, `id_forfait`, `id_user`) VALUES ('','".$tarif_forfait."','".$id_user."') ";
}else if ($type==2) {
	$sql="UPDATE `rel_forfait_user` SET `id_forfait`='".$tarif_forfait."' WHERE `id_user`='".$id_user."' ";
}
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
if (FALSE == $result)
{
  return FALSE ;
}
else
{
return TRUE;
}
}

function delreluserforfaittemps($iduser)
{
	$sql="DELETE FROM `rel_forfait_user` WHERE `id_user`='".$iduser."' ";
	$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
if (FALSE == $result)
{
  return FALSE ;
}
else
{
return TRUE;
}
}

// ins�rer le forfait achet�, et la relation pour le d�compte des ateliers du forfait
function addForfaitUser($type_transac,$id_user,$tarif_forfait,$nbreforfait,$date,$statutp)
{

$sql="INSERT INTO `tab_transactions`(`id_transac`, `type_transac`, `id_user`, `id_tarif`, `nbr_forfait`, `date_transac`, `status_transac`) VALUES ('','".$type_transac."','".$id_user."','".$tarif_forfait."','".$nbreforfait."','".$date."','".$statutp."')";

$db=opendb();
$result = mysqli_query($db,$sql);
$idtransac=mysqli_insert_id($db);

closedb($db);
if (FALSE == $result)
{
  return FALSE ;
}
else
{
return $idtransac;
}

}

function addRelforfaitUser($id_user,$idtransac,$nbatelier,$depense,$statutp)
{
	
$sql="INSERT INTO `rel_user_forfait`(`id_forfait`, `id_user`, `id_transac`, `total_atelier`, `depense`, `statut_forfait`) 
VALUES ('','".$id_user."','".$idtransac."','".$nbatelier."','".$depense."','".$statutp."') ";
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
if (FALSE == $result)
{
  return FALSE ;
}
else
{
return TRUE;
}
}

//modification d'un forfait sur le compte
function modifForfaitUser($id,$tarif_forfait,$date,$nbreforfait,$statutp,$nbatelier)
{
$sql="UPDATE `tab_transactions` SET `id_tarif`='".$tarif_forfait."',`nbr_forfait`='".$nbreforfait."',`date_transac`='".$date."',`status_transac`='".$statutp."' WHERE `id_transac`=".$id;

$sql2="UPDATE `rel_user_forfait` SET `total_atelier`='".$nbatelier."',`statut_forfait`='".$statutp."' WHERE `id_transac`=".$id;

$db=opendb();
	  $result = mysqli_query($db,$sql);
	   $result2 = mysqli_query($db,$sql2);
	  closedb($db);
	  if (FALSE == $result)
	  {
		  return FALSE ;
	  }
	  else
	  {
		return TRUE;
	  }


}

//retourner le nombre d'atelier d�pens�s sur le forfait
function getForfaitdepense($iduser,$tarif,$status)
{
$sql="SELECT COUNT( `id_forfait` ) AS total
FROM `rel_user_forfait`
WHERE `id_user` ='".$iduser."'
AND `id_tarif` ='".$tarif."'
AND `statut_forfait` =".$status;

$db=opendb();
	  $result = mysqli_query($db,$sql);
	  closedb($db);
	  if (FALSE == $result)
	  {
		  return FALSE ;
	  }
	  else
	  {
		$row=mysqli_fetch_array($result);
		return $row['total'];
	  }

}

//FORFAIT ATELIERS 
//**Retoune le forfait atelier en cours pour l'adherent

function getForfaitUserEncours($iduser)
{
$sql="SELECT `id_forfait`,`total_atelier`,`depense` FROM `rel_user_forfait` WHERE `id_user`=".$iduser." AND `statut_forfait`=1";
$db=opendb();
	  $result = mysqli_query($db,$sql);
	  closedb($db);
	  if (FALSE == $result)
	  {
		  return FALSE ;
	  }
	  else
	  {
		$row=mysqli_fetch_array($result);
		return $row;
	  }

}

//incrementer le nombre depense
function updateForfaitdepense($id)
{
	$sql="UPDATE `rel_user_forfait` SET `depense`=depense+1 
	WHERE `id_forfait`='".$id."' 
	";

	$db=opendb();
	  $result = mysqli_query($db,$sql);
	  closedb($db);
	  if (FALSE == $result)
	  {
		  return FALSE ;
	  }
	  else
	  {
		return TRUE;
	  }

}

//retirer un atelier du forfait en cours
function DeleteOneFromForfait($id,$iduser)
{

$sql="UPDATE `rel_user_forfait` SET `depense`=`depense`-1 WHERE `id_user`=".$iduser." AND `id_forfait`=".$id." ";
	$db=opendb();
	  $result = mysqli_query($db,$sql);
	  closedb($db);
	  if (FALSE == $result)
	  {
		  return FALSE ;
	  }
	  else
	  {
		return TRUE;
	  }


}

//cloturer le forfait en cours d'un adherent
function clotureforfaitUser($depense,$idforfait)
{
$sql="UPDATE `rel_user_forfait` 
SET `depense`=".$depense.",
`statut_forfait`=2 
WHERE `id_forfait`=".$idforfait." ";
$db=opendb();
	  $result = mysqli_query($db,$sql);
	  closedb($db);
	  if (FALSE == $result)
	  {
		  return FALSE ;
	  }
	  else
	  {
		return TRUE;
	  }


}
//determiner le statut d'un forfait
function getForfaitDonnesbyID($id,$iduser)
{
$sql="SELECT `id_forfait`, `total_atelier`, `depense`, `statut_forfait` FROM `rel_user_forfait` WHERE `id_user`=".$iduser." AND `id_transac`=".$id;
$db=opendb();
	  $result = mysqli_query($db,$sql);
	  closedb($db);
	  if (FALSE == $result)
	  {
		  return FALSE ;
	  }
	  else
	  {
		$row=mysqli_fetch_array($result);
		return $row;
	  }
}


//del forfait retirer un forfait d'un compte
function delForfait($id)
{
$sql="DELETE FROM `tab_transactions` WHERE `id_transac`=".$id;
$db=opendb();
	  $result = mysqli_query($db,$sql);
	  closedb($db);
	  if (FALSE == $result)
	  {
		  return FALSE ;
	  }
	  else
	  {
		return TRUE;
	  }


}


///***** FORFAITS POUR EPN CONNECT ****/////
//
// addForfait()
// ajoute un forfait
function addForfait($date_creat_forfait, $type_forfait, $nom_forfait, $prix_forfait, $critere_forfait, $comment_forfait,  $nombre_duree_forfait, $unite_duree_forfait, $temps_forfait_illimite, $date_debut_forfait,$status_forfait,$nombre_temps_affectation, $unite_temps_affectation, $frequence_temps_affectation, $temps_affectation_occasionnel,$nombre_atelier_forfait )
{
	$date_creat_forfait=convertDate($date_creat_forfait);
	$date_debut_forfait=convertDate($date_debut_forfait);
	//$type_forfait=addslashes($type_forfait);
	
  $sql="INSERT INTO `tab_forfait`(`id_forfait`, `date_creation_forfait`, `type_forfait`, `nom_forfait`, `prix_forfait`, `critere_forfait`, `commentaire_forfait`, `nombre_duree_forfait`, `unite_duree_forfait`, `temps_forfait_illimite`, `date_debut_forfait`, `status_forfait`, `nombre_temps_affectation`, `unite_temps_affectation`, `frequence_temps_affectation`, `temps_affectation_occasionnel`, `nombre_atelier_forfait`)
        VALUES ('', '".$date_creat_forfait."', '".$type_forfait."', '".$nom_forfait."', '".$prix_forfait."', '".$critere_forfait."', '".$comment_forfait."', '".$nombre_duree_forfait."', '".$unite_duree_forfait."', '".$temps_forfait_illimite."', '".$date_debut_forfait."', '".$status_forfait."', '".$nombre_temps_affectation."', '".$unite_temps_affectation."', '".$frequence_temps_affectation."', '".$temps_affectation_occasionnel."', '".$nombre_atelier_forfait."' ) 
        ";
    $db=opendb();
  	$result = mysqli_query($db, $sql);
  if (FALSE == $result)
  {
      return FALSE;
  }
  else
  {
  		$ides=mysqli_insert_id($db);
      	return $ides;
      	//return TRUE;
  }
    closedb($db);
}

//recupere les infos des tarifs consultation
function getTarifConsult()
{
	
$sql="SELECT * FROM `tab_forfait`";
	$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
if (FALSE == $result)
  {
      return FALSE ;
  }
  else
  {
      return $result;
  }
		
}

//recupere la liste des tarifs de consultation
function getTarifsTemps()
{
	$sql="SELECT `id_forfait`,`nom_forfait`,`prix_forfait` FROM `tab_forfait` ";
	 $db=opendb();
    $result = mysqli_query($db,$sql);
    closedb($db);
    if (FALSE == $result)
    {
        return FALSE ;
    }
    else
    {
        $tarif = array();
        $nb= mysqli_num_rows($result);
        for ($i=1;$i<=$nb;$i++)
        {
            $row = mysqli_fetch_array($result);
            $tarif[$row["id_forfait"]] = $row["nom_forfait"]." (". $row["prix_forfait"]."&euro; )" ;
        }
        return $tarif ;
    }
	
}
//
// delForfait()
// supprime un atelier
function deletarifConsult($idforfait)
{
  	$sql = "DELETE FROM `tab_forfait` WHERE `id_forfait`=".$idforfait ;
    $db=opendb();
  	$result = mysqli_query($db, $sql);
    closedb($db);
	if (FALSE == $result)
	{
		return FALSE ;
  	}
  	else
  	{
	  	$sql = "DELETE FROM `rel_forfait_espace` WHERE `id_forfait`=".$idforfait ;
		$db=opendb();
		$result = mysqli_query($db, $sql);
		closedb($db);
		if (FALSE == $result)
		{
			return FALSE ;
		}
		else
		{
			return TRUE ;
		}
	}
}
// modConfigForfaitEsp()
//
function modConfigForfaitEsp($status_config, $id_espace)
{
    $sql="UPDATE `tab_config` 
			SET `activation_forfait`='".$status_config."'
			WHERE `id_espace`='".$id_espace."';";
    $db=opendb();
  	$result = mysqli_query($db, $sql);
    closedb($db);
  	if (FALSE == $result)
  	{
      	return FALSE;
  	}
  	else
  	{
      	return $result;
  	}
}
//
// addForfaitEspace()
// ajoute un espace li� au forfait
function addForfaitEspace($id_forfait, $id_espace)
{
  $sql="INSERT INTO `rel_forfait_espace` ( `id_forfait_espace` , `id_forfait`, `id_espace` )
        VALUES ('', '".$id_forfait."', '".$id_espace."' ) ";
    $db=opendb();
  	$result = mysqli_query($db, $sql);
    closedb($db);
  if (FALSE == $result)
  {
      return FALSE;
  }
  else
  {
      	return TRUE;
  }
}

//
//getAllRelForfaitEspace($id_forfait)$temps_illimite
//retrouve les espaces pour les forfaits consultation
function getAllRelForfaitEspace($id)
{
$sql="SELECT `id_espace` FROM `rel_forfait_espace` WHERE `id_forfait`='".$id."' ";
 $db=opendb();
  	$result = mysqli_query($db, $sql);
    closedb($db);
  	if (FALSE == $result)
  	{
      	return FALSE;
  	}
  	else
  	{
      	$epn = array();
        $nb= mysqli_num_rows($result);
        for ($i=0;$i<$nb;$i++)
        {
            $row = mysqli_fetch_array($result);
            $epn[$i] = $row["id_espace"] ;
        }
        return $epn ;
  	}
	
}


// modForfait(), $status_forfait
// modifie un forfait
function modForfait($idforfait,$type_forfait, $nom_forfait, $prix_forfait,$comment_forfait, $nombre_duree_forfait, $unite_duree_forfait, $temps_forfait_illimite, $nombre_temps_affectation, $unite_temps_affectation, $frequence_temps_affectation, $nombre_atelier_forfait, $temps_affectation_occasionnel)


{
  $sql="UPDATE `tab_forfait`
  SET `type_forfait`='".$type_forfait."',
      `nom_forfait`='".$nom_forfait."',
      `prix_forfait`='".$prix_forfait."',
     
      `commentaire_forfait`='".$comment_forfait."',
      
      `nombre_duree_forfait`='".$nombre_duree_forfait."',
      `unite_duree_forfait`='".$unite_duree_forfait."',
      `temps_forfait_illimite`='".$temps_forfait_illimite."',
      `nombre_temps_affectation`='".$nombre_temps_affectation."',
      `unite_temps_affectation`='".$unite_temps_affectation."',
      `frequence_temps_affectation`='".$frequence_temps_affectation."',
      `nombre_atelier_forfait`='".$nombre_atelier_forfait."',
      `temps_affectation_occasionnel`='".$temps_affectation_occasionnel."'
      
      WHERE `id_forfait` =".$idforfait." LIMIT 1 ";
    $db=opendb();
  	$result = mysqli_query($db, $sql);
    closedb($db);
  if (FALSE == $result)
  {
      return FALSE;
  }
  else
  {
      return TRUE;
  }
}

//Suite � modification de forfait
//deleteAllEspaceForfait

function deleteAllEspaceForfait($id)
{
  	//supprimer le lien dans la table de relation
  	$sql = "DELETE FROM `rel_forfait_espace` WHERE `id_forfait`='".$id."' " ;
	//remettre a 0 dans la table config
		$sql1="UPDATE `tab_config` 	SET `activation_forfait`=0 ";
  
  $db=opendb();
  	$result = mysqli_query($db, $sql);
  	$result1 = mysqli_query($db, $sql1);
    closedb($db);
  if (FALSE == $result)
  {
      return FALSE;
  }
  else
  {
      	return TRUE;
  }
}





//
//////////////********GESTION DES SESSIONS*********///////////////////
//Creation de nouvelles sessions dans la base
function createSession($sujet,$content,$niveau,$categorie)
{
$sql = "INSERT INTO `tab_session_sujet`(`id_session_sujet`, `session_titre`, `session_detail`, `session_niveau`, `session_categorie`) 
	VALUES ('','".$sujet."','".$content."','".$niveau."','".$categorie."')
		";
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
if (FALSE == $result)
  {
      return FALSE ;
  }
  else
  {
      return TRUE;
  }
  
}

//retrouver tous les sujets des sessions
function getAllSujetSession()
{
$sql = "SELECT id_session_sujet,session_titre FROM tab_session_sujet 
		ORDER BY session_titre ASC
		" ;
  $db=opendb();
  $result = mysqli_query($db,$sql);
  closedb($db);
  if (FALSE == $result)
  {
      return FALSE ;
  }
  else
  {
	 $sujet = array();
        $nb= mysqli_num_rows($result);
        for ($i=1;$i<=$nb;$i++)
        {
            $row = mysqli_fetch_array($result);
            $sujet[$row["id_session_sujet"]] = $row["session_titre"] ;
        }
        return $sujet ;
  }
 } 
 
//retrouver une session par son id
function getSujetSessionById($id)
{
$sql="SELECT *
		FROM tab_session_sujet 
		WHERE id_session_sujet=".$id."";
$db=opendb();
  $result = mysqli_query($db,$sql);
  closedb($db);
  if (FALSE == $result)
  {
      return FALSE ;
  }
  else
  {
	return $result;
  }

}

function delSujetSession($id)
{
 $sql = "DELETE FROM `tab_session_sujet` 
		WHERE `id_session_sujet`='".$id."'
		";
    $db=opendb();
    $result = mysqli_query($db,$sql);
    closedb($db);
    if (FALSE == $result)
    {
        return FALSE ;
    }
    else
    {
        return TRUE;
    }

}


function ModifSujetsession($id,$sujet,$content,$niveau,$categorie)
{

$sql = " UPDATE `tab_session_sujet` 
	SET 	`session_titre`= '".$sujet."',
		`session_detail`='".$content."',
		`session_niveau`='".$niveau."',
		`session_categorie`='".$categorie."'
			
		WHERE `id_session_sujet`='".$id."' " ;		
		
  
  $db=opendb();
  $result = mysqli_query($db,$sql);
  closedb($db);
  if (FALSE == $result)
  {
      return FALSE ;
  }
  else
  {
      return $result;
  }


}

//Retrouver le titre d'une session avec id
function getTitreSession($id) 
{
$sql=" SELECT id_session_sujet, nom_session, session_titre, session_detail
		FROM tab_session_sujet, tab_session
		WHERE id_session_sujet=nom_session
		AND nom_session='".$id."'
	";
	$db=opendb();
 $result = mysqli_query($db,$sql);
  closedb($db);
  if (FALSE == $result)
  {
      return FALSE ;
  }
  else
  {
      $row=mysqli_fetch_array($result);
	  return $row;
  }
}
 
 
// Affichage des sessions programm�es dans l futur + les session qui n'ont pas �t� valid�es Statut=0
function getFutsessions($epn)
{
//tout le reseau
if($epn==0){
	$sql="SELECT * FROM `tab_session`	WHERE  status_session=0 ORDER BY `date_session` ASC";
}else{
	//uniquement par epn
$sql="SELECT * FROM `tab_session`, tab_salle
WHERE status_session=0 
AND  tab_salle.`id_salle` = tab_session.`id_salle`
AND tab_salle.`id_espace` =".$epn."

ORDER BY `id_session` ASC";
}
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
if (FALSE == $result)
  {
      return FALSE ;
  }
  else
  {
      return $result ;
  }

}

//retourne les sessions par animateur
function getFutsessionsbyanim($anim)
{
	$sql="SELECT * FROM `tab_session` 
	WHERE `id_anim`=".$anim." 
	AND `status_session`=0
	
	ORDER BY `id_session` ASC ";
	
	$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
if (FALSE == $result)
  {
      return FALSE ;
  }
  else
  {
      return $result ;
  }

	
}


// retourne les sessions anciennement programm�s et archiv�es
function getAncSession($epn,$date)
{
$sql= "SELECT tab_session.id_session AS idsession, tab_session.date_session AS datep, session_titre, nbplace_session, nbre_dates_sessions, id_anim, tab_session.id_salle, id_tarif
FROM tab_session, tab_session_sujet, tab_salle
WHERE tab_salle.`id_salle` = tab_session.`id_salle`
AND tab_salle.`id_espace` =".$epn."
AND status_session =1
AND tab_session.nom_session = tab_session_sujet.id_session_sujet
AND YEAR( tab_session.date_session)=".$date."
ORDER BY date_session ASC ";
		
	$db=opendb();
  $result = mysqli_query($db,$sql);
  closedb($db);
  if (FALSE == $result)
  {
      return FALSE ;
  }
  else
  {
	return $result;
  }
}

function countPlaceSession ($idsession,$statut)
{
if($statut==0)
{
   $sql = "SELECT DISTINCT (`id_user`) FROM `rel_session_user` WHERE `id_session`='".$idsession."' 
			  AND status_rel_session<2
			
			";
}else {
  $sql = "SELECT DISTINCT (`id_user`) FROM `rel_session_user` WHERE `id_session`='".$idsession."' 
			AND status_rel_session=".$statut."
			
			";
}
   $db=opendb();
  $result = mysqli_query($db,$sql);
    closedb($db);
   if (FALSE != $result)
   {
      return mysqli_num_rows($result) ;
   }
}
//
// getSessionUser()
// renvoi les users inscrits a une session
function getSessionUser($idsession,$statut)
{

  $sql = "SELECT DISTINCT(rel.id_user), `nom_user` , `prenom_user`, `status_rel_session`
          FROM `tab_user` AS user, `rel_session_user` AS rel
          WHERE rel.id_user = user.id_user
	AND rel.status_rel_session=".$statut."
	  AND rel.id_session =".$idsession."  ORDER BY `nom_user` ASC
		  ";

  $db=opendb();
 $result = mysqli_query($db,$sql);
  closedb($db);
  if (FALSE == $result)
  {
      return FALSE ;
  }
  else
  {
      return $result;
  }
}

///renvoie les participants � un atelier valid� absents ou pr�sents

function getSessionValidpresences($idsession,$iddate)
{
$sql="SELECT rel.id_user,`nom_user` , `prenom_user`, `status_rel_session`
FROM  `tab_user` AS user, `rel_session_user` AS rel
WHERE `id_session`=".$idsession." 
AND rel.id_user = user.id_user
AND rel.status_rel_session<2
AND `id_datesession`=".$iddate." ";
 $db=opendb();
 $result = mysqli_query($db,$sql);
  closedb($db);
  if (FALSE == $result)
  {
      return FALSE ;
  }
  else
  {
      return $result;
  }


}


//Affichage des dates d'une session
function ValidSessionPresence($idsession,$nombre_present,$nombre_inscrit,$ids_presents,$date_session,$id_categorie,$nom_session)
{
$sql = "INSERT INTO `tab_session_stat` (`id_programmation`, `id_session`, `nombre_presents`, `nombre_inscrits`, `ids_presents`, `date_session`, `id_categorie`, `nom_session`)
		VALUES ('','".$idsession."','".$nombre_present."','".$nombre_inscrit."','".$ids_presents."','".$date_session."','".$id_categorie."','".$nom_session."')" ;

	$db=opendb();
 $result = mysqli_query($db,$sql);
  closedb($db);
  if (FALSE == $result)
  {
      return FALSE ;
  }
  else
  {
      return True;
  }
}


//Affichage d'une session
function getSession($id)
{
  $sql = "SELECT *
          FROM `tab_session`
          WHERE `id_session`=".$id."
		  ";
  $db=opendb();
 $result = mysqli_query($db,$sql);
  closedb($db);
  if (FALSE == $result)
  {
      return FALSE ;
  }
  else
  {
      $row = mysqli_fetch_array($result) ;
      return $row;
  }
}

function getNomSession($id)
{
 $sql = "SELECT *
          FROM `tab_session_sujet`
          WHERE `id_session_sujet`=".$id."
		  ";
		  $db=opendb();
 $result = mysqli_query($db,$sql);
  closedb($db);
  if (FALSE == $result)
  {
      return FALSE ;
  }
  else
  {
      $row = mysqli_fetch_array($result) ;
      return $row;
  }
}

function updateSessionStatut($id)
{
$sql="UPDATE `tab_session` SET `status_session`=1 WHERE `id_session`=".$id;
	$db=opendb();
 $result = mysqli_query($db,$sql);
  closedb($db);
  if (FALSE == $result)
  {
      return FALSE ;
  }
  else
  {
      return True;
  }
}

// inscrit un adherent a un atelier
function addUserSession($idsession,$iduser,$idstatut,$iddate)
{

    $sql ="INSERT INTO `rel_session_user` ( `id_rel_session` , `id_session` , `id_datesession`, `id_user` , `status_rel_session` )
             VALUES ('', '".$idsession."', '".$iddate."', '".$iduser."', '".$idstatut."')";
	     
     $db=opendb();
     $result = mysqli_query($db,$sql);
	closedb($db);
   
      if (FALSE == $result)
      {
          return FALSE ;
      }
      else
      {
          return TRUE;
      }
 
}

//
// delUserAtelier()
// Desinscription d'un adherent a un atelier
function delUserSession($idsession,$iduser)
{
    $sql = "DELETE FROM `rel_session_user` WHERE `id_user`=".$iduser." AND `id_session`=".$idsession ;
    $db=opendb();
   $result = mysqli_query($db,$sql);
  closedb($db);
    if (FALSE == $result)
    {
        return FALSE ;
    }
    else
    {
        return TRUE;
    }
}
//
//retourne le test si une des dates de la session a d�j� ete valid�e ou pas
// 
function getSessionvalidees($idsession)
{
$sql="SELECT count( `statut_datesession` ) AS nb FROM `tab_session_dates` WHERE `id_session` =".$idsession." AND `statut_datesession` >0";
  $db=opendb();
   $result = mysqli_query($db,$sql);
  closedb($db);
  
    
    if (FALSE == $result)
    {
        return FALSE ;
    }
    else
    {
        $row=mysqli_fetch_array($result);
	return $row["nb"];
    }

}


//
// modification du statut, passer de la liste d'attente � inscription
function ModifyUserSession($idsession,$iduser,$statut)
{
    $sql = "UPDATE `rel_session_user` 
			SET status_rel_session=".$statut."
			WHERE `id_user`=".$iduser." 
			AND `id_session`=".$idsession."
			";
    $db=opendb();
   $result = mysqli_query($db,$sql);
    closedb($db);
    if (FALSE == $result)
    {
        return FALSE ;
    }
    else
    {
        return TRUE;
    }
}


//validation des presences a une date de session
function ModifyUser1Session($idsession,$iduser,$statut,$iddate)
{
    $sql = "UPDATE `rel_session_user` 
			SET status_rel_session=".$statut."
			WHERE `id_user`=".$iduser." 
			AND `id_session`=".$idsession."
			AND id_datesession=".$iddate."
			";
    $db=opendb();
   $result = mysqli_query($db,$sql);
    closedb($db);
    if (FALSE == $result)
    {
        return FALSE ;
    }
    else
    {
        return TRUE;
    }
}
function miseazerosession($idsession,$iddate)
{
$sql = "UPDATE `rel_session_user` 
			SET status_rel_session=0
			WHERE `id_session`=".$idsession."
			AND id_datesession=".$iddate."
			";
    $db=opendb();
   $result = mysqli_query($db,$sql);
    closedb($db);
    if (FALSE == $result)
    {
        return FALSE ;
    }
    else
    {
        return TRUE;
    }


}


// verifie si un user est deja inscrit a un atelier ou non
function checkUserSession($idsession,$iduser)
{
  // verifie si le user n'est pas deja inscrit et si il reste des places disponibles.
  $sql = "SELECT * FROM `rel_session_user` WHERE `id_session` =".$idsession." AND `id_user` =".$iduser ;
  $db=opendb();
 $result = mysqli_query($db,$sql);
   closedb($db);
  if (mysqli_num_rows($result) == 0) // verifie si user deja inscrit
  {
      $sql3 = "SELECT `nbplace_session` FROM `tab_session` WHERE `id_session`=".$idsession ;
      $db=opendb();
      $result3 = mysqli_query($db,$sql3);
      $row = mysqli_fetch_array($result3);
       closedb($db);
      if (countPlace($idsession) < $row["nbplace_session"])  // verifie le nombre de place restante
      {
          return TRUE ;
      }
      else
      {
          return TRUE ; //inserer la liste d'attente Attention normalement doit etre FALSE
      }
  }
  else
  {
      return FALSE ;
  }
}

//
// addAtelier()
// cree une session d' atelier de formation
function addSession($dates,$nom,$nbplace,$nbre_dates_sessions,$anim,$salle,$tarif)
{
  $sql = "INSERT INTO `tab_session`(`id_session`, `date_session`, `nom_session`, `nbplace_session`, `nbre_dates_sessions`, `status_session`, `id_anim`, `id_salle`, `id_tarif`) 
  VALUES ('','".$dates."','".$nom."','".$nbplace."','".$nbre_dates_sessions."',0,'".$anim."','".$salle."','".$tarif."')" ;
$db=opendb();
 $result = mysqli_query($db,$sql);
 $id=mysqli_insert_id($db);

  closedb($db);
  if (FALSE == $result)
  {
	
	  return FALSE ;
  }
  else
  {
      return $id;
  }
}

// Ins�rer une date dans une session
function insertDateSessions($id,$date_s,$statut)
{
$sql="INSERT INTO `tab_session_dates`(`id_datesession`, `id_session`, `date_session`, `statut_datesession`)
		VALUES ('','".$id."','".$date_s."','".$statut."')";	
$db=opendb();
 $result = mysqli_query($db,$sql);
  $id=mysqli_insert_id($db);
  closedb($db);
  if (FALSE == $result)
  {
	
	  return FALSE ;
  }
  else
  {
       return $id;
  }	

}

//supprimer toutes les dates d'une session
function deleteAllDatessession($idsession)
{
$sql="DELETE FROM `tab_session_dates` WHERE `id_session`=".$idsession."";
$db=opendb();
 $result = mysqli_query($db,$sql);
  closedb($db);
  if (FALSE == $result)
  {
	
	  return FALSE ;
  }
  else
  {
      return True;
  }	

}
//retourne le test de pr�sence des dates dans la table de relation user/datessession
function testrelsessiondate($idsession)
{
$sql="SELECT `id_rel_session` FROM `rel_session_user` WHERE `id_session`=".$idsession ;
$db=opendb();
 $result = mysqli_query($db,$sql);
  closedb($db);
$rown=mysqli_num_rows($result);
  if ($rown==0)
  {
	 return FALSE ;
  }
  else
  {
      return True;
  }	
}

// supprimer une date faisant partie d'une session
function deletedatesession($iddatesession)
{
$sql="DELETE FROM `tab_session_dates` WHERE `id_datesession`=".$iddatesession." ";

$db=opendb();
 $result = mysqli_query($db,$sql);
 
  closedb($db);
  if (FALSE == $result)
  {
	
	  return FALSE ;
  }
  else
  {
      return TRUE;
  }	

}
//supprimer de la table de relation les valuers des dates precedemment modifi�es
function deleteRelsessionUser($idsession,$num)
{
$sql="DELETE FROM `rel_session_user` WHERE `id_session`=".$idsession." AND `id_datesession`=".$num." ";

$db=opendb();
 $result = mysqli_query($db,$sql);
  closedb($db);
  if (FALSE == $result)
  {
	
	  return FALSE ;
  }
  else
  {
      return True;
  }	

}
// modification des dates d'une session == modifiationn du nombre de dates + ou - !!
function updatenbredates($idsession,$nbre_dates)
{
$sql="UPDATE `tab_session` SET `nbre_dates_sessions`=".$nbre_dates." WHERE `id_session`=".$idsession." ";
$db=opendb();
 $result = mysqli_query($db,$sql);
  closedb($db);
  if (FALSE == $result)
  {
	
	  return FALSE ;
  }
  else
  {
      return True;
  }	


}
// Modifier une date faisant partie d'une session 
function modifDateSession($iddatesession,$datesession,$statut)
{
$sql="UPDATE `tab_session_dates` 
SET `date_session`='".$datesession."',
 `statut_datesession`='".$statut."'
WHERE `id_datesession`=".$iddatesession."
";
$db=opendb();
 $result = mysqli_query($db,$sql);
  closedb($db);
  if (FALSE == $result)
  {
	
	return FALSE ;
  }
  else
  {
      return True;
  }	

}

// retourne le nombre de dates valides par sessions
function getValidDatesbysession($idsession){
$sql="SELECT COUNT(  `id_datesession` ) AS nb
FROM  `tab_session_dates` 
WHERE  `statut_datesession` <2
AND  `id_session` =".$idsession." ";
$db=opendb();
 $result = mysqli_query($db,$sql);
  closedb($db);
  if (FALSE == $result)
  {
	
	return FALSE ;
  }
  else
  {
      $row=mysqli_fetch_array($result);
	  return $row['nb'];
  }	
}
//retroune le nombre de date avec ateliers valid�s par session pour comparaison avec  le nombre ci-dessus
function getDatesValidesbysession($idsession)
{
$sql="SELECT COUNT(  `id_datesession` ) as nb
FROM  `tab_session_dates` 
WHERE  `statut_datesession` =1
AND  `id_session` =".$idsession." ";
$db=opendb();
 $result = mysqli_query($db,$sql);
  closedb($db);
  if (FALSE == $result)
  {
	
	return FALSE ;
  }
  else
  {
      $row=mysqli_fetch_array($result);
	  return $row['nb'];
  }	


}

// retourne toutes les dates d'une session
function getDatesSession($id)
{
$sql="SELECT * FROM `tab_session_dates` WHERE `id_session`=".$id." ORDER BY `date_session` ASC ";
$db=opendb();
 $result = mysqli_query($db,$sql);
  closedb($db);
  if (FALSE == $result)
  {
	
	  return FALSE ;
  }
  else
  {
      return $result;
  }	

}

//retourne le nombre de dates annul�es das une sesison
function getDatesAnnulebysession($idsession)
{
$sql="SELECT count( `id_datesession` ) AS nb
FROM `tab_session_dates`
WHERE `id_session` =".$idsession."
AND `statut_datesession` =2";
$db=opendb();
 $result = mysqli_query($db,$sql);
  closedb($db);
  if (FALSE == $result)
  {
	
	  return FALSE ;
  }
  else
  {
      $row=mysqli_fetch_array($result);
      return $row["nb"];
  }	


}

//function retourne le nombre de dates enregistr�es dans tab_session
function getSessionNbreDates($idsession)
{
$sql="SELECT `nbre_dates_sessions` FROM `tab_session` WHERE `id_session`=".$idsession."";
$db=opendb();
 $result = mysqli_query($db,$sql);
  closedb($db);
  if (FALSE == $result)
  {
	
	  return FALSE ;
  }
  else
  {
      $row=mysqli_fetch_array($result);
      return $row["nbre_dates_sessions"];
  }	

}
// changer le statut d'une date d'une session 0= en cours 1= valid�e
function updateDatesessionStatut($iddate)
{
$sql="UPDATE `tab_session_dates` SET `statut_datesession`=1 WHERE `id_datesession`=".$iddate;
$db=opendb();
 $result = mysqli_query($db,$sql);
  closedb($db);
  if (FALSE == $result)
  {
	
	  return FALSE ;
  }
  else
  {
      return True;
  }	
}

//Retourne une date d'une session par son numero
function getDatebyNumero($num)
{
$sql="SELECT date_session FROM tab_session_dates WHERE id_datesession=".$num;
$db=opendb();
 $result = mysqli_query($db,$sql);
  closedb($db);
  if (FALSE == $result)
  {
	
	  return FALSE ;
  }
  else
  {
      $row=mysqli_fetch_array($result);
      return $row["date_session"];
  }	

}

// Modifsession()
// Modifie les param�tres de la session
function modifSession($id,$dates,$nom,$nbplace,$nbre_dates_sessions,$anim,$salle,$tarif)
{
 //echo $id,$dates,$nom,$heure,$nbplace,$nbre_dates_sessions,$session_dates ;
  $sql = "UPDATE `tab_session`
			SET `date_session` ='".$dates."',
			`nom_session` ='".$nom."',
			`nbplace_session` ='".$nbplace."',
			`nbre_dates_sessions` ='".$nbre_dates_sessions."',
			
			`id_anim` ='".$anim."',
			 `id_salle`= '".$salle."',
			 `id_tarif`= '".$tarif."'
	 WHERE `id_session` =".$id." 
	 ";
  
  $db=opendb();
 $result = mysqli_query($db,$sql);
  closedb($db);
  if (FALSE == $result)
  {
 // echo "modif dans la table operation echou�e";
      return FALSE ;
  }
  else
  {
      return $result;
  }
}


//
// delSession()
// supprime un atelier
function delSession($id)
{
  $sql = "DELETE FROM `tab_session` WHERE `id_session`=".$id ;
  //enlever aussi les dates dans la tab_session_dates
  $sql2="DELETE FROM `tab_session_dates` WHERE `id_session`=".$id ;
  
  $db=opendb();
 $result = mysqli_query($db,$sql);
 $result2 = mysqli_query($db,$sql2);
  closedb($db);
  if (FALSE == $result)
  {
      return FALSE ;
  }
  else
  {
      return $result;
  }
}

//////////////*****************FIN SESSIONS*************//////////////

function getDay($nb)
{
    switch ($nb)
    {
        case "0":
            $day = "Dimanche";
        break;
        case "1":
            $day ="Lundi";
        break;
        case "2":
            $day ="Mardi";
        break;
        case "3":
            $day ="Mercredi";
        break;
        case "4":
            $day ="Jeudi";
        break;
        case "5":
            $day ="Vendredi";
        break;
        case "6":
            $day ="Samedi";
        break;
        
    }   
    return $day;
}




/////FONCTIONS SUR LES SEMAINES////////
//
// fonction qui renvoi les dates de comparaisons pour la semaine
function get_lundi_dimanche_from_week($week)
{
$week= $week-1;
$year= date('Y');

for ($i = 1 ; $i < 8; $i++)
	{
	if (date ('D' ,mktime(0, 0, 0, 1, $i, $year)) == 'Mon')
	$lundi = mktime(0, 0, 0, 1, $i, $year);
	}
$dimanche = $lundi + (60 * 60 * 24 * 6);
$lundi = $lundi + (60 * 60 * 24 * 7 * ($week - 1));
$dimanche = $dimanche + (60 * 60 * 24 * 7 * ($week - 1));

return (Array($lundi, $dimanche));
}



function getDaySemaine($d,$weekday,$dayYear)
{
	if ($d>$weekday){
		$day=$dayYear+($d-$weekday); //jour suivants
	}elseif ($d == $weekday){
		$day=$dayYear ; //jour choisi m�me jour qu'aujourd'hui
	}else{
		$day=$dayYear-($weekday-$d); // jour precedents
	}
return $day	;
}
 
function numero_semaine($jour,$mois,$annee)
{
    /*
     * Norme ISO-8601:
     * - La semaine 1 de toute ann�e est celle qui contient le 4 janvier ou que la semaine 1 de toute ann�e est celle qui contient le 1er jeudi de janvier.
     * - La majorit� des ann�es ont 52 semaines mais les ann�es qui commence un jeudi et les ann�es bissextiles commen�ant un mercredi en poss�de 53.
     * - Le 1er jour de la semaine est le Lundi
     */ 
    
    // D�finition du Jeudi de la semaine
    if (date("w",mktime(12,0,0,$mois,$jour,$annee))==0) // Dimanche
        $jeudiSemaine = mktime(12,0,0,$mois,$jour,$annee)-3*24*60*60;
    else if (date("w",mktime(12,0,0,$mois,$jour,$annee))<4) // du Lundi au Mercredi
        $jeudiSemaine = mktime(12,0,0,$mois,$jour,$annee)+(4-date("w",mktime(12,0,0,$mois,$jour,$annee)))*24*60*60;
    else if (date("w",mktime(12,0,0,$mois,$jour,$annee))>4) // du Vendredi au Samedi
        $jeudiSemaine = mktime(12,0,0,$mois,$jour,$annee)-(date("w",mktime(12,0,0,$mois,$jour,$annee))-4)*24*60*60;
    else // Jeudi
        $jeudiSemaine = mktime(12,0,0,$mois,$jour,$annee);
    
    // D�finition du premier Jeudi de l'ann�e
    if (date("w",mktime(12,0,0,1,1,date("Y",$jeudiSemaine)))==0) // Dimanche
    {
        $premierJeudiAnnee = mktime(12,0,0,1,1,date("Y",$jeudiSemaine))+4*24*60*60;
    }
    else if (date("w",mktime(12,0,0,1,1,date("Y",$jeudiSemaine)))<4) // du Lundi au Mercredi
    {
        $premierJeudiAnnee = mktime(12,0,0,1,1,date("Y",$jeudiSemaine))+(4-date("w",mktime(12,0,0,1,1,date("Y",$jeudiSemaine))))*24*60*60;
    }
    else if (date("w",mktime(12,0,0,1,1,date("Y",$jeudiSemaine)))>4) // du Vendredi au Samedi
    {
        $premierJeudiAnnee = mktime(12,0,0,1,1,date("Y",$jeudiSemaine))+(7-(date("w",mktime(12,0,0,1,1,date("Y",$jeudiSemaine)))-4))*24*60*60;
    }
    else // Jeudi
    {
        $premierJeudiAnnee = mktime(12,0,0,1,1,date("Y",$jeudiSemaine));
    }
        
    // D�finition du num�ro de semaine: nb de jours entre "premier Jeudi de l'ann�e" et "Jeudi de la semaine";
    $numeroSemaine =     ( 
                    ( 
                        date("z",mktime(12,0,0,date("m",$jeudiSemaine),date("d",$jeudiSemaine),date("Y",$jeudiSemaine))) 
                        -
                        date("z",mktime(12,0,0,date("m",$premierJeudiAnnee),date("d",$premierJeudiAnnee),date("Y",$premierJeudiAnnee))) 
                    ) / 7 
                ) + 1;
    
    // Cas particulier de la semaine 53
    if ($numeroSemaine==53)
    {
        // Les ann�es qui commence un Jeudi et les ann�es bissextiles commen�ant un Mercredi en poss�de 53
        if (date("w",mktime(12,0,0,1,1,date("Y",$jeudiSemaine)))==4 || (date("w",mktime(12,0,0,1,1,date("Y",$jeudiSemaine)))==3 && date("z",mktime(12,0,0,12,31,date("Y",$jeudiSemaine)))==365))
        {
            $numeroSemaine = 53;
        }
        else
        {
            $numeroSemaine = 1;
        }
    }
        
    //echo $jour."-".$mois."-".$annee." (".date("d-m-Y",$premierJeudiAnnee)." - ".date("d-m-Y",$jeudiSemaine).") -> ".$numeroSemaine."<BR>";
            
    return sprintf("%02d",$numeroSemaine);
} 

///********************FIN SEMAINES**************///


/// ***********FONCTIONS SUR LES IMPRESSIONS ****************///

/// retourne l'id de l'utilisateur externe utilis� pour encaisser les impressions sans adhesion MAJ 0.95
function getIduserexterne()
{
$sql="SELECT `id_user` FROM `tab_user` WHERE `nom_user`='Externe' AND `login_user`='compte_imprim' ";
$db=opendb();
   $result = mysqli_query($db,$sql);
	closedb;
if ($result==FALSE)
	{
      return FALSE;
	}
    else
	{
	$row=mysqli_fetch_array($result);
     return $row['id_user'] ;
	}	

}


function getinfouseriprim($iduser)
{
$sql="SELECT print_date,print_credit,print_tarif,print_statut, nom_user, prenom_user,print_debit
		FROM tab_print, tab_user
		WHERE print_user ='".$iduser."'
		AND tab_print.print_user=tab_user.id_user
		ORDER BY print_date DESC LIMIT 1;
	";
	$db=opendb();
   $result = mysqli_query($db,$sql);
	closedb;
if ($result==FALSE)
	{
      return FALSE;
	}
    else
	{
	$row=mysqli_fetch_array($result);
     return $row ;
	}	
}

//**Selectionner les adherents qui impriment
function getPrintingUsers()
{
$sql="SELECT DISTINCT(`print_user`) FROM `tab_print` ";
$db=opendb();
   $result = mysqli_query($db,$sql);
	closedb;
if ($result==FALSE)
	{
      return FALSE;
	}
    else
	{
	
     return $result ;
	}

}

//selectionner les adh�rents dont le solde est cr�diteur
function getPrintingUserswithcredit()
{
$sql="SELECT  `print_user` , SUM(`print_credit`) AS credit, SUM(  `print_debit` *  `donnee_tarif` ) AS donnee
FROM  `tab_print` , tab_tarifs
WHERE tab_tarifs.`id_tarif` = tab_print.print_tarif
GROUP BY  `print_user`
HAVING (credit-donnee) > 0
";
$db=opendb();
   $result = mysqli_query($db,$sql);
	closedb;
if ($result==FALSE)
	{
      return FALSE;
	}
    else
	{
	
     return $result ;
	}

}

function getAllUserPrintCredit($tarif)
{
$sql="SELECT print_user, SUM(`print_debit`) AS debit, nom_user, prenom_user
	FROM tab_print, tab_user
	WHERE print_user.tab_print=id_user.tab_user
	AND print_tarif='".$tarif."'
	group by print_user
	ORDER BY print_date DESC
";
$db=opendb();
 $result = mysqli_query($db,$sql);
   closedb($db);
  if (FALSE == $result)
  {
      return FALSE;
  }
  else
  {
      return $result;
  }

}
// retrouver les impression classee par tarif par user
function getPrintbyTarif($id,$date,$tarif)
{
$sql="SELECT id_print,print_debit, print_tarif,donnee_tarif,nom_tarif
	FROM tab_print, tab_tarifs
	WHERE print_user='".$id."' 
	AND print_date='".$date."'
	AND print_tarif='".$tarif."'
	AND tab_print.print_tarif = tab_tarifs.id_tarif
	";


 $db=opendb();
 $result = mysqli_query($db,$sql);
   closedb($db);
  if (FALSE == $result)
  {
      return FALSE;
  }
  else
  {
    $print= mysqli_fetch_array($result);
     return $print ;
  }
}

///// retrouver la transaction
function getPrintFromID($id)
{
$sql="SELECT * FROM tab_print WHERE id_print='".$id."' 
";
$db=opendb();
 $result = mysqli_query($db,$sql);
   closedb($db);
  if (FALSE == $result)
  {
      return FALSE;
  }
  else
  {
    
     return $result ;
  }

}
/// retourne les utilisateurs qui n'ont pas pay�'
function getPrintingUserswithdebt()
{
$sql="SELECT `print_user`,`print_statut`,`print_debit`,`print_tarif`,`print_date`, (`print_debit`*donnee_tarif) AS debit 
FROM `tab_print`,tab_tarifs 
WHERE id_tarif=`print_tarif` 
AND `print_statut`=0 GROUP BY print_user 
ORDER BY `print_date` DESC";
$db=opendb();
 $result = mysqli_query($db,$sql);
   closedb($db);
  if (FALSE == $result)
  {
      return FALSE;
  }
  else
  {
    
     return $result ;
  }

}

///retourne tous les utilisateurs qui impriment
function getAllUserPrint()
{
    $sql="SELECT print_user, nom_user, prenom_user, print_date,print_credit, print_tarif, print_statut, print_debit
		FROM tab_print, tab_user
		WHERE id_user=print_user
		AND DATE(print_date)=CURDATE()
		AND print_statut<2
		order BY print_date DESC";
  
  $db=opendb();
 $result = mysqli_query($db,$sql);
   closedb($db);
	 $nb=mysqli_num_rows($result);
  if ($nb==0)
  {
      return FALSE;
  }
  else
  {
      return $result;
  }
}


///retourne les impression d'hier

function getYesterdayPrints($date)
{
$sql="
SELECT SUM(`print_debit`) AS credit
FROM tab_print
WHERE print_date='".$date."'
";
$db=opendb();
 $result = mysqli_query($db,$sql);
   closedb($db);
  if (FALSE == $result)
  {
      return FALSE;
  }
  else
  {
      return $result;
  }

}

///********FIN IMPRESSIONS ***** //////////////////////////////////////////////

///*************FONCTION SUR LE MESSAGE BOARD **** ********************

function addMessage($date, $id_user, $message,$tags,$destinataire)
{
$sql = "INSERT INTO `tab_messages` (`id_messages`, `mes_date`, `mes_auteur`, `mes_txt`, `mes_tag`, `mes_destinataire`)
		VALUES ('','".$date."','".$id_user."','".$message."','".$tags."','".$destinataire."')" ;
  $db=opendb();
 $result = mysqli_query($db,$sql);
  closedb($db);
  if (FALSE == $result)
  {
	
	  return FALSE ;
  }
  else
  {
      return TRUE;
  }

}

function readMessage()
{
$jour=date('Y-m')."-01".date('H:i:s');

$sql="SELECT *
	from tab_messages
	WHERE mes_date BETWEEN '".$jour."' AND NOW()
	ORDER BY mes_date DESC ;
	";
	
$db=opendb();
 $result = mysqli_query($db,$sql);
  closedb($db);
  if (FALSE == $result)
  {
	
	  return FALSE ;
  }
  else
  {
      return $result;
  }
}

//retourne la liste des usages ayant poste pour la reponse de lanimateur
function getListReponse($anim)
{
$sql="SELECT `mes_auteur`, nom_user, prenom_user,id_user FROM `tab_messages`, tab_user 
WHERE `mes_destinataire`='".$anim."' 
AND mes_auteur=id_user
ORDER BY `mes_auteur`";
$db=opendb();
 $result = mysqli_query($db,$sql);
  closedb($db);
  if (FALSE == $result)
  {
	
	  return FALSE ;
  }
  else
  {
     $auteur = array();
        $nb= mysqli_num_rows($result);
        for ($i=1;$i<=$nb;$i++)
        {
            $row = mysqli_fetch_array($result);
            $auteur[$row["id_user"]] = $row["nom_user"]." ".$row["prenom_user"] ;
        }
        return $auteur ;
	 
  }

}

// pour la admin envoi a tous les anim + adherents ayant poste
function getListRepAdmin()
{
$sql="SELECT `mes_auteur` , `nom_user` , `prenom_user` , `status_user`
FROM `tab_messages` , tab_user
WHERE `mes_auteur` = id_user
UNION
SELECT `id_user` , `nom_user` , `prenom_user` , `status_user`
FROM tab_user
WHERE `status_user` =3
OR `status_user` =4
ORDER BY `status_user` ASC ";
$db=opendb();
 $result = mysqli_query($db,$sql);
  closedb($db);
  if (FALSE == $result)
  {
	
	  return FALSE ;
  }
  else
  {
     $auteur = array();
        $nb= mysqli_num_rows($result);
        for ($i=1;$i<=$nb;$i++)
        {
            $row = mysqli_fetch_array($result);
            $auteur[$row["mes_auteur"]] = $row["nom_user"]." ".$row["prenom_user"] ;
        }
        return $auteur ;
	 
  }

}

///***********************************************************


//
// renvoi si il y a atelier et le nom de l'atelier

function checkDayAtelier($j,$m,$year)
{
   
  $sql = "SELECT `date_atelier`,label_atelier, id_atelier
          FROM `tab_atelier`,tab_atelier_sujet
          WHERE tab_atelier.id_sujet=tab_atelier_sujet.id_sujet
		  AND date_atelier='".$year."-".$m."-".$j."' ";
  
  $db=opendb();
  $result = mysqli_query($db,$sql);
   closedb($db);
  
  if($result == FALSE)
    {
        return FALSE;
    }
    else
    {
        $row = mysqli_fetch_array($result);
		return $row;
	}
}

//fonction qui affiche les dates des sessions.  ---> a faire

//renvoi la liste des mois o� les ateliers sont programm�s pour le calendrier

function ListMoisAtelier()
{
$sql="SELECT `date_atelier`
FROM `tab_atelier`
ORDER BY `date_atelier` ASC
";
$db=opendb();
  $result = mysqli_query($db,$sql);
   closedb($db);
  
     if ($result == FALSE )
    {
        return FALSE ;
    }
    else
    {
        return $result ;
    }
}

function getSessionValid($id)
{
$sql="SELECT `id_programmation` FROM `tab_session_stat` WHERE `id_session`='".$id."' ";
	
	$db=opendb();
	$result = mysqli_query($db,$sql);
	 closedb($db);
  
    if (FALSE == $result)
  {
      return FALSE ;
  }
  else
  {
      $nb = mysqli_fetch_array($result) ;
      return $nb ;
  }

}

//Pour les stat, validation des pr�sents
function InsertStatAS($type,$id,$date,$inscrits,$presents,$absents,$attente,$nbplace,$categorie,$statut,$anim,$epn)
{
$sql="INSERT INTO `tab_as_stat`(`id_stat`, `type_AS`, `id_AS`, `date_AS`, `inscrits`, `presents`, `absents`, `attente`, `nbplace`, `id_categorie`, `statut_programmation`, `id_anim`, `id_epn`) 
VALUES ('','".$type."','".$id."','".$date."','".$inscrits."','".$presents."', '".$absents."','".$attente."','".$nbplace."','".$categorie."','".$statut."','".$anim."','".$epn."') ";

	$db=opendb();
 $result = mysqli_query($db,$sql);
  closedb($db);
  if (FALSE == $result)
  {
      return FALSE ;
  }
  else
  {
      return True;
  }
}

//modifier les nombres statistiuques, apr�s modif par les archives
function ModifStatAS($inscrit,$present,$absents,$idatelier,$type)
{
$sql="UPDATE `tab_as_stat` SET `inscrits`=".$inscrit.",`presents`=".$present.",`absents`=".$absents." WHERE `type_AS`='".$type."' AND`id_AS`=".$idatelier;
	$db=opendb();
 $result = mysqli_query($db,$sql);
  closedb($db);
  if (FALSE == $result)
  {
      return FALSE ;
  }
  else
  {
      return True;
  }


}


//
//retourne le nombre d'inscrit et le nombre en attente pour une session
function getInscritpersession($idsession,$iddate)
{
$sql="SELECT count( `id_user` ) AS nb1 FROM `rel_session_user`WHERE `status_rel_session` =0 AND `id_session` =".$idsession." AND `id_datesession` =".$iddate."";
$sql1="SELECT count( `id_user` ) AS nb2 FROM `rel_session_user`WHERE `status_rel_session` =2 AND `id_session` =".$idsession." AND `id_datesession` =".$iddate."";
$db=opendb();
 $result = mysqli_query($db,$sql);
  $result1 = mysqli_query($db,$sql1);
  closedb($db);
  if ((FALSE == $result) OR  (FALSE == $result1))
  {
      return FALSE ;
  }
  else
  {
	$row=mysqli_fetch_array($result);
	$row1=mysqli_fetch_array($result1);
	$res=array();
	$res[0]=$row['nb1'];
	$res[1]=$row1['nb2'];
      return $res;
  }

}

// supression d'un sujet d'atelier de la base
function delSujetAtelier($id)
{

$sql = "DELETE FROM `tab_atelier_sujet` WHERE `id_sujet`='".$id."'
	" ;
    $db=opendb();
   $result = mysqli_query($db,$sql);
    closedb($db);
    if (FALSE == $result)
    {
        return FALSE ;
    }
    else
    {
        return TRUE;
    }
}

//////////
/// Gestion des tarifs //////


/// tarifs impressions

function newTarif($nomtarif, $prixtarif, $comment,$nbatelier,$categoryTarif, $duree, $epn)
{

$sql = "INSERT INTO `tab_tarifs`(`id_tarif`, `nom_tarif`, `donnee_tarif`, `comment_tarif`, `nb_atelier_forfait`, `categorie_tarif`, `duree_tarif`, `epn_tarif`)
		VALUES ('','".$nomtarif."','".$prixtarif."','".$comment."','".$nbatelier."','".$categoryTarif."','".$duree."','".$epn."')" ;

	$db=opendb();
 $result = mysqli_query($db,$sql);
  closedb($db);
  if (FALSE == $result)
  {
      return FALSE ;
  }
  else
  {
      return True;
  }
}
// renvoie le test sur l'existence de tarifs pour les ateliers
function TestTarifs()
{
$sql="SELECT count(`id_tarif`) as nb FROM `tab_tarifs` WHERE `categorie_tarif`=5";
$db=opendb();
$result = mysqli_query($db,$sql);
closedb($db);
  if (FALSE == $result)
  {
      return FALSE ;
  }
  else
  {
	  $row=mysqli_fetch_array($result);
      return $row["nb"];
  }	
	
}
function modifTarif($id,$nomtarif, $prixtarif, $descriptiontarif,$forfait,$categorieTarif,$duree,$epn)
{
$sql="UPDATE `tab_tarifs` 
	SET  nom_tarif= '".$nomtarif."',
	donnee_tarif='".$prixtarif."',
	comment_tarif='".$descriptiontarif."',
	nb_atelier_forfait='".$forfait."',
	categorie_tarif='".$categorieTarif."',
	duree_tarif= '".$duree."',
	epn_tarif='".$epn."'
	WHERE id_tarif='".$id."' LIMIT 1 ";
$db=opendb();
 $result = mysqli_query($db,$sql);
  closedb($db);
  if (FALSE == $result)
  {
      return FALSE ;
  }
  else
  {
      return True;
  }

}

function deletarif($id)
{
$sql="DELETE FROM `tab_tarifs` WHERE `id_tarif`='".$id."' ";
$db=opendb();
 $result = mysqli_query($db,$sql);
  closedb($db);
  if (FALSE == $result)
  {
      return FALSE ;
  }
  else
  {
      return True;
  }
}

// retourne le prix et le nom du tarif � partir du tarif s�lectionn�
function getPrixFromTarif($id)
{

$sql=" SELECT `donnee_tarif`,`nom_tarif`
FROM `tab_tarifs`
WHERE `id_tarif`='".$id."'
 ";
$db=opendb();
$result = mysqli_query($db,$sql);
  closedb($db);
  if (FALSE == $result)
  {
      return FALSE ;
  }
  else
  {
	
    return $result ;
  }



}


function getAllTarifs()
{

$sql=" SELECT * 
FROM  `tab_tarifs` 
WHERE id_tarif > 1
ORDER BY `categorie_tarif` ASC";
$db=opendb();
$result = mysqli_query($db,$sql);
  closedb($db);
  if (FALSE == $result)
  {
      return FALSE ;
  }
  else
  {
     
        return $result ;
  }
}


function getTarifs($cat)
{
$sql="SELECT * FROM `tab_tarifs` WHERE `categorie_tarif`='".$cat."' AND `id_tarif`>1 ORDER BY `id_tarif` ASC";
	$db=opendb();
	$result = mysqli_query($db,$sql);
	closedb($db);
	  if (FALSE == $result)
	  {
	      return FALSE ;
	  }
	  else
	  {
	   //  $row = mysqli_fetch_array($result);
		return $result ;
	  }
}


function getTarifsbyCat($t)
{

$sql=" SELECT id_tarif, nom_tarif,donnee_tarif
FROM  `tab_tarifs` 
WHERE  `categorie_tarif` ='".$t."'
ORDER BY `id_tarif` ASC";
$db=opendb();
$result = mysqli_query($db,$sql);
  closedb($db);
  if (FALSE == $result)
  {
      return FALSE ;
  }
  else
  {
      $tarif = array();
        $nb= mysqli_num_rows($result);
        for ($i=1;$i<=$nb;$i++)
        {
            $row = mysqli_fetch_array($result);
            $tarif[$row["id_tarif"]] = $row["nom_tarif"]." (".$row["donnee_tarif"]." &euro;)";
        }
        return $tarif ;
  }
}

function getNomTarif($id)
{
$sql="SELECT `nom_tarif` FROM `tab_tarifs` WHERE `id_tarif`=".$id;
$db=opendb();
$result = mysqli_query($db,$sql);
  closedb($db);
  if (FALSE == $result)
  {
      return FALSE ;
  }
  else
  {
	$row=mysqli_fetch_array($result);
	return $row["nom_tarif"];
	}

}

///******************Enregistrement des transactions ************///
function addAdhesion($date,$type_transac,$id_user,$adhesiontarif, $statutp){
$sql="INSERT INTO `tab_transactions`(`id_transac`, `id_user`,`type_transac`, `id_tarif`, `nbr_forfait`,`date_transac`, `status_transac`) 
	VALUES ('','".$id_user."','".$type_transac."','".$adhesiontarif."','1','".$date."','".$statutp."')
	";

$db=opendb();
$result = mysqli_query($db,$sql);

  closedb($db);
   if (FALSE == $result)
	  {
	      return FALSE ;
	  }
	  else
	  {
	   return TRUE;
	  }

}

function Testpaiement($adh)
{
$sql="SELECT * FROM  `tab_transactions` WHERE `status_transac`=0 AND `id_user`=".$adh;
$db=opendb();
$result = mysqli_query($db,$sql);
  closedb($db);
   if (FALSE == $result)
	  {
	      return FALSE ;
	  }
	  else
	  {
	  
	   return $result;
	  }

}
//
///DEPRECATED
/*
function updateAdhesion($transac,$date,$id_user,$adhesiontarif, $statutp)
{
$sql="UPDATE `tab_transactions` SET `date_transac`='".$date."',`status_transac`=".$statutp."
		WHERE `id_transac`=".$transac;
$db=opendb();
$result = mysqli_query($db,$sql);
  closedb($db);
   if (FALSE == $result)
	  {
	      return FALSE ;
	  }
	  else
	  {
	  
	   return TRUE;
	  }



}

*/
////

/**
//Modification de la situation de l'adh�rent apr�s renouvellement adh�sion
*/
function modifUserStatut($iduser,$statut, $daterenouv, $adhesiontarif)
{
$sql="UPDATE `tab_user` 
SET `status_user`=".$statut." ,
	`tarif_user`=".$adhesiontarif.",
	`dateRen_user`='".$daterenouv."'

WHERE  `id_user`=".$iduser." ";

$db=opendb();
$result = mysqli_query($db,$sql);
  closedb($db);
   if (FALSE == $result)
	  {
	      return FALSE ;
	  }
	  else
	  {
	  
	   return TRUE;
	  }


}

////***** FONCTIONS SUR LA GESTION MULTIESPACE ***/////

//
// getConsole()
// recupere les postes par salle
function getConsole($numsalle)
{
    $sql="SELECT `nom_computer`, `id_computer` FROM tab_computer WHERE id_salle=".$numsalle." ORDER BY nom_computer;";
	
  $db=opendb();
 $result = mysqli_query($db,$sql);
   closedb($db);
  if (FALSE == $result)
  {
      return FALSE;
  }
  else
  {
      return $result;
  }
}

//
// getConsoleoccup()
// recupere les postes par salle
function getConsoleoccup($numsalle)
{
    $sql="SELECT `nom_computer`, `id_computer`, `nom_user`, `prenom_user`, `dateresa_resa`, `debut_resa` FROM tab_user, tab_computer, tab_resa WHERE tab_resa.id_user_resa=tab_user.id_user AND tab_resa.id_computer_resa=tab_computer.id_computer AND tab_computer.id_salle=".$numsalle." AND tab_resa.status_resa=1 ORDER BY `id_computer`;";
	
  $db=opendb();
 $result = mysqli_query($db,$sql);
   closedb($db);
  if (FALSE == $result)
  {
      return FALSE;
  }
  else
  {
      return $result;
  }
}

//
// getAllSalle()
// recupere les salless
function getAllSalle()
{
    $sql="SELECT * FROM tab_salle;";
	
  $db=opendb();
 $result = mysqli_query($db,$sql);
   closedb($db);
  if (FALSE == $result)
  {
      return FALSE;
  }
  else
  {
      return $result;
  }
}

//
// getSalle()
// recupere les salless
function getSalle($numsalle)
{
    $sql="SELECT * FROM tab_salle WHERE id_salle=".$numsalle.";";
	
  $db=opendb();
 $result = mysqli_query($db,$sql);
   closedb($db);
  if (FALSE == $result)
  {
      return FALSE;
  }
  else
  {
      return $result;
  }
}

//
// addSalle()
// ajoute une salle dans la table salle
function addSalle($nom, $espace,$comment)
{
  $sql = "INSERT INTO `tab_salle` (`id_salle`,`nom_salle`,`id_espace`,`comment_salle`)
         VALUES ('','".$nom."', '".$espace."', '".$comment."') " ;
  $db=opendb();
 $result = mysqli_query($db,$sql);
  $lastid = mysqli_insert_id($db);
  closedb($db);
  if (FALSE == $result)
  {
      return FALSE ;
  }
  else
  {
      return $lastid;
  }
}

//
// modSalle()
// modifie une salle dans la table salle
function modSalle($id,$nom, $espace, $comment)
{
    $sql="UPDATE `tab_salle`
    SET `nom_salle` ='".$nom."',
        `id_espace` ='".$espace."',
        `comment_salle` ='".$comment."'
     WHERE `id_salle` =".$id." LIMIT 1 ";
  $db=opendb();
 $result = mysqli_query($db,$sql);
   closedb($db);
  if (FALSE == $result)
  {
      return FALSE;
  }
  else
  {
      return TRUE;
  }
}

//
// supSalle ()
// supprime une salle de la table salle
function supSalle($id)
{
  $sql = "DELETE FROM `tab_salle` WHERE `id_salle` = ".$id." LIMIT 1" ;
  $db=opendb();
 $result = mysqli_query($db,$sql);
   closedb($db);
  if (FALSE == $result)
  {
      return FALSE;
  }
  else
  {
      return TRUE;
  }
}

//
// getEspace()
// recupere les espaces
function getEspace($numespace)
{
    $sql="SELECT * FROM tab_espace WHERE id_espace=".$numespace." ";
	
  $db=opendb();
 $result = mysqli_query($db,$sql);
   closedb($db);
  if (FALSE == $result)
  {
      return FALSE;
  }
  else
  {
      return $result;
  }
}

//
//recuperer l'activation des forfaits pour l'epn
//
function getActivationForfaitEpn($id)
{
	$sql="SELECT `activer_console`,`inscription_usagers_auto`, `message_inscription`, `activation_forfait` FROM `tab_config` WHERE id_espace='".$id."' ";
	
  $db=opendb();
 $result = mysqli_query($db,$sql);
   closedb($db);
  if (FALSE == $result)
  {
      return FALSE;
  }
  else
  {
      $row=mysqli_fetch_array($result);
			return $row;
  }
	
}


//les param�tres config supplementaires dans tab_config pour la console
//
function updateConfigForfait($epn,$activerforfait,$inscription_auto,$message_inscrip)
{
	$sql="UPDATE `tab_config` 
	SET `inscription_usagers_auto`='".$inscription_auto."',
	`message_inscription`='".$message_inscrip."',
	`activation_forfait`='".$activerforfait."' 
	WHERE `id_espace`='".$epn."'";
	
	 $db=opendb();
 $result = mysqli_query($db,$sql);
   closedb($db);
  if (FALSE == $result)
  {
      return FALSE;
  }
  else
  {
      return TRUE;
  }
	
}


//
// getAllEspace()
// recupere les espaces
function getAllEspace()
{
    $sql="SELECT `nom_espace`, `id_espace`, `id_city`, `adresse`, mail_espace FROM tab_espace ORDER BY nom_espace ASC";
	
  $db=opendb();
 $result = mysqli_query($db,$sql);
   closedb($db);
  if (FALSE == $result)
  {
      return FALSE;
  }
  else
  {
      return $result;
  }
}

function getAllEPN()
{
 $sql = "SELECT `id_espace`, `nom_espace` FROM `tab_espace`  ORDER BY `nom_espace` asc" ;
    $db=opendb();
   $result = mysqli_query($db,$sql);
    closedb($db);
    if (FALSE == $result)
    {
        return FALSE ;
    }
    else
    {
        $epn = array();
        $nb= mysqli_num_rows($result);
        for ($i=1;$i<=$nb;$i++)
        {
            $row = mysqli_fetch_array($result);
            $epn[$row["id_espace"]] = $row["nom_espace"] ;
        }
        return $epn ;
    }


}

//retourne les noms des salles de la base
function getAllsalles()
{
 $sql = "SELECT `id_salle`, `nom_salle` FROM `tab_salle`  ORDER BY `id_salle`" ;
    $db=opendb();
   $result = mysqli_query($db,$sql);
    closedb($db);
    if (FALSE == $result)
    {
        return FALSE ;
    }
    else
    {
        $epn = array();
        $nb= mysqli_num_rows($result);
        for ($i=1;$i<=$nb;$i++)
        {
            $row = mysqli_fetch_array($result);
            $epn[$row["id_salle"]] = $row["nom_salle"] ;
        }
        return $epn ;
    }


}


//retourne la liste des salles par epn
function getAllSallesbyepn($epn)
{
	$sql = "SELECT `id_salle`, `nom_salle` FROM `tab_salle`  
					WHERE id_espace='".$epn."'
		
	ORDER BY `id_salle`" ;
    $db=opendb();
   $result = mysqli_query($db,$sql);
    closedb($db);
    if (FALSE == $result)
    {
        return FALSE ;
    }
    else
    {
        $epn = array();
        $nb= mysqli_num_rows($result);
        for ($i=1;$i<=$nb;$i++)
        {
            $row = mysqli_fetch_array($result);
            $epn[$row["id_salle"]] = $row["nom_salle"] ;
        }
        return $epn ;
    }
	
	
	
}



//
// addEspace()
// ajoute un EPN dans la table espace
function addEspace($nom, $adresse,$ville,$tel,$fax,$logoespace,$couleur,$mail)
{
  $sql = "INSERT INTO `tab_espace` (`id_espace`,`nom_espace`,`id_city`,`adresse`,`tel_espace`, `fax_espace`,`logo_espace`,`couleur_espace`,`mail_espace`)
         VALUES ('','".$nom."', '".$ville."', '".$adresse."', '".$tel."','".$fax."','".$logoespace."', '".$couleur."','".$mail."') " ;
  $db=opendb();
 $result = mysqli_query($db,$sql);
  $lastid = mysqli_insert_id($db);
  closedb($db);
  if (FALSE == $result)
  {
      return FALSE ;
  }
  else
  {
	
	return $lastid;
  }
}
//insertion des horaires du nouvel epn en copy de celui par d�faut
function copyhoraires($idespace){
$sql=" INSERT INTO `tab_horaire`(`id_horaire`, `jour_horaire`, `hor1_begin_horaire`, `hor1_end_horaire`, `hor2_begin_horaire`, `hor2_end_horaire`, `unit_horaire`, `id_epn`) 
SELECT '', `jour_horaire`, `hor1_begin_horaire`, `hor1_end_horaire`, `hor2_begin_horaire`, `hor2_end_horaire`, `unit_horaire`, '".$idespace."' FROM `tab_horaire` WHERE `id_epn`=1
";
$db=opendb();
 $result = mysqli_query($db,$sql);
  closedb($db);
  if (FALSE == $result)
  {
      return FALSE ;
  }
  else
  {
	return TRUE ;
  }
}

function copyconfig($idespace,$forfait){
$sql="INSERT INTO `tab_config`(`id_config`, `activer_console`, `name_config`, `unit_default_config`, `unit_config`, `maxtime_config`, `maxtime_default_config`, `id_espace`, `inscription_usagers_auto`, `message_inscription`, `activation_forfait`, `nom_espace`,`resarapide`, `duree_resarapide`)
SELECT '',`activer_console`, `name_config`, `unit_default_config`, `unit_config`, `maxtime_config`, `maxtime_default_config`,'".$idespace."', `inscription_usagers_auto`, `message_inscription`,'".$forfait."',`nom_espace`,`resarapide`, `duree_resarapide` FROM `tab_config` WHERE `id_espace`=1
";

$db=opendb();
 $result = mysqli_query($db,$sql);
  closedb($db);
  if (FALSE == $result)
  {
      return FALSE ;
  }
  else
  {
	return TRUE ;
  }

}

//
// modEspace()
// modifie un espace dans la table espace
function modEspace($id,$nom, $adresse, $city,$tel, $fax, $logoespace,$couleur,$mail)
{
    $sql="UPDATE `tab_espace`
    SET `nom_espace` ='".$nom."',
        `id_city` ='".$city."',
        `adresse` ='".$adresse."',
				`tel_espace`= '".$tel."',
				`fax_espace`= '".$fax."',
	`logo_espace`='".$logoespace."',
	`couleur_espace`='".$couleur."',
	`mail_espace`='".$mail."'
     WHERE `id_espace` =".$id." LIMIT 1 ";
  $db=opendb();
 $result = mysqli_query($db,$sql);
   closedb($db);
  if (FALSE == $result)
  {
      return FALSE;
  }
  else
  {
      return TRUE;
  }
}

//
//modif config li�e � l'espace
//

function modifconfigespace($id,$nom,$forfait,$inscripauto,$message)
{
if($forfait>1){
	//modification depuis admin_form_epn
$sql="UPDATE `tab_config` 
SET `nom_espace`='".$nom."'
WHERE `id_espace`='".$id."' ";

}else{
	//modification depuis admin_configepnconnect
	$sql="UPDATE `tab_config` 
SET `inscription_usagers_auto`='".$inscripauto."',
`message_inscription`='".$message."',
`activation_forfait`='".$forfait."'

WHERE `id_espace`='".$id."' ";
	
}


$db=opendb();
 $result = mysqli_query($db,$sql);
   closedb($db);
  if (FALSE == $result)
  {
      return FALSE;
  }
  else
  {
      return TRUE;
  }
	
	
}


//
// supEspace ()
// supprime un espace dans la table espace
function supEspace($id)
{
 
  $sql = "DELETE FROM `tab_espace` WHERE `id_espace` = ".$id." LIMIT 1" ;
  //supprimer aussi les config correspondante
  $sql2="DELETE FROM `tab_config` WHERE `id_espace`=".$id." LIMIT 1" ;
  $sql3="DELETE FROM `tab_config_logiciel` WHERE `id_espace`= ".$id." LIMIT 1" ;
  $sql4="DELETE FROM `tab_horaire` WHERE `id_epn`=".$id." LIMIT 1" ;
  
  $db=opendb();
 $result = mysqli_query($db,$sql);
 $result2 = mysqli_query($db,$sql2);
 $result3 = mysqli_query($db,$sql3);
 $result4 = mysqli_query($db,$sql4);
   closedb($db);
  if (FALSE == $result)
  {
      return FALSE;
  }
  else
  {
      return TRUE;
  }
}

//
// getAllVille()
// recupere les villes
function getAllVille()
{
    $sql="SELECT `nom_city`, `id_city` FROM tab_city;";
	
  $db=opendb();
 $result = mysqli_query($db,$sql);
   closedb($db);
  if (FALSE == $result)
  {
      return FALSE;
  }
  else
  {
      return $result;
  }
}


//
// utilisation ----------------------------------------------------------------------
//
//
// getAllUtilisation()
// recupere les utilisations de la table utilisation pour l'utilisation d'un poste
function getAllUtilisation()
{
  $sql ="SELECT `id_utilisation`,`nom_utilisation`,`type_menu`, `visible` FROM `tab_utilisation` ORDER BY `id_utilisation` " ;
  $db=opendb();
 $result = mysqli_query($db,$sql);
   closedb($db);
  if ($result ==FALSE)
  {
      return FALSE ;
  }
  else
  {
      /*$nb = mysql_num_rows($result);
      $tableau = array();
      for ($i=1;$i<=$nb;$i++)
      {
          $row=mysqli_fetch_array($result);
          $tableau[$row["id_utilisation"]] = $row["nom_utilisation"] ;
      }*/
      //return $tableau ;
      return $result ;
  }
}/*

//
// getUsage()
// recupere les codes de l'usage de la table usage pour l'utilisation d'un poste
function getUsage($id)
{
  $sql="SELECT `id_usage`
        FROM `rel_usage_computer` AS rel, `tab_computer` AS computer
        WHERE rel.id_computer = computer.id_computer
        AND computer.id_computer=".$id;
  $db=opendb();
 $result = mysqli_query($db,$sql);
   closedb($db);
  if ($result == FALSE)
  {
      return FALSE ;
  }
  else
  {
      $nb=mysql_num_rows($result);
      $tableau = array();
      for ($i=1;$i<=$nb;$i++)
      {
      $row = mysqli_fetch_array($result);
      $tableau[$i] = $row["id_usage"] ; 
      }
      return $tableau ;
  }
}

//renvoi le nom des usages d'une machine
function getUsageNameById($idcomp)
{
    $sql = 'SELECT TU.nom_usage
            FROM rel_usage_computer AS RUC 
            INNER JOIN tab_usage AS TU ON TU.id_usage = RUC.id_usage            
            WHERE id_computer='.$idcomp.'' ;

    $db=opendb();
   $result = mysqli_query($db,$sql);
     closedb($db);
    if ($result==FALSE)
    {
        return FALSE ;
    }
    else
    {
        return $result;    
    }
}

//renvoi les noms des usages a partir d'un tableau d'ID
function getUsageName($usage)
{
    $nb = COUNT($usage) ;
    $i = 1;
    $sql = "SELECT nom_usage FROM tab_usage
            WHERE ";
            
    foreach($usage AS $key=>$value)
    {
      if ($nb == $i)
          $sql .="id_usage=".$key." " ;
      else
          $sql .="id_usage=".$key." OR " ;
          ++$i ;
    }
    $sql .='ORDER BY nom_usage ASC' ;
    $db=opendb();
   $result = mysqli_query($db,$sql);
     closedb($db);
    if ($result == FALSE)
    {
         return FALSE ;
    }
    else
    {
        $return =array() ;
        while ($row = mysqli_fetch_array($result))
        {
            $return[]= $row['nom_usage'] ;
        }
        return $return ;        
    }
}*/

//
// addUtilisation()
// ajoute une nouvelle utilisation au materiel
function addUtilisation($utilisation, $type, $visible)
{
  $sql="INSERT INTO `tab_utilisation` (`id_utilisation`,`nom_utilisation`, `type_menu`, `visible`) VALUES ('','".$utilisation."', '".$type."', '".$visible."')";
  $db=opendb();
 $result = mysqli_query($db,$sql);
   closedb($db);
  if($result == FALSE)
  {
      return FALSE ;
  }
  else
  {
      return TRUE ;
  }
}


//
// modUtilisation()
// modifie une utilisation au materiel
function modUtilisation($id,$utilisation,$type, $visible)
{
  $sql="UPDATE `tab_utilisation`
        SET `nom_utilisation`='".$utilisation."',
        `type_menu`='".$type."',
        `visible`='".$visible."'
        WHERE `id_utilisation`=".$id." LIMIT 1 ";
  $db=opendb();
 $result = mysqli_query($db,$sql);
   closedb($db);
  if($result == FALSE)
  {
      return FALSE ;
  }
  else
  {
      return TRUE ;
  }
}

//
// supUtilisation()
// suppression d'une utilisation
function supUtilisation($id)
{
  $sql = "DELETE FROM `tab_utilisation` WHERE `id_utilisation`=".$id." LIMIT 1";
  $db=opendb();
 $result = mysqli_query($db,$sql);
   closedb($db);
  if($result == FALSE)
  {
      return FALSE ;
  }
  else
  {
      $sql = "SELECT `id_utilisation_user` FROM `rel_utilisation_user` WHERE `id_utilisation`=".$id ;
      $db=opendb();
     $result = mysqli_query($db,$sql);
       closedb($db);
      if($result == FALSE)
      {
          return FALSE ;
      }
      else
      {
          $nb = mysqli_num_rows($result);
          if ($nb >0)
          {
            for ($i=1;$i<=$nb;$i++)
            {
                $row = mysqli_fetch_array($result);
                $sql = "DELETE FROM `rel_utilisation_user` WHERE `id_utilisation_user`=".$row["id_utilisation_user"];
                $db=opendb();
               $result = mysqli_query($db,$sql);
                 closedb($db);
                if($result == FALSE)
                {
                    return FALSE ;
                }
            }
          }
          return TRUE;
      }
  }
}

///***** GESTION DES COURRIERS ***** ///
function createCourrier($titre,$texte,$name,$type){
	$sql="INSERT INTO `tab_courriers`(`id_courrier`, `courrier_titre`, `courrier_text`, `courrier_name`, `courrier_type`) 
	VALUES ('','".$titre."','".$texte."','".$name."','".$type."')
	";
	
	 $db=opendb();
 $result = mysqli_query($db,$sql);
   closedb($db);
  if($result == FALSE)
  {
      return FALSE ;
  }
  else
  {
      return TRUE ;
  }
}

function  getAllCourrier(){
	$sql="SELECT * FROM `tab_courriers`";
	 $db=opendb();
 $result = mysqli_query($db,$sql);
   closedb($db);
  if($result == FALSE)
  {
      return FALSE ;
  }
  else
  {
      return $result ;
  }
	
	
}

function getcourrier($id){
	$sql="SELECT * FROM `tab_courriers` WHERE id_courrier=".$id;
	 $db=opendb();
 $result = mysqli_query($db,$sql);
   closedb($db);
  if($result == FALSE)
  {
      return FALSE ;
  }
  else
  {
      return mysqli_fetch_array($result) ;
  }
}



function  modCourrier($id,$titre,$texte,$name,$type)
{
	$sql="UPDATE `tab_courriers` SET 	`courrier_titre`='".$titre."',
	`courrier_text`='".$texte."',
	`courrier_name`=".$name.",
	`courrier_type`=".$type." 
	WHERE `id_courrier`=".$id;
	 $db=opendb();
 $result = mysqli_query($db,$sql);
   closedb($db);
  if($result == FALSE)
  {
      return FALSE ;
  }
  else
  {
      return TRUE ;
  }
	
	
}

function supCourrier($id)
{
	$sql="DELETE FROM `tab_courriers` WHERE `id_courrier`=".$id;
	 $db=opendb();
  $result = mysqli_query($db,$sql);
  closedb($db);
  if ($result == FALSE)
  {
      return FALSE ;
  }
  else
  {
      return TRUE  ;
  }
}

//sur la page atelier, recuperer les infos du mail de rappel
function getMailRappel(){
	$sql="SELECT `courrier_text`,`courrier_type` FROM `tab_courriers` WHERE `courrier_name`=1";
	 $db=opendb();
 $result = mysqli_query($db,$sql);
   closedb($db);
  if($result == FALSE)
  {
      return FALSE ;
  }
  else
  {
       $txt = array();
        $nb= mysqli_num_rows($result);
        for ($i=1;$i<=$nb;$i++)
        {
            $row = mysqli_fetch_array($result);
						$txt[$row["courrier_type"]] = $row["courrier_text"] ;
	    
        }
        return $txt ;
  }
}
//sur la page utilisateur, recuperer les infos du mail d'inscription
function getMailInscript(){
	$sql="SELECT `courrier_text`,`courrier_type` FROM `tab_courriers` WHERE `courrier_name`=1 AND `courrier_titre` LIKE '%inscription%' ";
	 $db=opendb();
 $result = mysqli_query($db,$sql);
   closedb($db);
  if($result == FALSE)
  {
      return FALSE ;
  }
  else
  {
       $txt = array();
        $nb= mysqli_num_rows($result);
        for ($i=1;$i<=$nb;$i++)
        {
            $row = mysqli_fetch_array($result);
						$txt[$row["courrier_type"]] = $row["courrier_text"] ;
	    
        }
        return $txt ;
  }
}
//gestin de la newsletter
function getNewsletterUsers()
{
$sql="SELECT `nom_user`, `prenom_user`, `mail_user`, `epn_user` FROM `tab_user` WHERE `newsletter_user`=1";
 $db=opendb();
  $result = mysqli_query($db,$sql);
  closedb($db);
  if ($result == FALSE)
  {
      return FALSE ;
  }
  else
  {
      
	  return $result  ;
  }

}


///*******************************************************************///

///***** GESTION DES PROFILS ANIMATEURS ***** ///

//enregistrer les param�tres d'un animateur
function paramAnim($idanim,$epn_r,$salle_r,$avatar){
$sql="INSERT INTO `rel_user_anim`(`id_useranim`, `id_animateur`, `id_epn`, `id_salle`, `anim_avatar`) VALUES ('','".$idanim."', '".$epn_r."', '".$salle_r."', '".$avatar."')";
  $db=opendb();
 $result = mysqli_query($db,$sql);
   closedb($db);
  if($result == FALSE)
  {
      return FALSE ;
  }
  else
  {
      return TRUE ;
  }

}



function getAnimateur($id_user)
{
$sql="SELECT `id_epn`, `id_salle`, `anim_avatar` FROM `rel_user_anim` WHERE `id_animateur`='".$id_user."' ";
$db=opendb();
 $result = mysqli_query($db,$sql);
   closedb($db);
  if($result == FALSE)
  {
      return FALSE ;
  }
  else
  {
      $row=mysqli_fetch_array($result) ;
      return $row;
  }

}


function Ptestanim($id)
{
$sql="SELECT `id_useranim` FROM `rel_user_anim` WHERE `id_animateur`='".$id."' ";
$db=opendb();
 $result = mysqli_query($db,$sql);
   closedb($db);
  $row=mysqli_num_rows($result);
  
  if($row == 0)
  {
      return FALSE ;
  }
  else
  {
      return TRUE ;
  }


}


function modifparamAnim($idanim,$epn_r,$salles,$avatar_r)
{
$sql="UPDATE `rel_user_anim` 
SET `id_epn`='".$epn_r."',
`id_salle`='".$salles."',
`anim_avatar`='".$avatar_r."' 
WHERE id_animateur='".$idanim."' ";
$db=opendb();
 $result = mysqli_query($db,$sql);
   closedb($db);
  if($result == FALSE)
  {
      return FALSE ;
  }
  else
  {
      
      return TRUE;
  }

}

function getAvatar($id){
$sql="SELECT `anim_avatar` FROM `rel_user_anim` WHERE `id_animateur`='".$id."' ";
$db=opendb();
 $result = mysqli_query($db,$sql);
   closedb($db);
  if($result == FALSE)
  {
      return FALSE ;
  }
  else
  {
	 $row=mysqli_fetch_array($result) ;
      return $row;
  }
}

function getSallesbyAnim($id)
{
$sql="SELECT `id_salle` FROM `rel_user_anim` WHERE `id_animateur`='".$id."' ";
$db=opendb();
 $result = mysqli_query($db,$sql);
 closedb($db);
if($result == FALSE)
  {
      return FALSE ;
  }
else
  {
	 $row=mysqli_fetch_array($result) ;
      return $row;
  }

}

function getNomsalleforAnim($id)
{
$sql="SELECT `nom_salle` FROM `tab_salle` WHERE `id_salle`='".$id."' ";
$db=opendb();
 $result = mysqli_query($db,$sql);
 closedb($db);
if($result == FALSE)
  {
      return FALSE ;
  }
else
  {
	 $row=mysqli_fetch_array($result) ;
      return $row["nom_salle"];
  }
}

function getEpnSalle($id)
 {
 $sql="SELECT nom_espace, id_salle FROM tab_espace, tab_salle WHERE id_salle='".$id."' 
 AND tab_espace.id_espace=tab_salle.id_espace";
 
 $db=opendb();
 $result = mysqli_query($db,$sql);
 closedb($db);
if($result == FALSE)
  {
      return FALSE ;
  }
else
  {
	 $row=mysqli_fetch_array($result) ;
      return $row["nom_espace"];
  }
 
 }
 
 //***********Fonctions pour les page Utilisateur lambda *** ////
 function getTestInscript($user,$idatelier,$type)
 {
 if($type=="a"){
  $sql="SELECT `status_rel_atelier_user` AS statut FROM `rel_atelier_user` WHERE `id_atelier`='".$idatelier."' AND `id_user`='".$user."' ";
 }elseif($type=="s"){
 $sql="SELECT  `status_rel_session` AS statut FROM `rel_session_user` WHERE `id_session`='".$idatelier."' AND `id_user`='".$user."' ";
 }
$db=opendb();
 $result = mysqli_query($db,$sql);
 closedb($db);
 
 if(mysqli_num_rows($result) == 0)
  {
      return "FALSE" ;
  }
else
  {
	 $row=mysqli_fetch_array($result) ;
     return $row["statut"];
		
  }
 
 }
 
 function updateNewsletter($iduser,$type)
 {
 $sql="UPDATE `tab_user` SET `newsletter_user`=".$type." WHERE `id_user`=".$iduser;
 $db=opendb();
 $result = mysqli_query($db,$sql);
 closedb($db);
 if($result == FALSE)
  {
      return FALSE ;
  }
else
  {
	 return TRUE;
  }
 
 }
 
 function readMyMessage($iduser)
 {
 $sql="SELECT * FROM `tab_messages` WHERE `mes_auteur`='".$iduser."' OR `mes_destinataire`='".$iduser."'
ORDER BY `mes_date` DESC ";
 $db=opendb();
 $result = mysqli_query($db,$sql);
 closedb($db);
 if($result == FALSE)
  {
      return FALSE ;
  }
else
  {
	 return $result;
  }
 
 }
 
 //*************************************************************///
 
 //********Gestion des pr�inscriptions *********************/////
 
 //****preinscriptions automatiques par internet
 
 //retourne l'activation du module ou pas...
 function getPreinsmode()
 {
	$sql="SELECT * FROM tab_captcha";
	 $db=opendb();
  	$result = mysqli_query($db, $sql);
    closedb($db);
    if ($result == FALSE )
    {
        return FALSE ;
    }
    else
    {
        return mysqli_fetch_array($result) ;
    }
	
 }
 
 function updatePreinsmode($activation,$code)
 {
	
	$sql="UPDATE `tab_captcha` SET `capt_activation`='".$activation."',`capt_code`='".$code."' ";
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
// searchUserInsc()
// recherche un ou des utilisateurs et renvoi le resultat de la recherche
function searchUserInsc($exp)
{
    $sql="SELECT `id_inscription_user` , `nom_inscription_user` , `prenom_inscription_user` , `temps_inscription_user`,`status_inscription_user`, `lastvisit_inscription_user`, `login_inscription_user`, `id_inscription_computer`
        FROM `tab_inscription_user`
        WHERE `nom_inscription_user` LIKE '%".$exp."%'
        OR `prenom_inscription_user` LIKE '%".$exp."%'
        AND (`status_inscription_user`=1 OR `status_inscription_user`=2) 
        ORDER BY `status_inscription_user` ASC, `nom_inscription_user` ASC";
    $db=opendb();
  	$result = mysqli_query($db, $sql);
    closedb($db);
    if ($result == FALSE )
    {
        return FALSE ;
    }
    else
    {
        return $result ;
    }
}
//
// getAllUserInsc()
// recupere les utilisateurs
function getAllUserInsc()
{
 $sql="SELECT `id_inscription_user`, `date_inscription_user`, `nom_inscription_user`, `prenom_inscription_user`, `login_inscription_user`, `id_inscription_computer`
        FROM tab_inscription_user  ORDER BY `nom_inscription_user`";
  
    $db=opendb();
  	$result = mysqli_query($db, $sql);
    closedb($db);
  if (FALSE == $result)
  {
      return FALSE;
  }
  else
  {
      return $result;
  }
}

//
// getUserInsc()
// recupere un utilisateur
function getUserInsc($id)
{
  $sql="SELECT *
        FROM tab_inscription_user WHERE id_inscription_user=".$id;
    $db=opendb();
  	$result = mysqli_query($db, $sql);
    closedb($db);
  if (FALSE == $result)
  {
      return FALSE;
  }
  else
  {
      $row=mysqli_fetch_array($result);
      return $row;
  }
}

 
 //
// deluser
// Supprime un utilisateur
function delUserInsc($id)
{
  $sql = "DELETE FROM `tab_inscription_user` WHERE `id_inscription_user`=".$id." LIMIT 1 " ;
    $db=opendb();
  	$result = mysqli_query($db, $sql);
    closedb($db);
  if (FALSE == $result)
  {
      return FALSE;
  }
  else
  {
      return TRUE;
  }
}


function addUserinscript($date,$nom,$prenom,$sexe,$jour,$mois,$annee,$adresse,$pays,$codepostal,$commune,$ville,$tel,$telport,$mail,$temps,$loginn,$passs,$status,$csp,$equipement,$utilisation,$connaissance, $info,$epn)
{

$sql="INSERT INTO `tab_inscription_user`(`id_inscription_user`, `date_inscription_user`, `nom_inscription_user`, `prenom_inscription_user`, `sexe_inscription_user`, `jour_naissance_inscription_user`, `mois_naissance_inscription_user`, `annee_naissance_inscription_user`, `adresse_inscription_user`, `quartier_inscription_user`, `code_postal_inscription`, `commune_inscription_autres`, `ville_inscription_user`, `tel_inscription_user`, `tel_port_inscription_user`, `mail_inscription_user`, `temps_inscription_user`, `login_inscription_user`, `pass_inscription_user`, `status_inscription_user`, `lastvisit_inscription_user`, `csp_inscription_user`, `equipement_inscription_user`, `utilisation_inscription_user`, `connaissance_inscription_user`, `info_inscription_user`, `id_inscription_computer`) 
VALUES ('','".$date."','".$nom."','".$prenom."','".$sexe."','".$jour."','".$mois."','".$annee."','".$adresse."','".$pays."','".$codepostal."','".$commune."','".$ville."','".$tel."','".$telport."','".$mail."','".$temps."','".$loginn."','".$passs."','".$status."','".date('Y-m-d')."','".$csp."','".$equipement."','".$utilisation."','".$connaissance."','".$info."','".$epn."')";
 $db=opendb();
  	$result = mysqli_query($db, $sql);
    closedb($db);
  if (FALSE == $result)
  {
      return FALSE;
  }
  else
  {
      return TRUE;
  }

}

function array_put_to_position(&$array, $object, $position, $name = null)
{
        $count = 0;
        $return = array();
        foreach ($array as $k => $v)
        {  
                // insert new object
                if ($count == $position)
                {  
                        if (!$name) $name = $count;
                        $return[$name] = $object;
                        $inserted = true;
                }  
                // insert old object
                $return[$k] = $v;
                $count++;
        }  
        if (!$name) $name = $count;
        if (!$inserted) $return[$name];
        $array = $return;
        return $array;
}
 //**********//
 ///////
 
 //*********Gestion de la console et epnconnect*******************************//////////////////////////
 
//function updateConfiglogiciel($array,$idvalue, $idespace)
function updateConfiglogiciel($id,$epn,$shiftlog,$insclog,$renslog,$conexlog,$bloclog,$tempslog,$decouselog,$fermersessionlog)
{
  	$sql="UPDATE `tab_config_logiciel` SET 
	`id_espace`='".$epn."',
	`config_menu_logiciel`='".$shiftlog."',
	`page_inscription_logiciel`='".$insclog."',
	`page_renseignement_logiciel`='".$renslog."',
	`connexion_anim_logiciel`='".$conexlog."',
	`bloquage_touche_logiciel`='".$bloclog."',
	`affichage_temps_logiciel`='".$tempslog."',
	`deconnexion_auto_logiciel`='".$decouselog."', 
	fermeture_session_auto='".$fermersessionlog."'
	WHERE `id_config_logiciel`='".$id."' ";
	
    $db=opendb();
  	$result = mysqli_query($db, $sql);
    closedb($db);
    if (FALSE == $result)
    {
        return FALSE ;
    }
    else
    {
        return TRUE ;
    }
}

function addConfiglogiciel($epn,$shiftlog,$insclog,$renslog,$conexlog,$bloclog,$tempslog,$decouselog)
{
$sql="INSERT INTO `tab_config_logiciel`(`id_config_logiciel`, `id_espace`, `config_menu_logiciel`, `page_inscription_logiciel`, `page_renseignement_logiciel`, `connexion_anim_logiciel`, `bloquage_touche_logiciel`, `affichage_temps_logiciel`, `deconnexion_auto_logiciel`) 
VALUES ('','".$epn."','".$shiftlog."','".$insclog."','".$renslog."','".$conexlog."','".$bloclog."','".$tempslog."','".$decouselog."')";
$db=opendb();
  	$result = mysqli_query($db, $sql);
    closedb($db);
    if (FALSE == $result)
    {
        return FALSE ;
    }
    else
    {
        return TRUE ;
    }
}

function testconfigepn($epn)
{
$sql="SELECT  `id_config_logiciel` 
		FROM  `tab_config_logiciel` 
		WHERE  `id_espace` ='".$epn."' ";
$db=opendb();
  	$result = mysqli_query($db, $sql);
    closedb($db);
	$nb=mysqli_num_rows($result);
	
	
    if ($nb==0)
    {
        return FALSE ;
    }
    else
    {
        return TRUE ;
    }
}
//
// getConfig_logiciel()
// recupere un utilisateur
function getConfig_logiciel($idespace)
{
  $sql="SELECT * FROM `tab_config_logiciel` WHERE `id_espace`=".$idespace;
    $db=opendb();
  	$result = mysqli_query($db, $sql);
    closedb($db);
  if (FALSE == $result)
  {
      return FALSE;
  }
  else
  {
      $row=mysqli_fetch_array($result);
      return $row;
  }
}

//
// EnvoieMessageSocket()
// Supprime un utilisateur
function EnvoieMessageSocket($message, $adresse)
{
	//$adresse = "10.8.165.93";
	$service_port = "18181";
	if (($socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) 
	{
    	return FALSE;
	}
	$result = socket_connect($socket, $adresse, $service_port);
	if($result==FALSE)
	{
		return FALSE;
	}
	else
	{
		$msg = "menu=4&message=".$message;
    	socket_write($socket, $msg, strlen($msg));
		
		socket_close($socket);
		
		return TRUE;
	}
}

//
// AffectationUserSocket()
// Supprime un utilisateur
function AffectationUserSocket($id_user, $adresse, $temps)
{
	//$adresse = "10.8.165.93";
	$service_port = "18181";
	if (($socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) 
	{
    	return FALSE;
	}
	$result = socket_connect($socket, $adresse, $service_port);
	if($result==FALSE)
	{
		return FALSE;
	}
	else
	{
		$msg = "menu=1&id_user=".$id_user."&temps=".$temps."";
    	socket_write($socket, $msg, strlen($msg));

    	//socket_write($socket, $msg, $longmess);
		
		socket_close($socket);
		
		return TRUE;
	}
}

//
// LiberationUserSocket()
// Supprime un utilisateur
function LibertionUserSocket($adresse)
{
	//$adresse = "10.8.165.93";
	$service_port = "18181";
	if (($socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) 
	{
    	return FALSE;
	}
	$result = socket_connect($socket, $adresse, $service_port);
	if($result==FALSE)
	{
		return FALSE;
	}
	else
	{
		$msg = "menu=2";
    	socket_write($socket, $msg, strlen($msg));
		
		socket_close($socket);
		
		return TRUE;
	}
}

//
// ControlePosteSocket()
// Supprime un utilisateur
function ControlePosteSocket($adresse)
{
	//$adresse = "10.8.165.93";
	$service_port = "18181";
	if (($socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) 
	{
    	return FALSE;
	}
	$result = socket_connect($socket, $adresse, $service_port);
	if($result==FALSE)
	{
		return FALSE;
	}
	else
	{
		$msg = "menu=5";
    	socket_write($socket, $msg, strlen($msg));
		
		socket_close($socket);
		
		return TRUE;
	}
}

//
// AffectationAtelierSocket()
// Supprime un utilisateur
function AffectationAtelierSocket($adresse)
{
	//$adresse = "10.8.165.93";
	$service_port = "18181";
	if (($socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) 
	{
    	return FALSE;
	}
	$result = socket_connect($socket, $adresse, $service_port);
	if($result==FALSE)
	{
		return FALSE;
	}
	else
	{
		$msg = "menu=5";
    	socket_write($socket, $msg, strlen($msg));
		
		socket_close($socket);
		
		return TRUE;
	}
}

///liberation de la salle pour l'atelier par epnconnect

function ModifStatusAtelier($idatelier,$status)
{
if($status==2){
	$sql="UPDATE `tab_atelier` SET `status_atelier`='2', 
				`cloturer_atelier`= '1'
				WHERE `id_atelier`='".$idatelier."'";
}elseif ($status==1){
	$sql="UPDATE `tab_atelier` SET `status_atelier`='1' WHERE `id_atelier`='".$idatelier."' ";
	}
	
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

///Ajouter la relation atelier-commputer pour quEPN cpnnect lib�re la salle !
function connectAtelierComputer($salle,$idatelier)
{
	$sql="INSERT INTO `rel_atelier_computer` ( `id_atelier_computer` , `id_atelier_rel` , `id_computer_rel` )
SELECT '', '".$idatelier."', `id_computer`
FROM `tab_computer`
WHERE `id_salle` ='".$salle."'
AND `usage_computer` =1 ";
	$db=opendb();
	$resultrow = mysqli_query($db, $sql);
	closedb($db);
	if(FALSE==$resultrow){
		return FALSE;
	}else{
	
    return TRUE;
	}
}
///supprimer la susdite relation si suppression de l'atelier
function supprimComputerAtelier($idatelier)
{
	$sql="DELETE FROM `rel_atelier_computer` WHERE `id_atelier_rel`=".$idatelier;
	$db=opendb();
	$result = mysqli_query($db, $sql);
	closedb($db);
	if(FALSE==$result){
		return FALSE;
	}else{
	
    return TRUE;
	}
}


///************Fonctions de la page d'accueil ********************///
//
//Retourne les ID des ateliers et sessions de la semaine
function getWeekAteliers($jour,$espace)
{
//$sql="SELECT * FROM `tab_atelier` WHERE `date_atelier`='".$jour."' ORDER BY heure_atelier ASC";
if($espace==0){
//page utilisateur liste de tous les ateliers/sessions du reseau
$sql2=" SELECT  `id_atelier` AS id ,date_atelier AS dateAS,'tab_atelier' AS tab_origine,`id_espace`
FROM  `tab_atelier` , tab_salle
WHERE WEEK(`date_atelier`) = WEEK('".$jour."') 
AND statut_atelier=0
AND tab_atelier.`salle_atelier` = tab_salle.id_salle
UNION 
SELECT  tab_session_dates.`id_session` AS id, tab_session_dates.`date_session` AS dateAS, 'tab_session_dates' AS tab_origine,`id_espace`
FROM tab_session_dates,tab_salle,tab_session
WHERE WEEK(DATE(  tab_session_dates.date_session )) = WEEK('".$jour."') 
AND  `statut_datesession`=0
AND tab_session.`id_salle` = tab_salle.id_salle
AND tab_session.`id_session` =tab_session_dates.`id_session`
ORDER BY dateAS ASC 
";
}else{
//adapter donne les ID et le type d'atelier
$sql2=" SELECT  `id_atelier` AS id ,date_atelier AS dateAS,'tab_atelier' AS tab_origine
FROM  `tab_atelier` , tab_salle
WHERE WEEK(`date_atelier`) = WEEK('".$jour."') 
AND statut_atelier=0
AND `id_espace` =".$espace."
AND tab_atelier.`salle_atelier` = tab_salle.id_salle
UNION 
SELECT  tab_session_dates.`id_session` AS id, tab_session_dates.`date_session` AS dateAS, 'tab_session_dates' AS tab_origine
FROM tab_session_dates,tab_salle,tab_session
WHERE WEEK(DATE(  tab_session_dates.date_session )) = WEEK('".$jour."') 
AND  `statut_datesession`=0
AND `id_espace` =".$espace."
AND tab_session.`id_salle` = tab_salle.id_salle
AND tab_session.`id_session` =tab_session_dates.`id_session`
ORDER BY dateAS ASC 
";
}

 $db=opendb();
$result = mysqli_query($db, $sql2);
closedb($db);
    if ($result == FALSE )
    {
        return FALSE ;
    }
    else
    {
	return $result ;
    }


}
function getConfigVersion($idepn)
{
$sql="SELECT `name_config` FROM `tab_config` WHERE `id_espace`=".$idepn;
$db=opendb();
$result = mysqli_query($db, $sql);
closedb($db);
    if ($result == FALSE )
    {
        return FALSE ;
    }
    else
    {
	$row=mysqli_fetch_array($result);
	return $row["name_config"] ;
    }


}

// retourne l'id d'un log du jour pour la mose � jour du statut des adherents
function getLogUser($type)
{
$sql="SELECT `id_log`,`log_date` FROM `tab_logs` WHERE date(`log_date`)=date(NOW()) AND `log_type`='".$type."'  ";
$db=opendb();
$result = mysqli_query($db, $sql);
closedb($db);
    if ($result == FALSE )
    {
        return FALSE ;
    }
    else
    {
		return $result ;
    }

}

// update des adherents actifs ---> inactifs
function updateUserStatut()
{
$sql="UPDATE `tab_user` SET `status_user`=2 WHERE `status_user`=1 AND DATE(`dateRen_user`)=DATE(NOW())";
$db=opendb();
$result = mysqli_query($db, $sql);
$nb=mysqli_affected_rows($db);

closedb($db);
    if ($result == FALSE )
    {
        return FALSE ;
    }
    else
    {
		return $nb ;
    }

}

//retourne id + nom + prenom des adherents inactifs du jour
function getAdhInactif($jour)
{
	$sql="SELECT `id_user`,`nom_user`,`prenom_user`  FROM `tab_user` WHERE  DATE(`dateRen_user`)=DATE(NOW())";
	
	$db=opendb();
$result = mysqli_query($db, $sql);
closedb($db);
    if ($result == FALSE )
    {
        return FALSE ;
    }
    else
    {
		return mysqli_fetch_array($result) ;
    }

	
}

// test si la base a �t� sauvegard�e
function getLogBackup()
{
	$sql="SELECT `id_log` FROM `tab_logs` WHERE MONTH(`log_date`)=MONTH(NOW())  AND `log_type`='bac' AND DATE(`log_date`)>DATE(`log_date`)-15 ";
	$db=opendb();
$result = mysqli_query($db, $sql);
closedb($db);
    if (mysqli_num_rows($result) == NULL )
    {
      return TRUE ;
    }
    else
    {
			return FALSE ;
    }
	
	
}



//insert un log dans la table des logs
function addLog($date,$type,$valid,$comment)
{
$sql="INSERT INTO `tab_logs`(`id_log`, `log_type`, `log_date`, `log_MAJ`, `log_valid`, `log_comment`)
VALUES ('','".$type."','".$date."','','".$valid."','".$comment."')";
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



//****
/*************************************************************************
php easy :: pagination scripts set - Version Two
==========================================================================
Author:      php easy code, www.phpeasycode.com
Web Site:    http://www.phpeasycode.com
Contact:     webmaster@phpeasycode.com
*************************************************************************/
function paginate_two($reload, $page, $tpages, $adjacents) {
	
	$firstlabel = "&laquo;&nbsp;";
	$prevlabel  = "&lsaquo;&nbsp;";
	$nextlabel  = "&nbsp;&rsaquo;";
	$lastlabel  = "&nbsp;&raquo;";
	
	$out = "<ul class=\"pagination pagination-sm no-margin pull-right\">\n";
	
	// first
	if($page>($adjacents+1)) {
		$out.= "<li><a href=\"" . $reload . "\">" . $firstlabel . "</a></li>\n";
	}
	else {
		$out.= "<li><span>" . $firstlabel . "</span></li>\n";
	}
	
	// previous
	if($page==1) {
		$out.= "<li><span>" . $prevlabel . "</span></li>\n";
	}
	elseif($page==2) {
		$out.= "<li><a href=\"" . $reload . "\">" . $prevlabel . "</a></li>\n";
	}
	else {
		$out.= "<li><a href=\"" . $reload . "&amp;page=" . ($page-1) . "\">" . $prevlabel . "</a></li>\n";
	}
	
	// 1 2 3 4 etc
	$pmin = ($page>$adjacents) ? ($page-$adjacents) : 1;
	$pmax = ($page<($tpages-$adjacents)) ? ($page+$adjacents) : $tpages;
	for($i=$pmin; $i<=$pmax; $i++) {
		if($i==$page) {
			$out.= "<li class=\"btn active\"><span >" . $i . "</span></li>\n";
		}
		elseif($i==1) {
			$out.= "<li><a href=\"" . $reload . "\">" . $i . "</a></li>\n";
		}
		else {
			$out.= "<li><a href=\"" . $reload . "&amp;page=" . $i . "\">" . $i . "</a></li>\n";
		}
	}
	
	// next
	if($page<$tpages) {
		$out.= "<li><a href=\"" . $reload . "&amp;page=" .($page+1) . "\">" . $nextlabel . "</a></li>\n";
	}
	else {
		$out.= "<li><span>" . $nextlabel . "</span></li>\n";
	}
	
	// last
	if($page<($tpages-$adjacents)) {
		$out.= "<li><a href=\"" . $reload . "&amp;page=" . $tpages . "\">" . $lastlabel . "</a></li>\n";
	}
	else {
		$out.= "<li><span>" . $lastlabel . "</span></li>\n";
	}
	
	$out.= "</ul>";
	
	return $out;
}
//****///////////////END PAGINATION FUNCTION //****


//****** Fonction pour la tab_connexion ***************//

//entre le log de connexion dans la base
function enterConnexionstatus($iduser,$date,$type,$macadress,$navig,$exploitation)
{
	$sql="INSERT INTO `tab_connexion`(`id_connexion`, `id_user`, `date_cx`, `type_cx`, `macasdress_cx`, `navigateur_cx`, `system_cx`) 
	VALUES ('','".$iduser."','".$date."','".$type."','".$macadress."','".$navig."','".$exploitation."')";
	$db=opendb();
	$result = mysqli_query($db, $sql);
	closedb($db);
	if($result==TRUE){
		return TRUE;
	}else{
		return FALSE;
	}
	
}


/* return Operating System */
function operating_system_detection(){

		
    if ( isset( $_SERVER ) ) {
    $agent = $_SERVER['HTTP_USER_AGENT'] ;
    }
    else {
			global $HTTP_SERVER_VARS ;
			if ( isset( $HTTP_SERVER_VARS ) ) {
			$agent = $HTTP_SERVER_VARS['HTTP_USER_AGENT'] ;
			}
			else {
			global $HTTP_USER_AGENT ;
			$agent = $HTTP_USER_AGENT ;
			}
    }
    $ros[] = array('Windows XP', 'Windows XP');
    $ros[] = array('Windows NT 5.1|Windows NT5.1)', 'Windows XP');
    $ros[] = array('Windows 2000', 'Windows 2000');
    $ros[] = array('Windows NT 5.0', 'Windows 2000');
    $ros[] = array('Windows NT 4.0|WinNT4.0', 'Windows NT');
    $ros[] = array('Windows NT 5.2', 'Windows Server 2003');
    $ros[] = array('Windows NT 6.0', 'Windows Vista');
    $ros[] = array('Windows NT 7.0', 'Windows 7');
    $ros[] = array('Windows CE', 'Windows CE');
    $ros[] = array('(media center pc).([0-9]{1,2}\.[0-9]{1,2})', 'Windows Media Center');
    $ros[] = array('(win)([0-9]{1,2}\.[0-9x]{1,2})', 'Windows');
    $ros[] = array('(win)([0-9]{2})', 'Windows');
    $ros[] = array('(windows)([0-9x]{2})', 'Windows');
    // Doesn't seem like these are necessary...not totally sure though..
    //$ros[] = array('(winnt)([0-9]{1,2}\.[0-9]{1,2}){0,1}', 'Windows NT');
    //$ros[] = array('(windows nt)(([0-9]{1,2}\.[0-9]{1,2}){0,1})', 'Windows NT'); // fix by bg
    $ros[] = array('Windows ME', 'Windows ME');
    $ros[] = array('Win 9x 4.90', 'Windows ME');
    $ros[] = array('Windows 98|Win98', 'Windows 98');
    $ros[] = array('Windows 95', 'Windows 95');
    $ros[] = array('(windows)([0-9]{1,2}\.[0-9]{1,2})', 'Windows');
    $ros[] = array('win32', 'Windows');
    $ros[] = array('(java)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2})', 'Java');
    $ros[] = array('(Solaris)([0-9]{1,2}\.[0-9x]{1,2}){0,1}', 'Solaris');
    $ros[] = array('dos x86', 'DOS');
    $ros[] = array('unix', 'Unix');
    $ros[] = array('Mac OS X', 'Mac OS X');
    $ros[] = array('Mac_PowerPC', 'Macintosh PowerPC');
    $ros[] = array('(mac|Macintosh)', 'Mac OS');
    $ros[] = array('(sunos)([0-9]{1,2}\.[0-9]{1,2}){0,1}', 'SunOS');
    $ros[] = array('(beos)([0-9]{1,2}\.[0-9]{1,2}){0,1}', 'BeOS');
    $ros[] = array('(risc os)([0-9]{1,2}\.[0-9]{1,2})', 'RISC OS');
    $ros[] = array('os/2', 'OS/2');
    $ros[] = array('freebsd', 'FreeBSD');
    $ros[] = array('openbsd', 'OpenBSD');
    $ros[] = array('netbsd', 'NetBSD');
    $ros[] = array('irix', 'IRIX');
    $ros[] = array('plan9', 'Plan9');
    $ros[] = array('osf', 'OSF');
    $ros[] = array('aix', 'AIX');
    $ros[] = array('GNU Hurd', 'GNU Hurd');
    $ros[] = array('(fedora)', 'Linux - Fedora');
    $ros[] = array('(kubuntu)', 'Linux - Kubuntu');
    $ros[] = array('(ubuntu)', 'Linux - Ubuntu');
    $ros[] = array('(debian)', 'Linux - Debian');
    $ros[] = array('(CentOS)', 'Linux - CentOS');
    $ros[] = array('(Mandriva).([0-9]{1,3}(\.[0-9]{1,3})?(\.[0-9]{1,3})?)', 'Linux - Mandriva');
    $ros[] = array('(SUSE).([0-9]{1,3}(\.[0-9]{1,3})?(\.[0-9]{1,3})?)', 'Linux - SUSE');
    $ros[] = array('(Dropline)', 'Linux - Slackware (Dropline GNOME)');
    $ros[] = array('(ASPLinux)', 'Linux - ASPLinux');
    $ros[] = array('(Red Hat)', 'Linux - Red Hat');
    // Loads of Linux machines will be detected as unix.
    // Actually, all of the linux machines I've checked have the 'X11' in the User Agent.
    //$ros[] = array('X11', 'Unix');
    $ros[] = array('(linux)', 'Linux');
    $ros[] = array('(amigaos)([0-9]{1,2}\.[0-9]{1,2})', 'AmigaOS');
    $ros[] = array('amiga-aweb', 'AmigaOS');
    $ros[] = array('amiga', 'Amiga');
    $ros[] = array('AvantGo', 'PalmOS');
       $ros[] = array('[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3})', 'Linux');
    $ros[] = array('(webtv)/([0-9]{1,2}\.[0-9]{1,2})', 'WebTV');
    $ros[] = array('Dreamcast', 'Dreamcast OS');
    $ros[] = array('GetRight', 'Windows');
    $ros[] = array('go!zilla', 'Windows');
    $ros[] = array('gozilla', 'Windows');
    $ros[] = array('gulliver', 'Windows');
    $ros[] = array('ia archiver', 'Windows');
    $ros[] = array('NetPositive', 'Windows');
    $ros[] = array('mass downloader', 'Windows');
    $ros[] = array('microsoft', 'Windows');
    $ros[] = array('offline explorer', 'Windows');
    $ros[] = array('teleport', 'Windows');
    $ros[] = array('web downloader', 'Windows');
    $ros[] = array('webcapture', 'Windows');
    $ros[] = array('webcollage', 'Windows');
    $ros[] = array('webcopier', 'Windows');
    $ros[] = array('webstripper', 'Windows');
    $ros[] = array('webzip', 'Windows');
    $ros[] = array('wget', 'Windows');
    $ros[] = array('Java', 'Unknown');
    $ros[] = array('flashget', 'Windows');
    // delete next line if the script show not the right OS
    //$ros[] = array('(PHP)/([0-9]{1,2}.[0-9]{1,2})', 'PHP');
    $ros[] = array('MS FrontPage', 'Windows');
    $ros[] = array('(msproxy)/([0-9]{1,2}.[0-9]{1,2})', 'Windows');
    $ros[] = array('(msie)([0-9]{1,2}.[0-9]{1,2})', 'Windows');
    $ros[] = array('libwww-perl', 'Unix');
    $ros[] = array('UP.Browser', 'Windows CE');
    $ros[] = array('NetAnts', 'Windows');
    $file = count ( $ros );
    $os = '';
    for ( $n=0 ; $n<$file ; $n++ ){
			if ( preg_match('/'.$ros[$n][0].'/i' , $agent, $name)){
			$os = @$ros[$n][1].' '.@$name[2];
			break;
			}
    }
    return trim ( $os );
}
 
function getBrowser()
{
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version= "";

    //First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
    }
    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
    }
    elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'windows';
    }
   
    // Next get the name of the useragent yes seperately and for good reason
    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
    {
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    }
    elseif(preg_match('/Firefox/i',$u_agent))
    {
        $bname = 'Mozilla Firefox';
        $ub = "Firefox";
    }
    elseif(preg_match('/Chrome/i',$u_agent))
    {
        $bname = 'Google Chrome';
        $ub = "Chrome";
    }
    elseif(preg_match('/Safari/i',$u_agent))
    {
        $bname = 'Apple Safari';
        $ub = "Safari";
    }
    elseif(preg_match('/Opera/i',$u_agent))
    {
        $bname = 'Opera';
        $ub = "Opera";
    }
    elseif(preg_match('/Netscape/i',$u_agent))
    {
        $bname = 'Netscape';
        $ub = "Netscape";
    }
   
    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number just continue
    }
   
    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
            $version= $matches['version'][0];
        }
        else {
            $version= $matches['version'][1];
        }
    }
    else {
        $version= $matches['version'][0];
    }
   
    // check if we have a number
    if ($version==null || $version=="") {$version="?";}
   
    return array(
        'userAgent' => $u_agent,
        'name'      => $bname,
        'version'   => $version,
        'platform'  => $platform,
        'pattern'    => $pattern
    );
} 
    
    
///fonctions pour le flux rss
function getAtelierDuMois(){
	$sql="SELECT *
FROM `tab_atelier`,tab_atelier_sujet
WHERE MONTH( `date_atelier` ) = MONTH( NOW( ) )
AND YEAR( `date_atelier` ) = YEAR( NOW( ) ) 
AND `tab_atelier`.id_sujet=tab_atelier_sujet.`id_sujet`";
		$db=opendb();
 $result = mysqli_query($db,$sql);
  closedb($db);
  
  if (mysqli_num_rows($result)==0)
  {
      return FALSE ;
  }
  else
  {
      return $result;
  }
	
}

?>


