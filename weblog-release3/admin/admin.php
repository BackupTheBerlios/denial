<?php

/* 

This file is 

*/

$admin_area = TRUE;
$dir_path ="../";
$base_upload_path = $dir_path."uploads";
require($dir_path."config.php");
require($dir_path."functions/db.php");
require($dir_path."functions/authentication.php");
require($dir_path."functions/textparse.php");
require($dir_path."functions/render.php");

checkrank(30);

if(!isset($_REQUEST['start'])){
	$start = 0;
}else{
	$start = $_REQUEST['start'];
}

if(!isset($_REQUEST['limit']) )
{
	$limit = 20;
}
else
{
	$limit = $_REQUEST['limit'];
}

if(isset($_REQUEST['editing_blog']) )
{
	checkrank(20);
	if(isset($_REQUEST['id']) )
	{
		$id = $_REQUEST['id'];
		$q_editing = mysql_query("SELECT id, author, title, body, category, mood, listening, UNIX_TIMESTAMP(date), blog_name FROM $table_blogs WHERE id = '$id' LIMIT 1");

		while($editing = mysql_fetch_object($q_editing) )
		{
			$blogs = render_users_blogs($_SESSION['identity'], "blog_name", text_out($editing->blog_name) );
			$categories = render_categories_menu("blog_category", $editing->category, $editing->author);
			
			if($editing->open == 1)
			{
				$open_text = "checked";
			}
			else
			{
				$open_text = "";
			}
			
			print $header;
			print '<div class="title">Editing a blog post</div>
<form action="admin.php?updating_blog=1&id='.$editing->id.'" method="post" name="add_blog">
<table>
<tr>
	<td align="right"><b>Title:</b></td>
	<td><input type="text" name="blog_title" class="tbox" size="40" value="'.text_out($editing->title).'" /></td>
</tr>
<tr>
	<td align="right"><b>Blog:</b></td>
	<td>'.$blogs.'</td>
</tr>
<tr>
        <td align="right"><b>Category:</b></td>
        <td>'.$categories.'</td>
</tr>
<tr>
				<td align="right"><b>Comments</b></td>
				<td><input type="checkbox" name="open" class="tbox" '.$open_text.' /></td>
</tr>
<tr>
	<td align="right" valign="top"><b>Text:</b></td>
	<td><textarea name="blog_text" class="tbox" rows="10" cols="38">'.to_bbcode($editing->body).'</textarea></td>
</tr>
<tr>
        <td align="right"><b>Mood:</b></td>
        <td><input type="text" name="blog_mood" class="tbox" value="'.text_out($editing->mood).'" /></td>
</tr>
<tr>
        <td align="right"><b>Listening to:</b></td>
        <td><input type="text" name="blog_song" class="tbox" value="'.text_out($editing->listening).'" /></td>
</tr>
<tr>
	<td></td>
	<td><input type="submit" name="blog_submit" value="Post Blog" class="tbox" /></td>
</tr>
</table>
</form>';

		print $footer;
		}
	}
	else
	{
		print $header;
		print '<i>>>> An error has occured. Invalid entry id</i>';
		print $footer;
	}
exit();
}


if(isset($_REQUEST['edit_blogs']) )
{
	$username = $_SESSION['identity'];
		
	if(isset($_REQUEST['select']) )
	{
		$q_users = mysql_query("SELECT id, username FROM $table_admins");

		print $header;
		print '<table>'
			.'<div class="title">Users and their blogs</div>'
			.'<tr><th class="boxen">User</th><th class="boxen">Blogs owned/contributed to by this user</th></tr>';
		
		while($users = mysql_fetch_object($q_users) )
		{
			$postable = NULL;
			$q_blogs = mysql_query("SELECT id, blog FROM $table_blog_owners WHERE owner = '".$users->username."' OR posters LIKE \"%".$users->username."%\"");		
			while($blogs = mysql_fetch_object($q_blogs) )
			{
				$postable .= '<a href="admin.php?edit_blogs=1&amp;user='.$users->username.'&amp;blog_name='.text_out($blogs->blog).'">'.text_out($blogs->blog).'</a><br />';
			}	
	
			if(!empty($postable) )
			{
				print '<tr><td class="boxen" valign="top" align="right">'.$users->username.'</td><td class="boxen">';
				print $postable;
				print '</td></tr>';
			}
		}
		print '</table>';
		print $footer;
		exit();		
	}
	$extend = NULL;
	
	if(!isset($_REQUEST['blog_name']) )
	{
		$_REQUEST['blog_name'] = NULL;
	}
	
	if(!isset($_REQUEST['user']) )
	{
		$_REQUEST['user'] = NULL;
	}
	
	if(!isset($_REQUEST['category']) )
	{
		$_REQUEST['category'] = NULL;
	}
	
	if(isset($_REQUEST['blog_name']) && !empty($_REQUEST['blog_name']) )
	{
		$extend .= " AND blog_name = '".addslashes($_REQUEST['blog_name'])."' ";
	}

	if(isset($_REQUEST['user']) && !empty($_REQUEST['user']) )
	{
		$extend .= " AND author = '".addslashes($_REQUEST['user'])."' ";
	}

	if(isset($_REQUEST['category']) && !empty($_REQUEST['category']) )
	{
		$extend .= " AND category = '".addslashes($_REQUEST['category'])."' ";
	}

	$q_np = mysql_query("SELECT id, title, category, UNIX_TIMESTAMP(date) as date, blog_name, open FROM $table_blogs WHERE author = '$username' ".$extend."");
	$blog_rows = mysql_num_rows($q_np);
	
	$q_edit = mysql_query("SELECT id, author, title, category, UNIX_TIMESTAMP(date) as date, blog_name, open FROM $table_blogs WHERE id ".$extend." LIMIT $start, $limit");
	
	print $header;
	
	if($blog_rows < 1)
	{
		print '<i>>>> No blogs to edit</i>';
	}
	else
	{
		$previous = NULL;
		if($start < ($blog_rows-$limit))
		{

			if(isset($_REQUEST['user']) )
			{
				$previous .= '&amp;user='.stripslashes($_REQUEST['user']);
			}

			if(isset($_REQUEST['blog']) )
			{
				$previous .= '&amp;blog='.stripslashes($_REQUEST['blog']);
			}
			
			if(isset($_REQUEST['category']) )
			{
				$previous .= '&amp;category='.stripslashes($_REQUEST['category']);
			}
			
			$previous .= '&amp;start='.($start+$limit);
			$previous = '<a href="admin.php?edit_blogs=1'.$previous.'">Older &gt;&gt;</a>';
		}
		else
		{
			$previous = 'No older entries';
		}
		
		$next = NULL;
		if($start < ($blog_rows+$limit) && ($start+$limit) > $limit)
		{

			if(isset($_REQUEST['user']) )
			{
				$next .= '&amp;user='.stripslashes($_REQUEST['user']);
			}

			if(isset($_REQUEST['blog']) )
			{
				$next .= '&amp;blog='.stripslashes($_REQUEST['blog']);
			}
			
			if(isset($_REQUEST['category']) )
			{
				$next .= '&amp;category='.stripslashes($_REQUEST['category']);
			}
			$next .= '&amp;start='.($start-$limit);
			$next = '<a href="admin.php?edit_blogs=1'.$next.'">&lt;&lt; Newer</a>';
		}
		else
		{
			$next = 'No newer entries';
		}		
		
		print '<div class="title">Edit users\' blogs</div><br />
		<table>
		<tr>
		<td colspan="7" class="boxen"><center>'.$next.' | '.$previous.'</center></td>
		</tr>
		<tr>
			<th class="boxen">Title</th>
			<th class="boxen">Author</th>			
			<th class="boxen">Date</th>
			<th class="boxen"><a href="admin.php?edit_blogs=1&amp;blog_name='.stripslashes($_REQUEST['blog']).'&amp;user='.stripslashes($_REQUEST['user']).'">Category</a></th>
			<th class="boxen"><a href="admin.php?edit_blogs=1&amp;&amp;category='.stripslashes($_REQUEST['category']).'&amp;user='.stripslashes($_REQUEST['user']).'">Blog</a></th>
			<th class="boxen">Status</th>
			<th class="boxen">Remove</th>
		</tr>';
		
		while($edit = mysql_fetch_object($q_edit) )
		{
			if($edit->open == 0)
			{
				$status = "Closed";
			}
			else
			{
				$status = "Open";
			}
			
			print '<tr>
						 <td class="boxen"><a href="admin.php?editing_blog=1&amp;id='.$edit->id.'">'.text_out($edit->title).'</a></td>
						 <td class="boxen">'.text_out($edit->author).'</td>						 
						 <td class="boxen">'.date($admin_date,$edit->date).'
						 <td class="boxen"><a href="admin.php?edit_blogs=1&amp;category='.text_out($edit->category).'&amp;blog_name='.stripslashes($_REQUEST['blog']).'&amp;user='.stripslashes($_REQUEST['user']).'">'.text_out($edit->category).'</a></td>
						 <td class="boxen"><a href="admin.php?edit_blogs=1&amp;blog_name='.text_out($edit->blog_name).'&amp;category='.stripslashes($_REQUEST['category']).'&amp;user='.stripslashes($_REQUEST['user']).'">'.text_out($edit->blog_name).'</a></td>
						 <td class="boxen">'.$status.'</td>
						 <td class="boxen"><a href="admin.php?delete_blog=1&amp;id='.$edit->id.'">Remove</a></td>
						 </tr>';
		}
	
		print '</table>';
	}
	print $footer;	
	exit();
}


if(isset($_REQUEST['updating_blog']) )
{
	checkrank(20);
	if(!empty($_REQUEST['blog_text']) )
	{
		$blog_author	= $_SESSION['identity'];
		$blog_title	= text_in($_REQUEST['blog_title']);
		$blog_text	= to_html($_REQUEST['blog_text']);
		$blog_category	= text_in($_REQUEST['blog_category']);
		$blog_mood	= text_in($_REQUEST['blog_mood']);
		$blog_song	= text_in($_REQUEST['blog_song']);
		$blog_name	= text_in($_REQUEST['blog_name']);
		$id = $_REQUEST['id'];
		if(isset($_REQUEST['open']) )
		{
			$open = 1;
		}
		else
		{
			$open = 0;
		}		


		$insert 	= "UPDATE $table_blogs SET title = '$blog_title', body = '$blog_text', category = '$blog_category', mood = '$blog_mood', listening = '$blog_song', blog_name = '$blog_name', open = $open WHERE id = '$id'";

		print $header;
		if(mysql_query($insert) )
		{
			print '<i>>>> Post successfully stored</i><br />
<i>>>> 	Go back to the main admin content management area?</i><br />
<blockquote><i><a href="admin.php">Yes</a> / <a href="index.php">No, return to main</a></i></blockquote>';
		}
		else
		{
			print '<i>>>> Post <b>failed</b> with the following error: '.mysql_error().'</i><br />
<i>>>> 	Go back to the main textual content management area?</i><br />
<blockquote><i><a href="admin.php">Yes</a> / <a href="index.php">No, return to main</a></i></blockquote>';
		}
		print $footer;
		
	}
	else
	{
		print $header;
		print '<i>>>> You did not fill in the "Text" field (the main body of the post). You must enter text into said field.</i><br />
<i>>>> 	Go back to the main admin content management area?</i><br />
<blockquote><i><a href="admin.php">Yes</a> / <a href="index.php">No, return to main</a></i></blockquote>';
		print $footer;
	}
	exit();
}

if(isset($_REQUEST['delete_blog']) )
{
	checkrank(30);
	if(isset($_REQUEST['id']) )
	{
		$q_edit = mysql_query("SELECT title FROM $table_blogs WHERE id = '".$_REQUEST['id']."'");

	print $header;
		while($blog = mysql_fetch_object($q_edit) )
		{
			print '<center>Are you sure you wish to remove the entry entitled, &quot;'.text_out($blog->title).'&quot;?<br />
[ <a href="../index.php?id='.$_REQUEST['id'].'" target="_blank">View post</a> ] [ <a href="admin.php?deleting_blog=1&amp;id='.$_REQUEST['id'].'">Yes</a> / <a href="'.$_SERVER['HTTP_REFERER'].'">No</a> ]</center>';
		}
	}
	else
	{
		print '<i>>>> An error has occured. Invalid entry id</i>';
	}
	print $footer;
	exit();
}

if(isset($_REQUEST['deleting_blog']) )
{
	checkrank(30);
	if(isset($_REQUEST['id']) )
	{
		$id = $_REQUEST['id'];
		$q_delete = "DELETE FROM $table_blogs WHERE id = '$id' LIMIT 1";		
		
		if(mysql_query($q_delete) )
		{
			print $header;
			print '<i>>>> Post removed.<br />
<i>>>> 	Go back to the main admin content management area?</i><br />
<blockquote><i><a href="admin.php">Yes</a> / <a href="index.php">No, return to main</a></i></blockquote>';
			print $footer;
		}
		else
		{
			print $header;
			print '<i>>>> Post removed.<br />
<i>>>> 	Go back to the main admin content management area?</i><br />
<blockquote><i><a href="admin.php">Yes</a> / <a href="index.php">No, return to main</a></i></blockquote>';
			print $footer;
		}
	}
	exit();
}


/* start articles */

if(isset($_REQUEST['edit_articles']) )
{
	$username = $_SESSION['identity'];
	if(isset($_REQUEST['select']) )
	{
		$q_user = mysql_query("SELECT username FROM $table_admins");
		
		print $header;
		print '<div class="title">Users</div><br />
		You may <a href="admin.php?edit_articles=1">view all</a>, or<br /><br />';
		
		if(mysql_num_rows($q_user) > 0)
		{		
			print '<b>By author</b>
			<ul>';
			
			while($user = mysql_fetch_object($q_user) )
			{
				print '<li><a href="admin.php?edit_articles=1&amp;user='.text_out($user->username).'">'.text_out($user->username).'</a></li>';
			}
			
			print '</ul>';			
		}
		print $footer;
		exit();
	}

	$extend = NULL;
	
	if(!isset($_REQUEST['category']) )
	{
		$_REQUEST['category'] = NULL;
	}	
	
	if(isset($_REQUEST['category']) && !empty($_REQUEST['category']) )
	{
		$extend .= " AND category = '".addslashes($_REQUEST['category'])."' ";
	}

	$q_np = mysql_query("SELECT id FROM $table_articles WHERE author = '$username' ".$extend."");
	$art_rows = mysql_num_rows($q_np);
	
	$q_edit = mysql_query("SELECT id, title, category, UNIX_TIMESTAMP(date) as date, open FROM $table_articles WHERE author = '$username' ".$extend." LIMIT $start, $limit");
	
	print $header;
	
	if($art_rows < 1)
	{
		print '<i>>>> No blogs to edit</i>';
	}
	else
	{
		$previous = NULL;
		if($start < ($art_rows-$limit))
		{
			
			if(isset($_REQUEST['category']) )
			{
				$previous .= '&amp;category='.stripslashes($_REQUEST['category']);
			}
			$previous .= '&amp;start='.($start+$limit);
			$previous = '<a href="admin.php?edit_articles=1'.$previous.'">Older &gt;&gt;</a>';
		}
		else
		{
			$previous = 'No older entries';
		}
		
		$next = NULL;
		if($start < ($art_rows+$limit) && ($start+$limit) > $limit)
		{
			
			if(isset($_REQUEST['category']) )
			{
				$next .= '&amp;category='.stripslashes($_REQUEST['category']);
			}
			$next .= '&amp;start='.($start-$limit);
			$next = '<a href="admin.php?edit_articles=1'.$next.'">&lt;&lt; Newer</a>';
		}
		else
		{
			$next = 'No newer entries';
		}		
		
		print '<div class="title">Edit your articles</div><br />
		<table>
		<tr>
		<td colspan="5" class="boxen"><center>'.$next.' | '.$previous.'</center></td>
		</tr>
		<tr>
			<th class="boxen">Title</th>
			<th class="boxen">Date</th>
			<th class="boxen"><a href="admin.php?edit_articles=1">Category</a></th>
			<th class="boxen">Status</th>
			<th class="boxen">Remove</th>
		</tr>';
		
		while($edit = mysql_fetch_object($q_edit) )
		{
			if($edit->open == 0)
			{
				$status = "Closed";
			}
			else
			{
				$status = "Open";
			}
			
			print '<tr>
						 <td class="boxen"><a href="admin.php?editing_article=1&amp;id='.$edit->id.'">'.text_out($edit->title).'</a></td>
						 <td class="boxen">'.date($admin_date,$edit->date).'
						 <td class="boxen"><a href="admin.php?edit_articles=1&amp;category='.text_out($edit->category).'">'.text_out($edit->category).'</a></td>
						 <td class="boxen">'.$status.'</td>
						 <td class="boxen"><a href="admin.php?delete_articles=1&amp;id='.$edit->id.'">Remove</a></td>
						 </tr>';
		}
	
		print '</table>';
	}
	print $footer;	
	exit();
}

if(isset($_REQUEST['editing_article']) )
{
	checkrank(20);
	if(isset($_REQUEST['id']) )
	{
		$id = $_REQUEST['id'];
		$q_editing = mysql_query("SELECT id, author, title, body, category, UNIX_TIMESTAMP(date) as date, open FROM $table_articles WHERE id = '$id' LIMIT 1");

		while($editing = mysql_fetch_object($q_editing) )
		{
			if($editing->open == 1)
			{
				$open_text = "checked";
			}
			else
			{
				$open_text = "";
			}
		
			$categories = render_categories_menu("article_category",$editing->category,$editing->author);
			
			print $header;
			print '<div class="title">Editing an article post</div>
<form action="admin.php?updating_article=1&id='.$editing->id.'" method="post" name="add_article">
<table>
<tr>
	<td align="right"><b>Title:</b></td>
	<td><input type="text" name="article_title" class="tbox" size="40" value="'.text_out($editing->title).'" /></td>
</tr>
<tr>
        <td align="right"><b>Category:</b></td>
        <td>'.$categories.'</td>
</tr>
<tr>
				<td align="right"><b>Comments</b></td>
				<td><input type="checkbox" name="open" class="tbox" '.$open_text.' /></td>
</tr>
<tr>
	<td align="right" valign="top"><b>Text:</b></td>
	<td><textarea name="article_text" class="tbox" rows="10" cols="38">'.to_bbcode($editing->body).'</textarea></td>
</tr>
<tr>
	<td></td>
	<td><input type="submit" name="article_submit" value="Post article" class="tbox" /></td>
</tr>
</table>
</form>';
			
			print $footer;
		}
	}
	else
	{
		print $header;
		print '<i>>>> An error has occured. Invalid entry id</i>';
		print $footer;
	}
exit();
}

if(isset($_REQUEST['updating_article']) )
{
	checkrank(20);
	if(!empty($_REQUEST['article_text']) )
	{
		$article_author	= $_SESSION['identity'];
		$article_title	= text_in($_REQUEST['article_title']);
		$article_text	= to_html($_REQUEST['article_text']);
		$article_category	= text_in($_REQUEST['article_category']);
		$id = $_REQUEST['id'];
		if(isset($_REQUEST['open']) )
		{
			$open = 1;
		}
		else
		{
			$open = 0;
		}		

		

		$insert 	= "UPDATE $table_articles SET title = '$article_title', body = '$article_text', category = '$article_category', open = $open WHERE id = '$id'";

		print $header;
		if(mysql_query($insert) )
		{
			print "<i>>>> Article successfully stored</i>";
		}
		else
		{
			print "<i>>>> Article <b>failed</b> with the following error: ".mysql_error()."</i>";
		}
		print $footer;
		
	}
	else
	{
		print $header;
		print '<i>>>> You did not fill in the &quot;Text&quot; field (the main body of the post). You must enter text into said field.</i><br />
<i>>>> 	Go back to the main admin content management area?</i><br />
<blockquote><i><a href="admin.php">Yes</a> / <a href="index.php">No, return to main</a></i></blockquote>';
		print $footer;
	}
	exit();
}

if(isset($_REQUEST['delete_article']) )
{
	checkrank(20);
	if(isset($_REQUEST['id']) )
	{
		$q_edit = mysql_query("SELECT title FROM $table_articles WHERE id = '".$_REQUEST['id']."'");

	print $header;
		while($blog = mysql_fetch_object($q_edit) )
		{
			print '<center>Are you sure you wish to remove the entry entitled, &quot;'.$blog->title.'&quot;?<br />
[ <a href="../index.php?id='.$_REQUEST['id'].'" target="_blank">View post</a> ] [ <a href="admin.php?deleting_article=1&amp;id='.$_REQUEST['id'].'">Yes</a> / <a href="'.$_SERVER['HTTP_REFERER'].'">No</a> ]</center>';
		}
	}
	else
	{
		print '<i>>>> An error has occured. Invalid entry id</i>';
	}
	print $footer;
	exit();
}

if(isset($_REQUEST['deleting_article']) )
{
	if(isset($_REQUEST['id']) )
	{
		$id = $_REQUEST['id'];
		$q_delete = "DELETE FROM $table_articles WHERE id = '$id' LIMIT 1";		
		
		if(mysql_query($q_delete) )
		{
			print $header;
			print '<i>>>> Post removed.<br />
<i>>>> 	Go back to the main admin content management area?</i><br />
<blockquote><i><a href="admin.php">Yes</a> / <a href="index.php">No, return to main</a></i></blockquote>';
			print $footer;
		}
		else
		{
			print $header;
			print '<i>>>> Failed with error: '.mysql_error().'.<br />
<i>>>> 	Go back to the main admin content management area?</i><br />
<blockquote><i><a href="admin.php">Yes</a> / <a href="index.php">No, return to main</a></i></blockquote>';
			print $footer;
		}
	}
	exit();
}


/* start admin */

if(isset($_REQUEST['add_user']) )
{
	checkrank(10);
	print $header;

	print '<div class="title">Adding a user</div><br />
<form action="admin.php?adding_user=1" method="post" name="add_user">
<table>
<tr>
	<td align="right" valign="top">Real Name:</td>
	<td><input type="text" name="new_name" class="tbox" /><div class="small"><i style="color: red;">*</i>( Real name ie. Kevin Francis )</div></td>
</tr>
<tr>
	<td align="right" valign="top">Username:</td>
	<td><input type="text" name="new_username" class="tbox" /><div class="small"><i style="color: red;">*</i>( Do not use spaces, or any strange/exotic/special character )</div></td>
</tr>
<tr>
	<td align="right" valign="top">Password:</td>
	<td><input type="text" name="new_password" class="tbox" /><div class="small"><i style="color: red;">*</i>( Please make clear to him/her that this should be changed as soon as possible )</div></td>
</tr>
<tr>
	<td align="right" valign="top">Rank:</td>
	<td><select name="new_rank">
		<option value="10">Primary admin</option>
		<option value="20">Secondary admin</option>
		<option value="30">Restricted admin</option>
		<option value="40">Priviledged user</option>
		<option value="50">Normal user</option>
		<option value="60">Restricted user</option>
	</select><div class="small"><i style="color: red;">*</i>( See bottom of page for details )</div></td>
</tr>
<tr>
	<td></td>
	<td><input type="submit" name="submit" value="Create user" class="tbox" /></td>
</tr>
<tr>
	<td></td>
	<td><p><i style="color: red;">*</i>The ranking is as follows:<br />
		<ul>
		<li>Primary admin - Can do anything.</li>
		<li>Secondary admin - Access to everything, except user management</li>
		<li>Restricted admin - Can manage categories, and links</li>
		<li>Priviledged user - Can use blogs, articles, and uploads</li>
		<li>Standard user - Can use blogs, articles</li>
		<li>Restricted user - Can use blogs</li>
		</ul>
		</p>
		<p class="small">For most users, assigning them the rank of Priviledged user should suffice. Give out admin accounts only to those which are trusted.</p>
	</td>
</tr>
</table>
</form>';

	print $footer;
	exit();
}

if(isset($_REQUEST['adding_user']) )
{
	checkrank(10);
	$name = text_in($_REQUEST['new_name']);
	$username = text_in($_REQUEST['new_username']);
	$password = md5($_REQUEST['new_password']);
	$rank = $_REQUEST['new_rank'];

	$q_user = "INSERT INTO $table_admins VALUES('','$name','$username','$password','$rank','','','')";

	if(eregi(" ",$username) )
	{
		$errors = "Username had a space in it<br />";
		print $header;
		print $error;
		print $footer;
	}

	if(mysql_query($q_user) )
	{
		print $header;
		print '<i>>>> User was added.</i><br />
			<a href="admin.php">Return to user management</a> / <a href="index.php">Return to main</a>';
		print $footer;
	}
	else
	{
		print $header;
		print $errors."<i>>>> Error occured:</i> ".mysql_error();
		print $footer;
	}

	exit();
}

if(isset($_REQUEST['edit_user']) )
{
	checkrank(10);
	function rank($rank)
	{
		if($rank == 0)
		{
			$output = "Founder";
		}
		elseif($rank == 10)
		{
			$output = "Primary admin";
		}
		elseif($rank == 20)
		{
			$output = "Secondary admin";
		}
		elseif($rank == 30)
		{
			$output = "Restricted admin";
		}
		elseif($rank == 40)
		{
			$output = "Priviledged user";
		}
		elseif($rank == 50)
		{
			$output = "Standard user";
		}
		elseif($rank == 60)
		{
			$output = "Restricted user";
		}
		else
		{
			$output = "Invalid rank";
		}
		return $output;
	}	


	print $header;
	print '<div class="title">Edit or remove a user</div><br />
	Users currently registered:<br />
	<table>
	<tr><th class="boxen">Username</th><th class="boxen">Name</th><th class="boxen">Rank</th></tr>
';

	$q_users = mysql_query("SELECT id, real_name, username, rank FROM $table_admins");

	while($users = mysql_fetch_object($q_users) )
	{
		print '<tr><td class="boxen"><a href="admin.php?editing_user=1&id='.$users->id.'">'.$users->username.'</a></td><td class="boxen">( '.$users->real_name.' )</td><td class="boxen">( '.rank($users->rank).' )</td>';
		if(!$users->rank == 0)
		{
			print '<td class="boxen"><a href="admin.php?remove_user=1&id='.$users->id.'">Remove user</a></td></tr>';
		}
		else
		{
			print '<td></td></tr>';
		}
	}

	print '</table>';

	print $footer;
	exit();
}

if(isset($_REQUEST['editing_user']) )
{
	checkrank(10);
	if(isset($_REQUEST['id']) )
	{
	
		function rank($rank)
		{
			if($rank == 0)
			{
				$output = "Founder";
			}
			elseif($rank == 10)
			{
				$output = "Primary admin";
			}
			elseif($rank == 20)
			{
				$output = "Secondary admin";
			}
			elseif($rank == 30)
			{
				$output = "Restricted admin";
			}
			elseif($rank == 40)
			{
				$output = "Priviledged user";
			}
			elseif($rank == 50)
			{
				$output = "Standard user";
			}
			elseif($rank == 60)
			{
				$output = "Restricted user";
			}
			else
			{
				$output = "Invalid rank";
			}
			return $output;
		}	

		$id = $_REQUEST['id'];
		$q_user = mysql_query("SELECT id, real_name, username, rank, email, info, timezone FROM $table_admins WHERE id = '$id'");

		print $header;
		
		while($edit = mysql_fetch_object($q_user) )
		{
		
		if($edit->rank == 0)
		{
			$rank = "";
		}
		else
		{
			$rank = '			<tr>
				<td align="right" valign="top">Rank:</td>
				<td><select name="edit_rank">
					<option value="'.$edit->rank.'">'.rank($edit->rank).'</option>
					<option></option>
				<option value="10">Primary admin</option>
				<option value="20">Secondary admin</option>
				<option value="30">Restricted admin</option>
				<option value="40">Priviledged user</option>
				<option value="50">Normal user</option>
				<option value="60">Restricted user</option>
				</select><div class="small"><i style="color: red;">*</i>( See bottom of page for details )</div></td>
			</tr>';
		}		
		
			print '<div class="title">Editing a user</div><br />
			<form action="admin.php?updating_user=1&amp;id='.$edit->id.'" method="post" name="edit_user">
			<table>
			<tr>
				<td valign="top" align="right">Name:</td>
				<td><input type="text" name="edit_name" class="tbox" value="'.text_out($edit->real_name).'" /></td>
			</tr>
			<tr>
				<td valign="top" align="right">Password:</td>
				<td><input type="password" name="edit_password" class="tbox" /></td>
			</tr>
			<tr>
				<td valign="top" align="right">Confirm:</td>
				<td><input type="password" name="edit_password_confirm" class="tbox" />
				<div class="small"><i style="color: red;">*</i>( Type your password into both fields to change. Otherwise, leave it empty)</td>
			</tr>'.$rank.'
			<tr>
				<td valign="top" align="right">Timezone:</td>
				<td><input type="text" name="edit_timezone" class="tbox" value="'.$edit->timezone.'" />
				<div class="small"><i style="color: red;">*</i>( Please enter timezones in this format: +|- # ie. +800 )</div></td>
			</tr>
			<tr>
				<td valign="top" align="right">Email:</td>
				<td><input type="text" name="edit_email" class="tbox" value="'.$edit->email.'" /></td>
			</tr>
			<tr>
				<td valign="top" align="right">Biography:</td>
				<td><textarea name="edit_info" class="tbox" cols="60" rows="10">'.text_out($edit->info).'</textarea>
				<div class="small"><i style="color: red;">*</i>( You may use BBCode in this field )</div></td>
			</tr>
			<tr>
				<td></td>
				<td><input type="submit" name="submit" value="Apply changes" class="tbox" /></td>
			</table>
			</form>';
		}

		print $footer;
		
	}
	else
	{
		print $header;
		print '<i>>>> Error - Invalid ID</i>';
		print $footer;
	}
	exit();	
}

if(isset($_REQUEST['updating_user']) )
{
	checkrank(10);
	print $header;
	
	if(!empty($_REQUEST['edit_password']) || !empty($_REQUEST['edit_password_confirm']) )
	{
		if($_REQUEST['edit_password'] == $_REQUEST['edit_password_confirm'])
		{
			$password = $_REQUEST['edit_password'];
			$id = $_REQUEST['id'];
			$q_update = "UPDATE $table_admins SET password = '".md5($password)."' WHERE id = '$id'";
			
			if(mysql_query($q_update) )
			{
				print '<i>>>> Password updated.</i><br />';
			}
			else
			{
				print '<i>>>> An error has occured: '.mysql_error().'<br />
				Password was not updated. Please go back and try again.</i><br />';
			}
		}
		else
		{
			print '<i>>>> Passwords did not match. Please go back and try again.</i><br />';
		}
	}
	
	$id = $_REQUEST['id'];
	$q_update2 = "UPDATE $table_admins SET rank = '".$_REQUEST['edit_rank']."',
						timezone = '".text_in($_REQUEST['edit_timezone'])."',
						email = '".text_in($_REQUEST['edit_email'])."',
						info = '".to_html($_REQUEST['edit_info'])."' WHERE id = '$id'";
						
	if(mysql_query($q_update2) )
	{
		print '<i>>>> Profile updated.</i>';
	}
	else
	{
		print '<i>>>> An error has occured: '.mysql_error().'<br />
			Please go back and try again.</i>';
	}
	
	print $footer;
	exit();

}

if(isset($_REQUEST['remove_user']) )
{
	checkrank(10);
	if(isset($_REQUEST['id']) )
	{
		$id =  $_REQUEST['id'];
	
		$q_user = mysql_query("SELECT id, real_name, username, rank FROM $table_admins WHERE id = $id");
	
		while($user = mysql_fetch_object($q_user) )
		{
			print $header;
			print '<center>Are you sure you wish to remove user '.text_out($user->username).' ('.text_out($user->real_name).')?<br />
				<b>This action cannot be undone</b>. All the user\'s content will be removed as well. This includes blogs, article and uploads as well.<br />
				[ <a href="admin.php?removing_user=1&amp;id='.$user->id.'">Yes</a> / <a href="'.$_SERVER['HTTP_REFERER'].'">No</a> ]</center>';
			print $footer;
		}
	}
	else
	{
		print $header;
		print '<i>>>> Invalid user ID</i>';
		print $footer;
	}
	exit();
}

if(isset($_REQUEST['removing_user']) )
{
	$id = $_REQUEST['id'];
	$q_info = mysql_query("SELECT id, username FROM $table_admins WHERE id = '$id'");
	
	print $header;
	
	while($user = mysql_fetch_object($q_info) )
	{
		$author = $user->username;
		$q_user = "DELETE FROM $table_admins WHERE id = '$id'";
		$q_blogs = "DELETE FROM $table_blogs WHERE author = '$author'";
		$q_articles = "DELETE FROM $table_articles WHERE author = '$author'";
		
		if(!mysql_query($q_user))
		{
			print '<b>Error</b>: '.mysql_error().'<br />';
		}
		if(!mysql_query($q_blogs))
		{
			print '<b>Error</b>: '.mysql_error().'<br />';
		}
		if(!mysql_query($q_articles))
		{
			print '<b>Error</b>: '.mysql_error().'<br />';
		}		
		
		$q_files = mysql_query("SELECT id, owner, filename, filename2 FROM $table_uploads WHERE owner = '".$user->username."'");
		while($delete = mysql_fetch_object($q_files) )
		{
			if(file_exists($base_upload_path.'/'.$delete->owner.'/'.$delete->filename) )
			{
				if(!unlink($base_upload_path.'/'.$delete->owner.'/'.$delete->filename) )
				{
					print '<b>Error</b> ( file was not deleted:'.$base_upload_path.'/'.$delete->owner.'/'.$delete->filename.' )<br />';
				}
				
				if(file_exists($base_upload_path.'/'.$delete->owner.'/thumbnails/'.$delete->filename) )
				{
					if(!unlink($base_upload_path.'/'.$delete->owner.'/thumbnails/'.$delete->filename) )
					{
						print '<b>Error</b> ( file was not deleted:'.$base_upload_path.'/'.$delete->owner.'/thumbnails/'.$delete->filename.' )<br />';
					}
				}
			}
			else
			{
				print '<b>Error</b> ( file did not exist: '.$base_upload_path.'/'.$delete->owner.'/'.$delete->filename.' )<br />';
			}
		}
	}
	
	print $footer;
	exit();
}

/* uploads management :E aka "Teh Pain" */

if(isset($_REQUEST['edit_uploads']) )
{
	if(!isset($_REQUEST['user']) )
	{
		print $header;
		
		print '<div class="title">Manage users\' uploads</div>
		<ul>';
	
		$q_users = mysql_query("SELECT id, username FROM $table_admins");
		while($users = mysql_fetch_object($q_users) )
		{
			print '<li><a href="admin.php?edit_uploads=1&amp;user='.$users->username.'">'.$users->username.'</a></li>';
		}
		
		print '</ul>';
		
		print $footer;
		exit();
	}

	$extend = NULL;
	
	if(!isset($_REQUEST['category']) )
	{
		$_REQUEST['category'] = NULL;
	}

	if(!isset($_REQUEST['user']) )
	{
		$_REQUEST['user'] = NULL;
	}
	
	if(isset($_REQUEST['category']) && !empty($_REQUEST['category']) )
	{
		$extend .= " AND category = '".addslashes($_REQUEST['category'])."' ";
	}
	
	if(isset($_REQUEST['user']) && !empty($_REQUEST['user']) )
	{
		$extend .= " AND owner = '".addslashes($_REQUEST['user'])."' ";
	}	

	$q_np = mysql_query("SELECT id FROM $table_uploads WHERE id ".$extend."");
	$up_rows = mysql_num_rows($q_np);
	
	$q_edit = mysql_query("SELECT id, owner, filename, filename2, UNIX_TIMESTAMP(date) as date, category, public, counter FROM $table_uploads WHERE id ".$extend." LIMIT $start, $limit");
	
	print $header;
	
	if($up_rows < 1)
	{
		print '<i>>>> No uploads have been made for this user</i>';
	}
	else
	{
		$previous = NULL;
		if($start < ($up_rows-$limit))
		{
			
			if(isset($_REQUEST['category']) )
			{
				$previous .= '&amp;category='.stripslashes($_REQUEST['category']);
			}
			$previous .= '&amp;start='.($start+$limit);
			$previous = '<a href="admin.php?edit_uploads=1'.$previous.'&amp;user='.text_out($_REQUEST['user']).'">Older &gt;&gt;</a>';
		}
		else
		{
			$previous = 'No older entries';
		}
		
		$next = NULL;
		if($start < ($up_rows+$limit) && ($start+$limit) > $limit)
		{
			
			if(isset($_REQUEST['category']) )
			{
				$next .= '&amp;category='.stripslashes($_REQUEST['category']);
			}
			$next .= '&amp;start='.($start-$limit);
			$next = '<a href="admin.php?edit_uploads=1'.$next.'&amp;user='.text_out($_REQUEST['user']).'">&lt;&lt; Newer</a>';
		}
		else
		{
			$next = 'No newer entries';
		}		
		
		print '<div class="title">Browsing uploads</div><br />
		<table>
		<tr>
		<td colspan="7" class="boxen"><center>'.$next.' | '.$previous.'</center></td>
		</tr>
		<tr>
			<th class="boxen">File</th>
			<th class="boxen">Date</th>
			<th class="boxen">Owner</th>
			<th class="boxen"><a href="admin.php?edit_uploads=1&amp;user='.text_out($_REQUEST['user']).'">Category</a></th>
			<th class="boxen">Status</th>
			<th class="boxen">Downloads</th>
		</tr>';
		
		while($edit = mysql_fetch_object($q_edit) )
		{
			if($edit->open == 0)
			{
				$status = "Private";
			}
			else
			{
				$status = "Public";
			}
			
			print '<tr>
						 <td class="boxen"><a href="admin.php?viewfile=1&amp;id='.$edit->id.'">'.text_out($edit->filename2).'</a></td>
						 <td class="boxen">'.date($admin_date,$edit->date).'
						 <td class="boxen">'.text_out($edit->owner).'
						 <td class="boxen"><a href="admin.php?edit_uploads=1&amp;category='.text_out($edit->category).'&amp;user='.text_out($_REQUEST['user']).'">'.text_out($edit->category).'</a></td>
						 <td class="boxen">'.$status.'</td>
						 <td class="boxen">'.$edit->counter.'</td>
						 </tr>';
		}
	
		print '</table>';
	}
	print mysql_error().$footer;	
	exit();			
}

if(isset($_REQUEST['viewfile']) )
{
	if(isset($_REQUEST['id']) )
	{
		$username = $_SESSION['identity'];
		$id = $_REQUEST['id'];
		$q_file = mysql_query("SELECT id, filename, filename2, description, UNIX_TIMESTAMP(date) as date, owner, public, category, counter FROM $table_uploads WHERE id = '$id'");
		
			while($file = mysql_fetch_object($q_file) )
			{
				$upload_path = $base_upload_path."/".$file->owner."/";
				$thumbnail_path = $base_upload_path."/".$file->owner."/thumbnails/";
			
				$handle = popen("/usr/bin/file -b '".addslashes($upload_path.$file->filename2)."'", "r");
				$filetype = fread($handle, 100);
				pclose($handle);			
			
				print $header;
				print '<div class="title">Viewing a file</div>
					<table class="boxen">
					<tr>
					<td align="right"><b>Options:</b></td>
					<td><a href="'.$upload_path.$file->filename2.'">Download</a> / <a href="uploads.php?edit=1&amp;id='.$file->id.'">Edit details</a> / <a href="uploads.php?remove=1&amp;id='.$file->id.'">Remove file</a></td>
					</tr>
					<tr>
					<td align="right"><b>Name:</b></td>
					<td>'.text_out($file->filename2).'</td>
					</tr>
					<tr>
					<tr>
					<td align="right"><b>Category:</b></td>
					<td>'.text_out($file->category).'</td>
					</tr>
					<tr>					
					<td align="right"><b>Size:</b></td>
					<td>'.round(filesize($upload_path.$file->filename2)/1024, 1).' KB</td>
					</tr>
					<tr>
					<td align="right"><b>Date:</b></td>
					<td>'.date($date_format_three,filectime($upload_path.$file->filename2)).'</td>
					</tr>
					<tr>
					<td align="right" valign="top"><b>Type:</b></td>
					<td>'.text_out($filetype).'<div class="small"><i style="color:red;">*</i>( These readings might not be accurate )</div></td>
					</tr>';
					
					if(strstr($filetype, "image") )
					{
						print '
							<tr>
							<td align="right" valign="top"><b>Description:</b></td>
							<td>'.text_out($file->description).'</td>
							</tr>						
							<tr>
							<td align="right" valign="top"><b>Preview:</b></td>
							<td>Click to view full sized image<div class="small"><i style="color:red;">*</i>( There might not be an preview here if your browser doesn\'t support the filetype )</div><br /><br /><a href="viewer.php?filename='.$file->owner."/".text_out($file->filename).'"><img src="'.$thumbnail_path.$file->filename.'" alt="" /></a></td>
							</tr>
					</table>';
					}
					else
					{
						print '
					<tr>
					<td align="right" valign="top"><b>Description:</b></td>
					<td>'.text_out($file->description).'</td>
					</tr>
					</table>';
					}
					
				print $footer;
			}
	}
	else
	{
		print $header;
		print '<i>>>> Invalid ID</i>';
		print $footer;
	}
	exit();
}

print $header;
print '<div class="title">Administration of users, and their content.</div><br />
Manage content:
<ul>
<li><a href="admin.php?edit_blogs=1&amp;select=1">Manage blogs</a></li>
<li><a href="admin.php?edit_articles=1&amp;select=1">Manage articles</a></li>
<li><a href="admin.php?edit_uploads=1">Manage uploads</a></li>
</ul>
Manage users:
<ul>
<li><a href="admin.php?add_user=1">Add a new user</a></li>
<li><a href="admin.php?edit_user=1">Edit an existing user</a></li>
</ul>';


print $footer;

?>

