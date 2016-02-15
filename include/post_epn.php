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
 

 include/post_epn.php V0.1
*/

// fichier de recuperation des variables du formulaire espace

$act      =  $_GET["act"];
$id       =  $_GET["idespace"];

$nom     = addslashes($_POST["nom"]) ;
$adresse = addslashes($_POST["adresse"]) ;
$ville      = $_POST["ville"] ;
$tel      = $_POST["telephone"] ;
$fax      = $_POST["fax"] ;
$couleur=$_POST["ecouleur"];
$logoespace=$_POST["elogo"];
$mail=$_POST["mail"] ;
//debug($_POST);

if ($act !="" AND $act!=3)  // verife si non vide
{
  // Traitement des champs a ins�rer
    if (!$nom || !$ville )
    {
       $mess = getError(4);
    }
    else
    {
        switch($act)  
        {
            case 1:   // ajout d'un epn
            $idespace = addEspace($nom,$adresse,$ville,$tel,$fax,$logoespace,$couleur,$mail) ;
                 if (FALSE == $idespace)
                 {
                     header("Location: ./index.php?a=43&mesno=0");
                 }
                 else
                 {
									copyhoraires($idespace);
									copyconfig($idespace,'0',$nom);
									
									header("Location: ./index.php?a=43");
                 }
            break;
            case 2:   // modifie un espace
                 if (FALSE == modEspace($id, $nom,$adresse,$ville,$tel,$fax,$logoespace,$couleur,$mail))
                 {
                     header("Location: ./index.php?a=43&mesno=0");
                 }
                 else
                 {
										modifconfigespace($id,$nom,2,'','');
										header("Location: ./index.php?a=43");
                 }
            break;
        }
    }
}
if ($act==3) // supprime un espace
{
  if (FALSE == supEspace($id))
  {
      header("Location: ./index.php?a=43&mesno=0");
  }
  else
  {
      header("Location: ./index.php?a=43");
  }
}
?>