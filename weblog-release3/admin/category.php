<?php

$admin_area = TRUE;
$dir_path ="../";
require($dir_path."config.php");
require($dir_path."functions/db.php");
require($dir_path."functions/authentication.php");
require($dir_path."functions/textparse.php");
require($dir_path."functions/render.php");
require($dir_path."functions/admin.php");

if (!isset ($_REQUEST['type']) )
{
	$_REQUEST['type'] = "";
}

if ($_REQUEST['type'] == "category")
{
	if (isset ($_REQUEST['adding']) )
	{
		$category	= text_in ($_REQUEST['category']);
		$query		= mysql_query ("SELECT category FROM $table_category WHERE category = '$category' AND owner = '".$_SESSION['identity']."'");
		
		if (mysql_num_rows ($query) > 0)
		{
			print $header.'<i>>>> That category already exists. Please try something else.</i>'.$footer;
			exit ();
		}

		$query	= "INSERT INTO $table_category VALUES('','$category','".$_SESSION['identity']."')";
		
		if(mysql_query($query) )
		{
			print $header.'<i>>>> Category was successfully created.'.$footer;
		}
		else
		{
			print $header.'<i>>> An error occured: '.mysql_error().'</i>'.$footer;
		}
	}

	if (isset ($_REQUEST['add']) )
	{
		print $header.'<form name="category" method="post" action="category.php?type=category&amp;adding=1">
		<div class="title">Creating a category</div><br />
		<table>
			<tr>
				<td><b>Category:</b></td>
				<td><input class="tbox" type="text" name="category"></td>
			</tr>
			<tr>
				<td></td>
				<td><input class="tbox" type="submit" name="Submit" value="Create"></td>
			</tr>
		</table>
		</form>'.$footer;	
	}

	if (isset ($_REQUEST['remove']) )
	{
		$q_cat = mysql_query("SELECT id FROM $table_category WHERE owner = '".$_SESSION['identity']."'");
		if(mysql_num_rows($q_cat) < 1)
		{
			print $header;
			print '<i>>>> No categories to remove</i>';
			print $footer;
			exit();
		}
	
		$categories = render_categories_menu("category",NULL,$_SESSION['identity']);
	
		print $header.'<form name="form" method="post" action="category.php?type=category&amp;delete=1">
		<div class="title">Removing a category</div><br />
		'.$categories.'
		<br />
		<input type="checkbox" name="content" /> Remove content in this category as well?<br />
		<div class="small"><i style="color: red;">*</i>( This option will permanently delete all blogs, articles, and other content in this category.)</div>
		<br />
		<input class="tbox" name="submit" type="submit" value="Remove" /></form>'.$footer;
	}

	if(isset ($_REQUEST['delete']) )
	{
		$category = $_REQUEST['category'];
		$q_category = mysql_query("SELECT id, category FROM $table_category WHERE category = '$category' AND owner = '".$_SESSION['identity']."'");
		
		while($cat = mysql_fetch_object($q_category) )
		{
			$category = $cat->category;
			
			if(!empty($_REQUEST['content']) )
			{
				print $header.'<div align="center"><b>Notice:</b> You have chosen to remove all content associated with this category.<br />
				Are you sure you wish to remove the "'.text_out($category).'" category?<br />
				<b><a href="category.php?type=category&amp;deleting=1&amp;category='.text_out($_REQUEST['category']).'&amp;content=1">yes</a> / <a href="'.$_SERVER['HTTP_REFERER'].'">no</a></b></div><br />'.$footer;
			}
			else
			{	
				print $header.'<div align="center">Are you sure you wish to remove the "'.text_out($category).'" category?<br />
				<b><a href="category.php?type=category&amp;deleting=1&amp;category='.text_out($_REQUEST['category']).'">yes</a> / <a href="'.$_SERVER['HTTP_REFERER'].'">no</a></b></div><br />'.$footer;
			}

		}
		
		if(mysql_num_rows($q_category) == 0)
		{
			print $header.'<i>>>> No such category</i><br />'.$footer;
		}
	}

	if(isset ($_REQUEST['deleting']) )
	{
		$username = $_SESSION['identity'];
		$category	= $_REQUEST['category'];
		$q_category	= "DELETE FROM $table_category WHERE owner = '$username' AND category = '$category'";

		print $header;

		if(mysql_query($q_category) )
		{
			print '<i>>>> Category removed</i><br />';
		}
		else
		{
			print '<i>>>> Category wasn\'t removed. An error occured: '.mysql_error().'</i><br />';
		}


		if(isset($_REQUEST['content']) )
		{
			print remove_user_content($_SESSION['identity'],text_in($category));
		}
		
		print $footer;
		
	}
	exit ();
}

print $header;

print '<div class="title">Editing your categories</div>
<ul>
<li><a href="category.php?type=category&amp;add=1">Add a category</a></li>
<li><a href="category.php?type=category&amp;remove=1">Remove a category</a></li>
</ul>';

print $footer;

?>
