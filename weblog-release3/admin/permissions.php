<?php

/* 
	This file is distributed under the terms of the GPL License.
	
	This work is copyright Kevin G. Francis 2003
	
	Redistribution of this work must comply to the original
	release license restrictions.
	
*/

$admin_area = TRUE;
$dir_path ="../";
require($dir_path."config.php");
require($dir_path."functions/db.php");
require($dir_path."functions/authentication.php");
require($dir_path."functions/textparse.php");
require($dir_path."functions/render.php");

checkrank(10);


if(isset($_REQUEST['edit_blog']) )
{
	$q_blog = mysql_query("SELECT id, blog, owner, posters FROM $table_blog_owners WHERE id = '".$_REQUEST['id']."'");

	print $header;

	while($blogs = mysql_fetch_object($q_blog) )
	{
		$posters = array();
		$posters = explode("|",$blogs->posters);
		
		$post_text = NULL;
		
		foreach($posters as $key)
		{
			if(!empty($key) )
			{
				$post_text .= $key.', ';
			}
			else
			{
				$post_text .= '';
			}
		}
		
		$post_text = substr(trim($post_text),0, -1);
		
		$users = render_users_menu("owner",$blogs->owner);
		
		print '<div class="title">Editing blog permissions</div><br />
						<form name="perms" action="permissions.php?updating=1&amp;id='.$blogs->id.'" method="post">
						<table>
						<tr>
						<td align="right"><b>Blog Name:</b></td>
						<td><input type="text" class="tbox" name="blog_name" value="'.text_out($blogs->blog).'" /></td>
						</tr>
						<tr>
						<td align="right"><b>Owner:</b></td>
						<td>'.$users.'</td>
						</tr>
						<tr>
						<td align="right"><b>Posters:</b></td>
						<td><input type="text" class="tbox" name="posters" value="'.text_out($post_text).'" /></td>
						</tr>
						<tr>
						<td></td>
						<td><input type="submit" value="Update" class="tbox" /></td>
						</tr>
						</table></form>';
	}

	print mysql_error().$footer;	
	exit();
}

if(isset($_REQUEST['updating']) )
{

	print $header;
	
	$posters = str_replace(",","|",$_REQUEST['posters']);
	$posters = str_replace(" ","",$posters);
	
	$q_update = "UPDATE $table_blog_owners SET blog = '".text_in($_REQUEST['blog_name'])."', owner = '".$_REQUEST['owner']."', posters = '".$posters."' WHERE id = '".$_REQUEST['id']."' ";
	
	if(mysql_query($q_update) )
	{
		print '<i>>>> Permissions updated.</i>';
	}
	else
	{
		print '<i>>>> <b>Error:</b></i>'.mysql_error();
	}
	
	print $footer;
	exit();
}

if(isset($_REQUEST['edit']) )
{
	print $header;

	print '<div class="title">Manage blog permissions</div>
<table>
<tr>
	<th class="boxen">Blog</th><th class="boxen">Owner</th><th class="boxen">Posters</th><th class="boxen">Remove</th>
</tr>';

	$q_blog = mysql_query("SELECT id, blog, owner, posters FROM $table_blog_owners");

	while($blogs = mysql_fetch_object($q_blog) )
	{
		$posters = array();
		$posters = explode("|",$blogs->posters);
		
		$post_text = NULL;
		
		foreach($posters as $key)
		{
			if(!empty($key) )
			{
				$post_text .= $key.', ';
			}
			else
			{
				$post_text .= 'No other posters';
			}
		}
	
		print '<tr>
						<td class="boxen"><a href="permissions.php?edit_blog=1&amp;id='.$blogs->id.'">'.text_out($blogs->blog).'</a></td>
						<td class="boxen">'.text_out($blogs->owner).'</td>
						<td class="boxen">'.text_out($post_text).'</td>
						<td class="boxen"><a href="permissions.php?remove=1&amp;id='.$blogs->id.'">Remove</td>
					</tr>';
	}

	print '</table>';
	print $footer;
	exit();
}

if(isset($_REQUEST['add']) )
{

	$users = render_users_menu("owner", " ");

	print $header;
	print '<div class="title">Adding blog</div>
<form method="post" action="permissions.php?adding=1">
<table>
<tr>
	<td align="right"><b>Owner:</b></td>
	<td>'.$users.'</td>
</tr>
<tr>
	<td align="right"><b>Name:</b></td>
	<td><input type="text" name="blog_name" class="tbox" /></td>
</tr>
<tr>
	<td align="right" valign="top"><b>Posters:</b></td>
	<td><input type="text" class="tbox" name="posters" /><div class="small"><i style="color: red;">*</i>( Please enter values seperated by commas. Example: "kevin, robert, kester"</div></td>
</tr>
<tr>
	<td></td>
	<td><input type="submit" value="Add" class="tbox" /></td>
</tr>
</table>';
	
	print $footer;
	exit();
}

if(isset($_REQUEST['adding']) )
{
	if($_REQUEST['owner'] == " ")
	{
		print $header;
		print '<i>>>> The owner you chose is invalid. You most probably didn\'t pick one.</i>';
		print $footer;
	}
	else
	{
		$posters = str_replace(",","|",$_REQUEST['posters']);
		$posters = str_replace(" ","",$posters);
		$q_add = "INSERT INTO $table_blog_owners VALUES('', '".text_in($_REQUEST['blog_name'])."', '".$_REQUEST['owner']."', '$posters')";
		
		if(mysql_query($q_add) )
		{
			print $header;
			print '<i>>>> Blog created</i>';
			print $footer;
		}
		else
		{
			print $header;
			print '<i>>>> <b>Error:</b></i>'.mysql_error();
		}
	}		
	exit();
}

if(isset($_REQUEST['remove']) )
{

	$q_blog = mysql_query("SELECT blog FROM $table_blog_owners WHERE id = '".$_REQUEST['id']."'");
	
	while($blog = mysql_fetch_object($q_blog) )
	{
		print $header;
		print '<center>Are you sure you wish to remove the blog &quot;'.text_out($blog->blog).'&quot;?<br />
					<b><a href="permissions.php?removing=1&amp;id='.$_REQUEST['id'].'">Yes</a> / <a href="'.$_SERVER['HTTP_REFERER'].'">No</a></b></center>';
		print $footer;
	}
	exit();
}

if(isset($_REQUEST['removing']) )
{
	$q_remove_blog = "DELETE FROM $table_blog_owners WHERE id = '".$_REQUEST['id']."'";
	$q_remove_content = "DELETE FROM $table_blogs WHERE blog_name = '".text_in($_REQUEST['blog'])."'";

	print $header;
	
	if(mysql_query($q_remove_blog) )
	{
		print '<i>>>> Blog removed</i><br />';
	}
	else
	{
		print '<i>>>> <b>Error:</b></i><br />'.mysql_error();
	}
	
	if(mysql_query($q_remove_content) )
	{
		print '<i>>>> Blog content removed</i><br />';
	}
	else
	{
		print '<i>>>> <b>Error:</b></i><br />'.mysql_error();
	}
	
	print $footer;
	
	exit();
}

print $header;
print '<div class="title">Manage blog permissions</div>
<ul>
<li><a href="permissions.php?edit=1">Edit blog</a></li>
<li><a href="permissions.php?add=1">Add blog</a></li>
</ul>';

print $footer;

?>
