<style type="text/css">
#consoleafficher
{
	height:500pt;
	width:700pt;
} 
</style> 
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
 

 include/admin_console.php V0.1
*/
/*function trigger() {
        echo '<script type="text/javascript">window.alert("'.$chargé.'");</script>';
}*/

// console
	//}

?>
<table width="100%">
    <form method="post" action="index.php?a=45">
        <tr class="list_salle">
            <td align="right" colspan="4">Salle : <select name="numsalle">
            <option> --- </option>
	    <?php
		$resultsalle=getAllSalle();
		$nbsalle=mysqli_num_rows($resultsalle);
	    for($i=1; $i<=$nbsalle; $i++)
		{
		$rowsalle = mysqli_fetch_array($resultsalle);
			
		    echo "<option value=\"".$rowsalle["id_salle"]."\">".$rowsalle["nom_salle"]."</option>";
	    }
	    ?>
    </select><input type="submit" value="Ok" onclick="request(readData);"></td></tr>
    </form></table>
<?php 
	if (FALSE!=isset($_POST['numsalle']) AND FALSE!=is_numeric($_POST['numsalle']))
    {
		/*echo '<script type="text/javascript">window.alert("'.$_SESSION['numsalle'].'");</script>';*/
        //$_SESSION['numsalle']=$_POST['numsalle'] ;
		
		//si une salle est demandé
		if($_POST['numsalle']!=0)
		{
			echo "<input type=\"hidden\" id=\"numconsole\" value=\"".$_POST['numsalle']."\"/>";
			echo "<div id=\"consoleafficher\" align=\"center\"><img src=\"img/ajax-loader.gif\"></div>";
			echo "<div id=\"actionconsoleafficher\" align=\"center\"></div>";
    	}
	}
?>