<?php
include("../connect_db.php");
include("../include/fonction.php");
require("../fpdf.php");



	/* variables contenu texte */
	$emetteur = array("nom"=>"","adr"=>"","cp"=>'');
	$destinataire = array("nom"=>"","adr"=>"","cp"=>'');
	$objet = 'Liste de vos inscriptions aux ateliers';
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
	$sql="SELECT nom_user, prenom_user, adresse_user, ville_user, nom_city, `code_postale_city` 
		FROM tab_user, tab_city
		WHERE id_user=".$user." 
		AND tab_city.id_city=tab_user.ville_user";
	$row = mysqli_query($db, $sql);
	mysqli_close ($db) ;
    $resultuser=mysqli_fetch_array($row);
    $destinataire['nom'] = utf8_decode($resultuser['prenom_user']." ".$resultuser['nom_user']);
    $destinataire['adr'] = utf8_decode($resultuser['adresse_user']);
    $destinataire['cp']=$resultuser['code_postale_city']." ".utf8_decode($resultuser['nom_city']);
//**************************************//
   //texte d'intro fichier externe
	$fichierparag='txt_lettre/text_intro.txt';
	$f=fopen($fichierparag,'r');
	$paragraphe =fread($f,filesize($fichierparag));
	fclose($f);
   
   //Texte de politesse fichier externe
	$fichierpolitesse='txt_lettre/text_closure.txt';
	//Lecture du fichier texte
	$f=fopen($fichierpolitesse,'r');
	$politesse=fread($f,filesize($fichierpolitesse));
	fclose($f);
//***************************************//
//Recuperation de laliste des ateliers //
$an=date('Y')."-".date('m')."-".date('d');
$db = mysqli_connect($host,$userdb,$passdb,$database) ;
$sql="SELECT atelier.id_atelier,`date_atelier`,`heure_atelier`,duree_atelier, suj.label_atelier
		FROM `tab_atelier` AS atelier, `rel_atelier_user` AS rel, tab_atelier_sujet AS suj
		WHERE atelier.id_atelier = rel.id_atelier
		AND atelier.id_sujet=suj.id_sujet
		AND rel.id_user=".$user."
		AND date_atelier >= '".$an."'
		AND status_rel_atelier_user < 2
		ORDER BY `date_atelier` ASC";
$rowatelier = mysqli_query($db, $sql);
mysqli_close ($db) ;
if($rowatelier==FALSE){$nba=0 ;}else{$nba=1;}
	

//recuperation de la liste des ateliers en attente
$db = mysqli_connect($host,$userdb,$passdb,$database) ;
$sql="SELECT atelier.id_atelier,`date_atelier`, `heure_atelier`,duree_atelier, suj.label_atelier
		FROM `tab_atelier` AS atelier, `rel_atelier_user` AS rel, tab_atelier_sujet AS suj
		WHERE atelier.id_atelier = rel.id_atelier
		AND atelier.id_sujet=suj.id_sujet
		AND rel.id_user=".$user."
		AND date_atelier >= '".$an."'
		AND status_rel_atelier_user = 2
		ORDER BY `date_atelier` ASC";
$rowatelierattente = mysqli_query($db, $sql);
mysqli_close ($db) ;
$nbattente=mysqli_num_rows($rowatelierattente);	
		

///**********ECRITURE DU COURRIER
	/* initialisation/configuration de la classe*/
	$courrier = new FPDF();
	//font
	$courrier->AddFont('SourceSansPro-Regular','','SourceSansPro-Regular.php');
	$courrier->SetFont('SourceSansPro-Regular','',12);
	
	$courrier->open();
	$courrier->SetAutoPageBreak(1, 2);
	$courrier->AddPage();
	$courrier->Ln(30);
	
	
	/* CrÃ©ation bloc emetteur */
	$courrier->SetFont('SourceSansPro-Regular','',12);
	$courrier->Image($logo,10,15,30);
	$courrier->Ln(20);
	$courrier->SetXY(55,20);
	$courrier->Cell(0,5,$emetteur['nom'],0,2,'L',false);//largeur,hauteur,,texte,bodure,suite,alignement,couleur de fond;
	$courrier->SetX(55);
	$courrier->MultiCell(0,5,$emetteur['adr'],0,'L',false);//largeur,hauteur,,texte,bodure,suite,alignement,couleur de fond;
	$courrier->SetX(55);
	$courrier->MultiCell(0,5,$emetteur['cp']." ".$ville,0,'L',false);//largeur,hauteur,,texte,bodure,suite,alignement,couleur de fond;
	$courrier->Ln(20);
	
	/* CrÃ©ation bloc destinataire */
	$courrier->SetFont('SourceSansPro-Regular','',12);
	$courrier->SetX(120);
	$courrier->Cell(0,5,$destinataire['nom'],0,1,'L',false);//largeur,hauteur,,texte,bodure,suite,alignement,couleur de fond;
	$courrier->SetX(120);
	$courrier->Cell(0,5,$destinataire['adr'],0,1,'L',false);//largeur,hauteur,,texte,bodure,suite,alignement,couleur de fond;
	$courrier->SetX(120);
	$courrier->Cell(0,5,$destinataire['cp'],0,1,'L',false);//largeur,hauteur,,texte,bodure,suite,alignement,couleur de fond;
	$courrier->Ln(10);
	
	
	/* date et heure */
	$semaine = array("dimanche","lundi","mardi","mercredi","jeudi","vendredi","samedi");
	$mois = array("","janvier","février","mars","avril","mai","juin","juillet","aout","septembre","octobre","novembre","décembre");
	$dateLieu = "A ".utf8_decode($ville).", le ".$semaine[date("w")].' '.date("j")." ".$mois[date("n")]." ".date("Y");
	$courrier->Cell(0,5,$dateLieu,0,1,'R',false);//largeur,hauteur,,texte,bodure,suite,alignement,couleur de fond;
	$courrier->Ln(10);
	
		
	/* paragraphe d'intro */
	$courrier->SetFont('SourceSansPro-Regular','',12);
	$courrier->MultiCell(0,5,$paragraphe,0,'J',false);
	$courrier->Ln(15);
	
//********liste d'inscription aux ateliers 
	//Couleurs, épaisseur du trait et police grasse
	if($nba>0){
	$courrier->SetFillColor(230,230,230);
	$courrier->SetTextColor(46,46,46);
	$courrier->SetDrawColor(27,42,10);
	//$this->SetLineWidth(.2);
	$courrier->SetFont('SourceSansPro-Regular','',13);
	//En-tête
	$w=array(50,25,25,80);
	$atelier=array('Date','Heure','Durée','Sujet');
	for($i=0;$i<4;$i++){
		$courrier->Cell($w[$i],7,$atelier[$i],0,0,'C',1);
	}
	$courrier->Ln();
	//Restauration des couleurs et de la police
	$courrier->SetFillColor(219,239,204);
	$courrier->SetTextColor(0,0,0,0);
	$courrier->SetFont('SourceSansPro-Regular','',10);
			
	//Données
	$fill=false;
	$courrier->SetAutoPageBreak(1,30);
	
	
	while($row=mysqli_fetch_array($rowatelier))
	 {
	 
	$courrier->cell(50,7,getDayfr($row['date_atelier']),0,0,'C',$fill);
	$courrier->cell(25,7,$row['heure_atelier'],0,0,'C',$fill);
	$courrier->cell(25,7,$row['duree_atelier'] .' min',0,0,'C',$fill);
	$courrier->cell(80,7,$row['label_atelier'],0,1,'C',$fill);
	//getSujet($row['id_sujet']);
	$fill=!$fill;
	 } 
	$courrier->Ln(15);
	}else{
		
		 $courrier->Write(5,"Vous n'êtes inscrit à aucun atelier actuellement.");
	}
//****Liste d'attente aux ateliers 
	if($nbattente>0){
	$courrier->MultiCell(0,5,utf8_decode('Vous êtes inscrit(e) en attente pour les ateliers suivants'),0,'J',false);
	$courrier->SetFillColor(230,230,230);
	$courrier->SetTextColor(46,46,46);
	$courrier->SetDrawColor(27,42,10);
	$courrier->SetFont('SourceSansPro-Regular','','13');
	$w=array(50,25,25,80);
	$atelier=array('Date','Heure', 'Durée','Sujet');
	for($i=0;$i<4;$i++){
		$courrier->Cell($w[$i],7,$atelier[$i],0,0,'C',1);
	}
	$courrier->Ln();
	//Restauration des couleurs et de la police
	$courrier->SetFillColor(219,239,204);
	$courrier->SetTextColor(0,0,0,0);
	$courrier->SetFont('Arial','',10);
	//Données
	$fill=false;
	$courrier->SetAutoPageBreak(1,30);
	while($row2=mysqli_fetch_array($rowatelierattente))
	 {
	$courrier->cell(50,7,getDayfr($row2['date_atelier']),0,0,'C',$fill);
	$courrier->cell(25,7,$row2['heure_atelier'],0,0,'C',$fill);
	$courrier->cell(25,7,$row2['duree_atelier'] .' min',0,0,'C',$fill);
	$courrier->cell(80,7,$row2['label_atelier'],0,1,'C',$fill);
	//getSujet($row['id_sujet']);
	$fill=!$fill;
	 } 
	$courrier->Ln(15);
	
	
	}else{
		 $courrier->Write(5,"Vous n'êtes inscrit à aucun atelier en attente.");
		
	}
	
	
	/* politesse */
	
	//$courrier->Image('../img/logos_courrier.jpg',15,270,35);
	$courrier->SetFont('SourceSansPro-Regular','',8);
	$courrier->SetY(255);
	$courrier->MultiCell(0,4,$politesse,0,'C',false);
	
	$courrier->Output();
?>
