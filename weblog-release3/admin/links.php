<?php

$admin_area = TRUE;
$dir_path ="../";
require($dir_path."config.php");
require($dir_path."functions/db.php");
require($dir_path."functions/authentication.php");
require($dir_path."functions/textparse.php");
require($dir_path."functions/render.php");

if(isset($_REQUEST['adding'])){

	print $header;

	$name = text_in($_REQUEST['link_name']);
	$url = text_in($_REQUEST['link_url']);
	$category = $_REQUEST['link_category'];

	$query = "INSERT INTO $table_links VALUES ('' , '$name' , '$url' , '$category', '".$_SESSION['identity']."')";

	print "Link creation ".insert_db($query)."<br />";

	print $footer;

	exit();

}

if(isset($_REQUEST['add'])){

	$categories = render_categories_menu("category",NULL,$_SESSION['identity']);

	print $header;

	print '<form name="form1" method="post" action="links.php?adding=1">
<div class="title">Adding a link</div><br />
<table>
	<tr>
		<td>Category:</td>
		<td><select name="link_category" class="tbox">
		'.$categories.'
		</td>
	<tr>
		<td align="right">Name:</td>
		<td><input class="tbox" type="text" name="link_name"></td>
	</tr>
	<tr>
		<td align="right">Link:</td>
		<td><input class="tbox" type="text" name="link_url"></td>
	</tr>
	<tr>
		<td><input class="tbox" type="submit" name="Submit" value="add"></td>
	</tr>
</table>
</form>';

	print $footer;
	exit();
}

if(isset($_REQUEST['removing'])){

	$category = $_REQUEST['remove'];
	$table = $table_category;

	print $header;
	print '<div align="center">are you sure you wish to remove the specified item ?
<br /><br /><br />
<b><a href="links.php?delete=1&amp;id='.$_REQUEST['remove'].'">yes</a> / <a href="'.$_SERVER['HTTP_REFERER'].'">no</a></b></div><br />';

	print $footer;

	exit();

}

if(isset($_REQUEST['remove'])){

	$username = $_SESSION['identity'];

	$q_check = mysql_query("SELECT id FROM $table_links WHERE owner = '$username'");
	
	if(mysql_num_rows($q_check) < 1)
	{
		print $header;
		print '<div class="title">Removing links</div><br />
		<i>>>> You have no links to remove</i>';
		print $footer;
		exit();
	}

	$categories = render_categories_menu("category",NULL,$_SESSION['identity']);

	print $header;

	print '<form name="form" method="post" action="links.php?switch=1">
	<div class="title">Removing links</div><br />
	<table>
	<tr>
	<td align="right">Categories:</td>
	<td>'.$categories.'</td>
	</tr>
	<tr>
	<td></td>
	<td><input class="tbox" name="submit" type="submit" value="Switch" /></td>
	</table>
	</form>';

	print $footer;

	exit();

}

if(isset($_REQUEST['switch']))
{
	$links = render_links_menu("link", NULL, $_REQUEST['categories'], $_SESSION['identity']);
	
	print $header;
	
	print '<form name="form" method="post" action="links.php?delete=1">
	<div class="title">Removing links</div><br />
	<table>
	<tr>
	<td align="right">Links:</td>
	<td>'.$links.'</td>
	</tr>
	<tr>
	<td></td>
	<td><input class="tbox" name="submit" type="submit" value="Remove link" /></td>
	</table>
	</form>';		
	print $footer;
	exit();
}

if(isset($_REQUEST['delete'])){

	$username = $_SESSION['identity'];
	$id = $_REQUEST['link'];
	$query = "DELETE FROM $table_links WHERE id = '$id' and owner = '$username'";

	print $header;
	print "removal of link ".remove_db($query)."<br />";
	print $footer;
	exit();

}

print $header;

print '<div class="title">Link management</div><br />
<ul>
<li><a href="links.php?add=1">add a link</a></li>
<li><a href="links.php?remove=1">remove a link</a></li>
</ul>';

print $footer;

?>
