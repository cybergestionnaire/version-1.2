
<?php
//header("Content-Type: text/html; charset=iso-8859-1");
header("Content-Type: text/plain");
	
/*echo '<script type="text/javascript">window.alert("test");</script>';	*/
					
if (isset($_GET["action"]) && isset($_GET["adresse"])) 
{
    if (!empty($_GET["action"]) && !empty($_GET["adresse"])) 
    {		
		include ("../connect_db.php");
		
		if ($port=="" OR FALSE==is_numeric($port))
		{
			$port="3306" ;
		}
		
		/*creation de la liaison avec la base de donnees*/
		$db = mysqli_connect($host,$userdb,$passdb,$database ) ;
		
		$action=$_GET["action"];	
		$adresse=$_GET["adresse"];
		if (isset($_GET["id_poste"]) && !empty($_GET["id_poste"])) 
		{
			$id_poste=$_GET["id_poste"];
		}
		else if (isset($_GET["id_user"]) && !empty($_GET["id_user"])) 
		{
			$id_user=$_GET["id_user"];
		}
		else if (isset($_GET["dureeaffect"]) && !empty($_GET["dureeaffect"])) 
		{
			$dureeaffect=$_GET["dureeaffect"];
		}
		else if (isset($_GET["id_atelier"]) && !empty($_GET["id_atelier"])) 
		{
			$id_atelier=$_GET["id_atelier"];
		}
		else if (isset($_GET["message"]) && !empty($_GET["message"])) 
		{
			$message=$_GET["message"];
		}
		
		if($action==1)	//affectation usagers
		{
			$date=date("Y-m-d");
			$heure=date("H");
			$minute=date("i");
			$debut=(($heure*60)+$minute);
			$sql="INSERT INTO `tab_resa` (`id_resa`, `id_computer_resa`, `id_user_resa`, `dateresa_resa`,`debut_resa`, `duree_resa`, `date_resa`, `status_resa`) VALUES('', '".$id_poste."', '".$id_user."', '".$date."', '".$debut."', '".$duree."', '".$date."', '0');";
  	$result = mysqli_query($db, $sql);
			//$result = $db->query($sql);
		}
		else if($action==2)	//libération usagers
		{
			//recherche id_resa
			$sql="SELECT `id_resa`, `dateresa_resa`, `debut_resa` FROM tab_resa WHERE `id_computer_resa`='".$id_poste."' AND `status_resa`='0' LIMIT 1;";
			$result = $db->query($sql);
			
			$row=mysqli_fetch_array($result) ;
			
			//poste occupé
			$dateresa = $row["dateresa_resa"];
			$heureresa = $row["debut_resa"];	//heure resa en minute
			$datereel = date("Y-m-d");
			$heure = date("H");
			$minute= date("i");
			$heurereel = (($heure*60)+$minute);	//heure reel en minute
			
			$nbSecondes= 60*60*24;
			$debut_ts = strtotime($dateresa);
			$fin_ts = strtotime($datereel);
			$diff = $fin_ts - $debut_ts;
			$diffjour=round($diff / $nbSecondes);	//difference de jour entre date resa et date réel (en jour)
			//jour meme
			if($diffjour==0)
			{
				$dureetotal=$heurereel-$heureresa;	//durée total en minutes
			}
			//lendemain
			else if($diffjour==1)
			{
				if($heurereel<$heureresa)	//moins de 24 h
				{
					$duree1=1439-$heureresa;	//temps j1 (en minutes)
					$dureetotal=$duree1+$heurereel;	//durée total en minutes
				}
				else	//plus de 24h
				{
					$duree1=$heureel-$heureresa;	//durée en minutes
					$dureetotal=$duree1+1440;		//durée total en minutes
				}
			}
			//jour d'après
			else if($diffjour>1)
			{
				if($heurereel<$heureresa)	//moins de 24 h
				{
					$diffjour--;	//on retire un jour non révolu
					$duree1=1439-$heureresa;	//temps j1 en minutes
					$duree2=$duree1+$heurereel;	//temps j2 en minutes
					$dureetotal=(($diffjour*1440)+$duree2);	//durée total en minutes
				}
				else	//plus de 24h
				{
					$duree1=$heurereel-$heureresa;	//durée en minutes
					$dureetotal=($duree1+($diffjour*1440));	//durée total en minutes
				}
			}
			
			$sql="UPDATE `tab_resa` SET `duree_resa` = '".$dureetotal."', `status_resa`='1' WHERE `id_resa`= '".$row["id_resa"]."';";
  	$result = mysqli_query($db, $sql);
			//$result = $db->query($sql);
		}
		
  		mysqli_close($db);
		//démarrage du socket
		/*$service_port = "18181";
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
			if($action==1)		//affectation d'un usagers sur un poste
			{
				$msg = "menu=1";
				socket_write($socket, $msg, strlen($msg));
			}
			else if($action==2)		//libération d'un usagers d'un poste
			{
				$msg = "menu=2";
				socket_write($socket, $msg, strlen($msg));
			}
			else if($action==3)		//envoie d'un message sur un poste
			{
				$msg = "menu=3";
				socket_write($socket, $msg, strlen($msg));
			}
			else if($action==5)		//démarrage du controle à distance d'un poste	/*envoie poste animateur avant poste client
			{
				$msg = "menu=5";
				socket_write($socket, $msg, strlen($msg));
			}
			else if($action==6)		//démarrage d'un poste à distance	/*envoie poste animateur avant poste client
			{
				$msg = "menu=6";
				socket_write($socket, $msg, strlen($msg));
			}
			else if($action==7)		//arret d'un poste à distance	/*envoie sur poste animateur avant poste client
			{
				$msg = "menu=7";
				socket_write($socket, $msg, strlen($msg));
			}
			else if($action==8)		//arret du logiciel EPN-Connect à distance
			{
				$msg = "menu=8";
				socket_write($socket, $msg, strlen($msg));
			}
			else if($action==19)		//redemmarage du logiciel EPN-Connect à distance
			{
				$msg = "menu=9";
				socket_write($socket, $msg, strlen($msg));
			}
			
			socket_close($socket);
			
			return TRUE;
		}*/
	}
}
?>