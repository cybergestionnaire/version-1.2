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
 2013 Florence DAUVERGNE
 
*/
//----
//----------
//------------ Validation de la resa rapide -------------------------------------------------------------------///
$duree=$_POST["duree"];
$heure=$_POST["heure"];
$date=$_POST["date"];
$id_user=$_POST["adh_submit"];
$id_poste=$_POST["idcomp"];
$restant=$_POST["restant"];

if($_POST["pastresa"]==1){
	$heurepp=explode(':',$heure);
	$heure=($heurepp[0]*60)+$heurepp[1];
	
}

if (TRUE == isset($_POST['resa_submit'])){
	
    // choix de l'adherent et poste obligatoire
    if(TRUE == isset($_POST['adh_submit']) AND TRUE == isset($_POST['idcomp']))
    {
		
		//reservation du jour
			
			//chargement de la duree de la r�servation en fonction de l'utilisateur et de son temps restant	
			//if ($duree>$restant){$duree=$restant;} //si le temps restant de l'utilisateur est inf�rieur � la dur�e l�gale.
		addResa($id_poste,$id_user,$date,$heure,$duree);
		$messErr = '<div class="col-md-4"><div class="alert alert-success alert-dismissable"><i class="fa fa-check-square"></i>
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;&nbsp;&nbsp;</button>Reservation ajout�e</div></div>' ; 
		
		
	}
	else
	{
	$messErr = '<div class="col-md-4"><div class="alert alert-info alert-dismissable"><i class="fa fa-info"></i>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;&nbsp;&nbsp;</button>Vous devez s&eacute;lectionner un adh&eacute;rent et un poste</div></div>' ; 
	}

}

//----
//----------
//------------ Validation des messages -------------------------------------------------------------------///
$message=addslashes($_POST["chattxt_message"]);
$tags=$_POST["tags_message"];
$id_user=$_POST["chatadh"];
$date=$_POST["chatdate"];
$destinataire=$_POST["chatdestinataire"];
//debug($message);
 if (TRUE == isset($_POST['message_submit'])){
 
	if(TRUE == isset($_POST['chatadh']) AND TRUE == isset($_POST['chattxt_message']))
	    {
		addMessage($date, $id_user, $message,$tags,$destinataire);
		//echo 'message ajout�';
		} 
		else
		{
		$messErr = '<h4 class="alert_info">Vous devez entrer un texte !</h4>' ; 
		}
}
?>