<?php

$dir_path = "./";
require("config.php");
require("functions/db.php");
require("functions/stats.php");
require("functions/textparse.php");

print $header;

$query = mysql_query("SELECT id FROM $table_stats");
$query2 = mysql_query("SELECT DISTINCT mask FROM $table_stats");
$query3 = mysql_query("SELECT DISTINCT mask FROM $table_stats ORDER BY mask DESC LIMIT 10");

print '<table><div class="title"> # of visits/hits :</div>
	<tr>
		<td align="right">total number of page hits :</td>
		<td>'.mysql_num_rows($query).'</td>
	</tr>
	<tr>
		<td align="right">total number of unique visits :</td>
		<td>'.mysql_num_rows($query2).'</td>
	</tr>
</table><div class="title"># of visits by host (last 10) :</div><table>';

while($unique = mysql_fetch_array($query3)){

	$mask = $unique['mask'];
	$query4 = mysql_query("SELECT mask FROM $table_stats WHERE mask='$mask'");
	print '<tr>
			<td align="right">'.ip_cut($mask).'</td>
			<td> : '.mysql_num_rows($query4).'</td>
		   </tr>';;

}

print "</table>".$footer;

?>