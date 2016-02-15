<?php
include("../connect_db.php");
include("../include/fonction.php");
require("../fpdf.php");



	/* variables contenu texte */
	$emetteur = array("nom"=>"","adr"=>"","cp"=>'');
	$destinataire = array("nom"=>"","adr"=>"","cp"=>'');
	$objet = 'Liste de vos inscriptions aux sessions';
	$epn=$_GET['epn'];
	$user=$_GET['user'];
	
	/*Recuperation des donnees  de l'epn emetteur*/ 
	$db = mysqli_connect($host,$userdb,$passdb,$database) ;
	$sql = "SELECT `nom_espace` , `adresse` , `nom_city` , `code_postale_city`, logo_espace
		FROM `tab_espace` , `tab_city` 
		WHERE `tab_city`.`id_city`=`tab_espace`.`id_city` 
		AND `tab_espace`.`id_espace` = '".$epn."' ";
	$row = mysqli_query($db, $sql);
	mysqli_close ($db) ;
	$pEpn=mysqli_fetch_array($row);
    
   $emetteur['nom'] = utf8_decode($pEpn['nom_espace']);
    $emetteur['adr'] = utf8_decode($pEpn['adresse']);
    $emetteur['cp']=$pEpn['code_postale_city'];
    $ville=$pEpn['nom_city'];
     $logo='../img/logo/'.$pEpn['logo_espace'];
     
/*Recuperation des donnees  de l'utilisateur*/ 
	$db = mysqli_connect($host,$userdb,$passdb,$database) ;
	$sql="SELECT nom_user, prenom_user, adresse_user, ville_user, nom_city, `code_postale_city` , ville_user
		FROM tab_user, tab_city
		WHERE id_user=".$user."
		AND tab_city.id_city=tab_user.ville_user";
	$row = mysqli_query($db, $sql);
	mysqli_close ($db) ;
    $resultuser=mysqli_fetch_array($row);
    $destinataire['nom'] = utf8_decode($resultuser['prenom_user']." ".$resultuser['nom_user']);
     $destinataire['adr'] = $resultuser['adresse_user'];
		if($resultuser['ville_user']>16){
			 $destinataire['cp']="";
		}else{
			$destinataire['cp']=$resultuser['code_postale_city']." ".$resultuser['nom_city'];
		}
//**************************************//
   //texte d'intro fichier externe
	$fichierparag='txt_lettre/text_intro_session.txt';
	$f=fopen($fichierparag,'r');
	$paragraphe =fread($f,filesize($fichierparag));
	fclose($f);
   
   //Texte de politesse fichier externe
	$fichierpolitesse='txt_lettre/text_closure_session.txt';
	//Lecture du fichier texte
	$f=fopen($fichierpolitesse,'r');
	$politesse=fread($f,filesize($fichierpolitesse));
	fclose($f);
//***************************************//
//Recuperation de laliste des sessions //
$an=date('Y')."-".date('m')."-".date('d');
$db = mysqli_connect($host,$userdb,$passdb,$database) ;

$sql="SELECT rel_session_user.`id_session`,`session_titre`,`session_detail`
FROM `rel_session_user`,tab_session,tab_session_sujet
WHERE `id_user` ='".$user."'
AND tab_session.`id_session`=rel_session_user.`id_session`
AND `status_rel_session` =0
AND `status_session` =0
AND tab_session_sujet.`id_session_sujet`= tab_session.nom_session
GROUP BY rel_session_user.`id_session` 
";
$rowsession = mysqli_query($db, $sql);
mysqli_close ($db) ;
if($rowsession==FALSE){$nbses=0;}else{ $nbses=mysqli_num_rows($rowsession);}	
	
	

//recuperation de la liste des ateliers en attente
$db = mysqli_connect($host,$userdb,$passdb,$database) ;
$sql="SELECT rel_session_user.`id_session`,`session_titre`,`session_detail`
FROM `rel_session_user`,tab_session,tab_session_sujet
WHERE `id_user` ='".$user."'
AND tab_session.`id_session`=rel_session_user.`id_session`
AND `status_rel_session` =2
AND `status_session` =0
AND tab_session_sujet.`id_session_sujet`= tab_session.nom_session
GROUP BY rel_session_user.`id_session` ";
$rowsessionattente = mysqli_query($db, $sql);
mysqli_close ($db) ;

if(mysqli_num_rows($rowsessionattente)==0){
	$attente=0;
	}else{
	$nba=mysqli_num_rows($rowsessionattente);
	$attente=1;
}
	


///**********ECRITURE DU COURRIER
	/* initialisation/configuration de la classe*/
	$courrier = new FPDF();
	//font declaration des polices !
	$courrier->AddFont('SourceSansPro-Regular','','SourceSansPro-Regular.php');
	$courrier->AddFont('SourceSansPro-Semibold','','SourceSansPro-Semibold.php');
	$courrier->AddFont('SourceSansPro-LightItalic','','SourceSansPro-LightItalic.php');
	
	$courrier->SetFont('SourceSansPro-Regular','',12);
	
	$courrier->open();
	$courrier->SetAutoPageBreak(1,15);
	$courrier->AddPage();
	
	/* Création bloc emetteur */
	$courrier->SetFont('SourceSansPro-Regular','',12);
	$courrier->Image($logo,5,10,45,27);
	$courrier->Ln(20);
	$courrier->SetXY(55,15);
	$courrier->Cell(0,5,$emetteur['nom'],0,2,'L',false);//largeur,hauteur,,texte,bodure,suite,alignement,couleur de fond;
	$courrier->SetX(55);
	$courrier->MultiCell(0,5,$emetteur['adr'],0,'L',false);//largeur,hauteur,,texte,bodure,suite,alignement,couleur de fond;
	$courrier->SetX(55);
	$courrier->MultiCell(0,5,$emetteur['cp']." ".$ville,0,'L',false);//largeur,hauteur,,texte,bodure,suite,alignement,couleur de fond;
	$courrier->Ln(20);
	
	/* Création bloc destinataire */
	$courrier->SetFont('SourceSansPro-Regular','',12);
	$courrier->SetX(120);
	$courrier->Cell(0,5,$destinataire['nom'],0,1,'L',false);//largeur,hauteur,,texte,bodure,suite,alignement,couleur de fond;
	$courrier->SetX(120);
	$courrier->MultiCell(0,5,$destinataire['adr'],0,'L',false);//largeur,hauteur,,texte,bodure,suite,alignement,couleur de fond;
	$courrier->SetX(120);
	$courrier->Cell(0,5,$destinataire['cp'],0,1,'L',false);//largeur,hauteur,,texte,bodure,suite,alignement,couleur de fond;
	$courrier->Ln(10);
	
	
	/* date et heure */
	$semaine = array("dimanche","lundi","mardi","mercredi","jeudi","vendredi","samedi");
	$mois = array("","janvier","février","mars","avril","mai","juin","juillet","aout","septembre","octobre","novembre","décembre");
	$dateLieu = "A ".utf8_decode($ville).", le ".$semaine[date("w")].' '.date("j")." ".$mois[date("n")]." ".date("Y");
	$courrier->Cell(0,5,$dateLieu,0,1,'R',false);//largeur,hauteur,,texte,bodure,suite,alignement,couleur de fond;
	$courrier->Ln(10);
	
	/* objet */
	$courrier->SetFont('SourceSansPro-Regular','',12);
	$courrier->Cell(0,6,$objet,0,2,'C',false);//largeur,hauteur,,texte,bodure,suite,alignement,couleur de fond;
	$courrier->Ln(10);
	
	/* paragraphe d'intro */
	$courrier->SetFont('SourceSansPro-Regular','',12);
	$courrier->MultiCell(0,5,$paragraphe,0,'J',false);
	$courrier->Ln(10);
	
//********liste des sessions enregistr�es
	
	$courrier->SetFont('SourceSansPro-Regular','',12);
	$courrier->Ln();
	//Donn�es
	$dates='';
	if($nbses>0){
	while($rowS=mysqli_fetch_array($rowsession))
		{
	///recuperer la liste des dates
	$ids=$rowS["id_session"];
	$db = mysqli_connect($host,$userdb,$passdb,$database) ;
		$sqldates="SELECT `date_session` FROM `tab_session_dates` WHERE `id_session` ='".$ids."'";
		$rowsdates = mysqli_query($db, $sqldates);
		mysqli_close ($db) ;
	$nbrdates=mysqli_num_rows($rowsdates);
	
	for ($f=0; $f<$nbrdates ; $f++){
		$rowd=mysqli_fetch_array($rowsdates);
		$dates=$dates.getDatefr($rowd["date_session"])."\n";
	
	}
	
	
	
		$courrier->SetFont('SourceSansPro-Semibold','',12);
		$titre=$rowS['session_titre']." : ";
		$courrier->Write(7,$titre);
		$courrier->Ln();
		$courrier->SetFont('SourceSansPro-LightItalic','',12);
		$courrier->Write(5,$rowS['session_detail']);
		$courrier->Ln(7);
		
		$courrier->SetFont('SourceSansPro-Regular','',12);
		$courrier->SetX(30);
		$courrier->Multicell(0,5,$dates,0,'L',false);
		
		$courrier->Ln();
		
		$dates="";
		} 
	 }
	 else{
		 $courrier->Write(5,html_entity_decode("Vous n'&ecirc;tes inscrit &agrave; aucune session actuellement."));
	 }
	$courrier->Ln(15);
	
//****Liste d'attente aux ateliers 
	if($attente>0){
	$courrier->MultiCell(0,5,html_entity_decode('Vous &ecirc;tes &ecute;galement inscrit en attente pour les sessions suivantes'),0,'J',false);

	
	if($nba>0){
	while($rowa=mysqli_fetch_array($rowsessionattente))
		{
	///recuperer la liste des dates
	$idsa=$rowa["id_session"];
	$db = mysqli_connect($host,$userdb,$passdb,$database) ;
		$sqldates="SELECT `date_session` FROM `tab_session_dates` WHERE `id_session` ='".$idsa."'";
		$rowsdates = mysqli_query($db, $sqldates);
		mysqli_close ($db) ;
	$nbrdates=mysqli_num_rows($rowsdates);
	
	for ($f=0; $f<$nbrdates ; $f++){
		$rowd=mysqli_fetch_array($rowsdates);
		$dates=$dates.getDatefr($rowd["date_session"])."\n";
	
	}
	
	
	
		$courrier->SetFont('SourceSansPro-Semibold','',12);
		$titre=$rowa['session_titre']." : ";
		$courrier->Write(7,$titre);
		$courrier->Ln();
		$courrier->SetFont('SourceSansPro-LightItalic','',12);
		$courrier->Write(5,$rowa['session_detail']);
		$courrier->Ln(7);
	
		$courrier->SetFont('SourceSansPro-Regular','',12);
		$courrier->Multicell(0,7,$dates,0,'L',false);
		
		$courrier->Ln();
		
		$dates="";
		} 
		
	}else{
		$courrier->SetFont('SourceSansPro-Regular','',12);
		$courrier->Write(5,utf8_decode("Vous n'êtes inscrit à aucune session sur liste d'attente."));
	} 
	 
	$courrier->Ln(15);
	
	}
	
	
	/* politesse */
	
	//$courrier->Image('../img/logos_courrier.jpg',10,275,35);
	$courrier->SetFont('SourceSansPro-Regular','',8);
	$courrier->SetXY(40,270);
	$courrier->MultiCell(0,5,$politesse,'T','C',false);
	
	$courrier->Output();
?>
