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
 
 2014 SAINT MARTIN Brice
 

 include/console.php V0.1
*/
header("Content-Type: text/html; charset=iso-8859-1");
//header("Content-Type: text/plain");

    include ("../connect_db.php");
    
    /*if ($port=="" OR FALSE==is_numeric($port))
	{
        $port="3306" ;
	}*/
	
    
    /*creation de la liaison avec la base de donnees*/
    $db = mysqli_connect($host,$userdb,$passdb,$database ) ;
	/* Vérification de la connexion */
	if (mysqli_connect_errno()) 
	{
    	return false;
    }
	else
	{
		$salle=$_POST['id_salle'];
					
		//$resultpost = getConsole($salle);
		//récupération de la liste d'ordinateur dans la salle demandé
		$sql="SELECT `nom_computer`, `id_computer`, `adresse_ip_computer`,`date_lastetat_computer`, `lastetat_computer`, `usage_computer` FROM tab_computer WHERE id_salle=".$salle." ORDER BY nom_computer;";
  	$resultpost = mysqli_query($db, $sql);
		//$resultpost = $db->query($sql);
		
		//récupération des informations d'occupation de poste dans la salle demandé			
		$sql="SELECT `nom_computer`, `id_computer`, `nom_user`, `prenom_user`, `status_user`, `date_resa`, `debut_resa` FROM `tab_user`, `tab_computer`, `tab_resa` WHERE `tab_resa`.`id_user_resa`=`tab_user`.`id_user` AND `tab_resa`.`id_computer_resa`=`tab_computer`.`id_computer` AND `tab_computer`.`id_salle`='".$salle."' AND `tab_resa`.`status_resa`='0' ORDER BY `nom_computer`;";
  	$result = mysqli_query($db, $sql);
		//$result = $db->query($sql); 
		
		//récupération des informations de la salle			
		$sql="SELECT `id_salle`, `nom_salle`, `id_espace`, `comment_salle` FROM tab_salle WHERE id_salle=".$salle.";";
  	$resultsalle = mysqli_query($db, $sql);
		//$resultsalle = $db->query($sql);
					
  		mysqli_close ($db) ;
					
		if (FALSE == $result || FALSE == $resultpost || FALSE == $resultsalle)
		{
						//echo getError(1);
			echo "<div class=\"error\">Impossible de r&eacute;cup&eacute;rer les informations sur l'occupation des postes</div>";
		}
		else  // affichage du resultat
		{
			$rowsalle=mysqli_fetch_array($resultsalle) ;
			$nbpost = mysqli_num_rows($resultpost);
			//$nbpost  = $resultpost->num_rows;
							
			echo "<form name=\"formactionconsole\">
				<table width=\"100%\">
				<tr class=\"list_title\">
				<td colspan=\"4\" align=\"center\" bgcolor=\"#00D2D2\">".$rowsalle['nom_salle']."</td></tr>
				<tr class=\"list_title\">
				<td width=\"100\">Nom Poste</td><td width=\"100\">&Eacute;tat</td><td width=\"100\">Affectation</td><td width=\"100\">Options</td></tr>";
				if ($nbpost > 0)
				{
					$row = mysqli_fetch_array($result) ;
					for ($i=1; $i<=$nbpost; $i++)
					{
						$rowpost = mysqli_fetch_array($resultpost) ;
						if($rowpost["id_computer"] == $row["id_computer"])
						{
							//poste occupé
							$dateresa = $row["date_resa"];
							$heureresa = $row["debut_resa"];
							$datereel = date("Y-m-d");
							$heure = date("H");
							$minute= date("i");
							$heurereel = $heure*60+$minute;
							$j=0;
									
							$nbSecondes= 60*60*24;
				
							$debut_ts = strtotime($dateresa);
							$fin_ts = strtotime($datereel);
							$diff = $fin_ts - $debut_ts;
							$diffjour=round($diff / $nbSecondes);
									
							//jour meme
							if($diffjour==0)
							{
								$minutepasser=$heurereel-$heureresa;
								if($minutepasser ==0)
								{
									$heures = 0;
									$minutes= 0;
									$sec =1;
								}
								else if($minutepasser < 60)
								{
									$heures = 0;
									$minutes= $minutepasser;
									$sec =0;
								}
										else if($minutepasser >= 60)
										{
											$heures  = floor(($minutepasser)/60);
											$minutes = $minutepasser-($heures*60);
											$sec =0;
										}
										// creation de la variable time //
										if ($minutes == 0)
										{
											if($sec==1)
											{
												$time = "<1mm" ;
											}
											else
											{
												$time = $heures."h" ;
											}
										}
										else
										{
											if ($heures == 0)
											{
												$time = $minutes."mn" ;
											}
											else
											{
												if($minutes<10)
												{
													$minutes2='0'.$minutes;
												}
												else
												{
													$minutes2=$minutes;
												}
												$time = $heures."h".$minutes2;
											}
										}
									}
									//jour suivant
									else if($diffjour==1)
									{
										if($heurereel<$heureresa)	//inferieur a 24h
										{									
											$minutepassed=1439-$heureresa;		//24h-heure resa
											$minutepasser=$minutepassed+$heurereel;
											if($minutepasser < 60)
											{
												$heures = 0;
												$minutes= $minutepasser;
											}
											else
											{
												$heures  = floor(($minutepasser)/60);
												$minutes = $minutepasser-($heures*60);
											}
											// creation de la variable time //
											if ($minutes == 0)
											{
												$time = $heures."h" ;
											}
											else
											{
												if ($heures == 0)
												{
													$time = $minutes."mn" ;
												}
												else
												{
													if($minutes<10)
													{
														$minutes2='0'.$minutes;
													}
													else
													{
														$minutes2=$minutes;
													}
													$time = $heures."h".$minutes2;
												}
											}
										}
										else if($heurereel>=$heureresa)//supérieur à 24h
										{
											$heurepasser=24;	//24h
										
											$minutepasser=$heurereel-$heureresa;
											if($minutepasser < 60)
											{
												$heures = 0;
												$minutes= $minutepasser;
											}
											else
											{
												$heures  = floor(($minutepasser)/60);
												$minutes = $minutepasser-($heures*60);
											}
											$tempsheurepasser=$heurepasser+$heures;
											// creation de la variable time //
											if ($minutes == 0)
											{
												$time = $tempsheurepasser."h" ;
											}
											else
											{
												if ($tempsheurepasser == 0)
												{
													$time = $minutes."mn" ;
												}
												else
												{
													if($minutes<10)
													{
														$minutes2='0'.$minutes;
													}
													else
													{
														$minutes2=$minutes;
													}
													$time = $tempsheurepasser."h".$minutes2;
												}
											}
										}
									}
									//jour supérieur à 1
									else if($diffjour>1)
									{
										if($heurereel<=$heureresa)	//si inferieur à x jour complet
										{
											$diffjour--;
											$heurepasser=$diffjour*24;
										
											$minutepassed=1439-$heureresa;
											$minutepasser=$minutepassed+$heurereel;
											if($minutepasser < 60)
											{
												$heures = 0;
												$minutes= $minutepasser;
											}
											else
											{
												$heures  = floor(($minutepasser)/60);
												$minutes = $minutepasser-($heures*60);
											}
											$tempsheurepasser=$heurepasser+$heures;
											// creation de la variable time //
											if ($minutes == 0)
											{
												$time = $tempsheurepasser."h" ;
											}
											else
											{
												if ($tempsheurepasser == 0)
												{
													$time = $minutes."mn" ;
												}
												else
												{
													if($minutes<10)
													{
														$minutes2='0'.$minutes;
													}
													else
													{
														$minutes2=$minutes;
													}
													$time = $tempsheurepasser."h".$minutes2;
												}
											}
										}
										else if($heurereel>=$heureresa)	//si superieur a x jours complet
										{
											//$jourpasser=$datereel-$dateresanum;//48
											$heurepasser=$diffjour*24;
											//$heurepasser=24;
										
											$minutepasser=$heurereel-$heureresa;
											if($minutepasser < 60)
											{
												$heures = 0;
												$minutes= $minutepasser;
											}
											else
											{
												$heures  = floor(($minutepasser)/60);
												$minutes = $minutepasser-($heures*60);
											}
											$tempsheurepasser=$heurepasser+$heures;
											// creation de la variable time //
											if ($minutes == 0)
											{
												$time = $tempsheurepasser."h" ;
											}
											else
											{
												if ($tempsheurepasser == 0)
												{
													$time = $minutes."mn" ;
												}
												else
												{
													if($minutes<10)
													{
														$minutes2='0'.$minutes;
													}
													else
													{
														$minutes2=$minutes;
													}
													$time = $tempsheurepasser."h".$minutes2;
												}
											}
										}
									}
									if($row["status_user"]==1)
									{
										echo"<tr class=\"list_console_occup\">
											<td>".$row["nom_computer"]."</td>
											<td>Occup&eacute;</td>
											<td>".$row["nom_user"]." ".$row["prenom_user"]." (".$time.")</td>
											<td align=\"center\"><select name=\"option_console\" onchange=\"ActionConsole();\">
											<option value=\"0\">-----</option>
											<option value=\"action=2&id_poste=".$rowpost["id_computer"]."\">Liberation</option>
											</select>
											</td></tr>";
									}
									else if($row["status_user"]==3)
									{
										echo"<tr class=\"list_console_occup\">
											<td>".$row["nom_computer"]."</td>
											<td>Occup&eacute;</td>
											<td>".$row["nom_user"]." ".$row["prenom_user"]." (Animateur)</td>
											<td align=\"center\"></td></tr>";
									}
									else if($row["status_user"]==4)
									{
										echo"<tr class=\"list_console_occup\">
											<td>".$row["nom_computer"]."</td>
											<td>Occup&eacute;</td>
											<td>".$row["nom_user"]." ".$row["prenom_user"]." (Administrateur)</td>
											<td align=\"center\"></td></tr>";
									}
									$row = mysqli_fetch_array($result) ;	
								}
								else if($rowpost["id_computer"] != $row["id_computer"])
								{	
									//poste libre
									$datelastetat = $rowpost["date_lastetat_computer"];
									$heurelastetat = $rowpost["lastetat_computer"];
									$datereel = date("Y-m-d");
									$heure = date("H");
									$minute= date("i");
									$seconde= date("s");
									$heurereel = $heure*3600;
									$minutereel= $minute*60;
									$tempsreel= $heurereel+$minutereel+$seconde;
									$j=0;
									
									$nbSecondes= 60*60*24;
				
									$debut_ts = strtotime($datelastetat);
									$fin_ts = strtotime($datereel);
									$diff = $fin_ts - $debut_ts;
									$diffjour=round($diff / $nbSecondes);
									
									//jour meme
									if($diffjour==0)
									{
										$tempsdiff=$tempsreel-$heurelastetat;
										if($tempsdiff >= 15)	//ordinateur éteint ou logciel non lancé (teste avec marge de 15 secondes).
										{
											if($rowpost["usage_computer"]==1)
											{
												echo "<tr class=\"list\">
													<td>".$rowpost["nom_computer"]."</td>
													<td>&Eacute;teint</td>
													<td>-</td>
													<td align=\"center\"><select name=\"option_console\" onchange=\"affect_user_computer();\">
													<option value=\"0\">-----</option>
													<option value=\"id_poste=".$rowpost["id_computer"]."\">Affectation</option>
													</select>
													</td></tr>";
											}
											else if($rowpost["usage_computer"]==2)
											{
												echo "<tr class=\"list\">
													<td>".$rowpost["nom_computer"]."</td>
													<td>&Eacute;teint</td>
													<td>-</td>
													<td align=\"center\"></td>
													</tr>";
											}
											/*echo "<tr class=\"list\">
												<td>".$rowpost["nom_computer"]."</td>
												<td>&Eacute;teint</td>
												<td>-</td>
												<td align=\"center\"><select name=\"option_console\">
												<option value=\"0\">-----</option>
												</select>
												</td></tr>";*/
										}
										else	//ordinateur allumé et logiciel lancé
										{
											if($rowpost["usage_computer"]==1)
											{
												echo "<tr class=\"list\">
													<td>".$rowpost["nom_computer"]."</td>
													<td>Libre</td>
													<td>-</td>
													<td align=\"center\"><select name=\"option_console\" onchange=\"affect_user_computer();\">
													<option value=\"0\">-----</option>
													<option value=\"id_poste=".$rowpost["id_computer"]."\">Affectation</option>
													</select>
													</td></tr>";
											}
											else if($rowpost["usage_computer"]==2)
											{
												echo "<tr class=\"list\">
													<td>".$rowpost["nom_computer"]."</td>
													<td>Libre</td>
													<td>-</td>
													<td align=\"center\"></td>
													</tr>";
											}
										}
									}
									else 	//ordinateur éteint ou logiciel non lancé
									{
											if($rowpost["usage_computer"]==1)
											{
												echo "<tr class=\"list\">
													<td>".$rowpost["nom_computer"]."</td>
													<td>&Eacute;teint</td>
													<td>-</td>
													<td align=\"center\"><select name=\"option_console\" onchange=\"affect_user_computer();\">
													<option value=\"0\">-----</option>
													<option value=\"id_poste=".$rowpost["id_computer"]."\">Affectation</option>
													</select>
													</td></tr>";
											}
											else if($rowpost["usage_computer"]==2)
											{
												echo "<tr class=\"list\">
													<td>".$rowpost["nom_computer"]."</td>
													<td>&Eacute;teint</td>
													<td>-</td>
													<td align=\"center\"></td>
													</tr>";
											}
										/*echo "<tr class=\"list\">
											<td>".$rowpost["nom_computer"]."</td>
											<td>&Eacute;teint</td>
											<td>-</td>
											<td align=\"center\"><select name=\"option_console\">
											<option value=\"0\">-----</option>
											</select>
											</td></tr>";*/
									}
								}
							}
							echo "</table><br /></form>";
						}
						else
						{
							echo "</table></form>
							<table width=\"100%\">
							<tr class=\"list\" align=\"center\"><td>Aucun poste n'est pr&eacute;sent dans cette salle</td> </tr></table>";
						}
					}
	}
?>