<?php

/* code not complete yet */

$admin_area = TRUE;
$dir_path ="../";
require($dir_path."config.php");
require($dir_path."functions/db.php");
require($dir_path."functions/authentication.php");
require($dir_path."functions/textparse.php");
require($dir_path."functions/render.php");

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

/* code from here takes the output from forms/menus and does stuff */

if(isset($_REQUEST['adding_blog']) )
{

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

		if(isset($_REQUEST['id']) )
		{
			$insert 	= "UPDATE $table_blogs SET title = '$blog_title', body = '$blog_text', category = '$blog_category', mood = '$blog_mood', listening = '$blog_song', blog_name = '$blog_name', open = $open WHERE id = '$id'";
		}
		else
		{
			$insert 	= "INSERT INTO $table_blogs VALUES('','$blog_author','$blog_title','$blog_text','$blog_category','$blog_mood','$blog_song',NOW(),'$blog_name',$open)";
		}

		print $header;
		if(mysql_query($insert) )
		{
			print '<i>>>> Post successfully stored</i><br />
<i>>>> 	Go back to the main textual content management area?</i><br />
<blockquote><i><a href="text.php">Yes</a> / <a href="index.php">No, return to main</a></i></blockquote>';
		}
		else
		{
			print '<i>>>> Post <b>failed</b> with the following error: '.mysql_error().'</i><br />
<i>>>> 	Go back to the main textual content management area?</i><br />
<blockquote><i><a href="text.php">Yes</a> / <a href="index.php">No, return to main</a></i></blockquote>';
		}
		print $footer;
		
	}
	else
	{
		print $header;
		print '<i>>>> You did not fill in the "Text" field (the main body of the post). You must enter text into said field.</i><br />
<i>>>> 	Go back to the main textual content management area?</i><br />
<blockquote><i><a href="text.php">Yes</a> / <a href="index.php">No, return to main</a></i></blockquote>';
		print $footer;
	}
	exit();
}


if(isset($_REQUEST['adding_article']) )
{

	if(!empty($_REQUEST['article_text']) )
	{
		$article_author	= $_SESSION['identity'];
		$article_title	= text_in($_REQUEST['article_title']);
		$article_text	= to_html($_REQUEST['article_text']);
		$article_category	= text_in($_REQUEST['article_category']);
		
		if(isset($_REQUEST['open']) )
		{
			$open = 1;
		}
		else
		{
			$open = 0;
		}

		if(isset($_REQUEST['id']) )
		{
			$insert 	= "UPDATE $table_articles SET title = '$article_title', body = '$article_text', category = '$article_category', open = $open WHERE id = '$id'";
		}
		else
		{
			$insert 	= "INSERT INTO $table_articles VALUES('','$article_author','$article_title','$article_text','$article_category',NOW(),$open)";
		}


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
<i>>>> 	Go back to the main textual content management area?</i><br />
<blockquote><i><a href="text.php">Yes</a> / <a href="index.php">No, return to main</a></i></blockquote>';
		print $footer;
	}
	exit();
}

/* code from here on generates the forms/menu */

if(isset($_REQUEST['add']) && $_REQUEST['type'] == "blogs")
{
	$categories = render_categories_menu("blog_category",NULL,$_SESSION['identity']);
	$blogs = render_users_blogs($_SESSION['identity'],"blog_name",NULL);
	
	print $header;
	print '<div class="title">Adding a blog post</div>
<form action="text.php?adding_blog=1" method="post" name="add_blog">
<table>
<tr>
	<td align="right"><b>Title:</b></td>
	<td><input type="text" name="blog_title" class="tbox" size="70" /></td>
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
				<td><input type="checkbox" name="open" class="tbox" checked /></td>
</tr>
<tr>
	<td align="right" valign="top"><b>Text:</b></td>
	<td><textarea name="blog_text" class="tbox" rows="20" cols="68"></textarea></td>
</tr>
<tr>
        <td align="right"><b>Mood:</b></td>
        <td><input type="text" name="blog_mood" class="tbox" /></td>
</tr>
<tr>
        <td align="right"><b>Listening to:</b></td>
        <td><input type="text" name="blog_song" class="tbox" /></td>
</tr>
<tr>
	<td></td>
	<td><input type="submit" name="blog_submit" value="Post Blog" class="tbox" /></td>
</tr>
</table>
</form>';

	print $footer;
	

	exit();
}

if(isset($_REQUEST['edit_blogs']) )
{
	$username = $_SESSION['identity'];
	if(isset($_REQUEST['select']) )
	{
		
		$q_cat = mysql_query("SELECT DISTINCT category FROM $table_blogs WHERE author = '$username'");
		$q_blog = mysql_query("SELECT DISTINCT blog_name FROM $table_blogs WHERE author = '$username'");
		print $header;
		print '<div class="title">Pick a viewing method</div><br />
		You may <a href="text.php?edit_blogs=1">view all</a>, or<br /><br />';
		
		if(mysql_num_rows($q_blog) > 0)
		{		
			print '<b>By blog</b>
			<ul>';
			
			while($blog = mysql_fetch_object($q_blog) )
			{
				print '<li><a href="text.php?edit_blogs=1&amp;blog='.text_out($blog->blog_name).'">'.text_out($blog->blog_name).'</a></li>';
			}
			
			print '</ul>';			
		}
		
		if(mysql_num_rows($q_cat) > 0)
		{		
			print '<b>By category</b>
			<ul>';
			
			while($cat = mysql_fetch_object($q_cat) )
			{
				print '<li><a href="text.php?edit_blogs=1&amp;category='.text_out($cat->category).'">'.text_out($cat->category).'</a></li>';
			}
			
			print '</ul>';			
		}
		print $footer;
		exit();
	}

	$extend = NULL;
	
	if(!isset($_REQUEST['blog']) )
	{
		$_REQUEST['blog'] = NULL;
	}
	
	if(!isset($_REQUEST['category']) )
	{
		$_REQUEST['category'] = NULL;
	}	
	
	if(isset($_REQUEST['blog']) && !empty($_REQUEST['blog']) )
	{
		$extend .= " AND blog_name = '".addslashes($_REQUEST['blog'])."' ";
	}

	if(isset($_REQUEST['category']) && !empty($_REQUEST['category']) )
	{
		$extend .= " AND category = '".addslashes($_REQUEST['category'])."' ";
	}

	$q_np = mysql_query("SELECT id, title, category, UNIX_TIMESTAMP(date) as date, blog_name, open FROM $table_blogs WHERE author = '$username' ".$extend."");
	$blog_rows = mysql_num_rows($q_np);
	
	$q_edit = mysql_query("SELECT id, title, category, UNIX_TIMESTAMP(date) as date, blog_name, open FROM $table_blogs WHERE author = '$username' ".$extend." LIMIT $start, $limit");
	
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
			if(isset($_REQUEST['blog']) )
			{
				$previous .= '&amp;blog='.stripslashes($_REQUEST['blog']);
			}
			
			if(isset($_REQUEST['category']) )
			{
				$previous .= '&amp;category='.stripslashes($_REQUEST['category']);
			}
			$previous .= '&amp;start='.($start+$limit);
			$previous = '<a href="text.php?edit_blogs=1'.$previous.'">Older &gt;&gt;</a>';
		}
		else
		{
			$previous = 'No older entries';
		}
		
		$next = NULL;
		if($start < ($blog_rows+$limit) && ($start+$limit) > $limit)
		{
			if(isset($_REQUEST['blog']) )
			{
				$next .= '&amp;blog='.stripslashes($_REQUEST['blog']);
			}
			
			if(isset($_REQUEST['category']) )
			{
				$next .= '&amp;category='.stripslashes($_REQUEST['category']);
			}
			$next .= '&amp;start='.($start-$limit);
			$next = '<a href="text.php?edit_blogs=1'.$next.'">&lt;&lt; Newer</a>';
		}
		else
		{
			$next = 'No newer entries';
		}		
		
		print '<div class="title">Edit your blogs</div><br />
		<table>
		<tr>
		<td colspan="7" class="boxen"><center>'.$next.' | '.$previous.'</center></td>
		</tr>
		<tr>
			<th class="boxen">Title</th>
			<th class="boxen">Date</th>
			<th class="boxen"><a href="text.php?edit_blogs=1&amp;blog='.stripslashes($_REQUEST['blog']).'">Category</a></th>
			<th class="boxen"><a href="text.php?edit_blogs=1&amp;category='.stripslashes($_REQUEST['category']).'">Blog</a></th>
			<th class="boxen">Status</th>
			<th class="boxen">Edit</th>
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
						 <td class="boxen">'.text_out($edit->title).'</td>
						 <td class="boxen">'.date($admin_date,$edit->date).'
						 <td class="boxen"><a href="text.php?edit_blogs=1&amp;category='.text_out($edit->category).'&amp;blog='.stripslashes($_REQUEST['blog']).'">'.text_out($edit->category).'</a></td>
						 <td class="boxen"><a href="text.php?edit_blogs=1&amp;blog='.text_out($edit->blog_name).'&amp;category='.stripslashes($_REQUEST['category']).'">'.text_out($edit->blog_name).'</a></td>
						 <td class="boxen">'.$status.'</td>
						 <td class="boxen"><a href="text.php?editing_blog=1&amp;id='.$edit->id.'">Edit</a></td>
						 <td class="boxen"><a href="text.php?delete_blog=1&amp;id='.$edit->id.'">Remove</a></td>
						 </tr>';
		}
	
		print '</table>';
	}
	print $footer;	
	exit();
}

if(isset($_REQUEST['editing_blog']) )
{
	if(isset($_REQUEST['id']) )
	{
		$id = $_REQUEST['id'];
		$q_editing = mysql_query("SELECT id, author, title, body, category, mood, listening, UNIX_TIMESTAMP(date), blog_name, open FROM $table_blogs WHERE id = '$id' LIMIT 1");

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
			$blogs = render_users_blogs($_SESSION['identity'],  "blog_name", text_out($editing->blog_name) );
			$categories = render_categories_menu("blog_category",$editing->category,$_SESSION['identity']);
			
			print $header;
			print '<div class="title">Adding a blog post</div>
<form action="text.php?adding_blog=1&id='.$editing->id.'" method="post" name="add_blog">
<table>
<tr>
	<td align="right"><b>Title:</b></td>
	<td><input type="text" name="blog_title" class="tbox" size="70" value="'.text_out($editing->title).'" /></td>
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
	<td><textarea name="blog_text" class="tbox" rows="20" cols="68">'.to_raw($editing->body).'</textarea></td>
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

if(isset($_REQUEST['delete_blog']) )
{
	if(isset($_REQUEST['id']) )
	{
		$q_edit = mysql_query("SELECT title FROM $table_blogs WHERE id = '".$_REQUEST['id']."'");

	print $header;
		while($blog = mysql_fetch_object($q_edit) )
		{
			print '<center>Are you sure you wish to remove the entry entitled, &quot;'.text_out($blog->title).'&quot;?<br />
<a href="text.php?deleting_blog=1&amp;id='.$_REQUEST['id'].'">Yes</a> / <a href="'.$_SERVER['HTTP_REFERER'].'">No</a></center>';
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
	if(isset($_REQUEST['id']) )
	{
		$id = $_REQUEST['id'];
		$q_delete = "DELETE FROM $table_blogs WHERE id = '$id'";

		if(mysql_query($q_delete) )
		{
			print $header;
			print "<i>>>> Post deleted.</i>";
			print $footer;
		}
		else
		{
			print $header;
			print "<i>>>> <b>Failed</b> with the following error:</i> ".mysql_error;
			print $footer;
		}
	}
	exit();
}


if(isset($_REQUEST['add']) && $_REQUEST['type'] == "articles")
{
	$categories = render_categories_menu("article_category",NULL,$_SESSION['identity']);
	
	print $header;
	print '<div class="title">Adding an article</div>
<form action="text.php?adding_article=1" method="post" name="add_article">
<table>
<tr>
	<td align="right"><b>Title:</b></td>
	<td><input type="text" name="article_title" class="tbox" size="70" /></td>
</tr>
<tr>
	<td align="right"><b>Category:</b></td>
	<td>'.$categories.'</td>
</tr>
<tr>
	<td align="right"><b>Comments</b></td>
	<td><input type="checkbox" name="open" class="tbox" checked /></td>
</tr>
<tr>
	<td align="right" valign="top"><b>Text:</b></td>
	<td><textarea name="article_text" class="tbox" rows="20" cols="68"></textarea></td>
</tr>
<tr>
	<td></td>
	<td><input type="submit" name="article_submit" value="Post article" class="tbox" /></td>
</tr>
</table>
</form>';

	print $footer;
	

	exit();
}

if(isset($_REQUEST['edit_articles']) )
{
	$username = $_SESSION['identity'];
	if(isset($_REQUEST['select']) )
	{
		$q_cat = mysql_query("SELECT DISTINCT category FROM $table_articles WHERE author = '$username'");
		
		print $header;
		print '<div class="title">Pick a viewing method</div><br />
		You may <a href="text.php?edit_articles=1">view all</a>, or<br /><br />';
		
		if(mysql_num_rows($q_cat) > 0)
		{		
			print '<b>By category</b>
			<ul>';
			
			while($cat = mysql_fetch_object($q_cat) )
			{
				print '<li><a href="text.php?edit_articles=1&amp;category='.text_out($cat->category).'">'.text_out($cat->category).'</a></li>';
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
			$previous = '<a href="text.php?edit_articles=1'.$previous.'">Older &gt;&gt;</a>';
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
			$next = '<a href="text.php?edit_article=1'.$next.'">&lt;&lt; Newer</a>';
		}
		else
		{
			$next = 'No newer entries';
		}		
		
		print '<div class="title">Edit your articles</div><br />
		<table>
		<tr>
		<td colspan="6" class="boxen"><center>'.$next.' | '.$previous.'</center></td>
		</tr>
		<tr>
			<th class="boxen">Title</th>
			<th class="boxen">Date</th>
			<th class="boxen"><a href="text.php?edit_articles=1">Category</a></th>
			<th class="boxen">Status</th>
			<th class="boxen">Edit</th>
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
						 <td class="boxen">'.text_out($edit->title).'</td>
						 <td class="boxen">'.date($admin_date,$edit->date).'
						 <td class="boxen"><a href="text.php?edit_articles=1&amp;category='.text_out($edit->category).'">'.text_out($edit->category).'</a></td>
						 <td class="boxen">'.$status.'</td>
						 <td class="boxen"><a href="text.php?editing_article=1&amp;id='.$edit->id.'">Edit</a></td>
						 <td class="boxen"><a href="text.php?delete_article=1&amp;id='.$edit->id.'">Remove</a></td>
						 </tr>';
		}
	
		print '</table>';
	}
	print $footer;	
	exit();
}


if(isset($_REQUEST['editing_article']) )
{
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
		
			$categories = render_categories_menu("article_category",$editing->category,$_SESSION['identity']);
			
			print $header;
			print '<div class="title">Editing an article post</div>
<form action="text.php?adding_article=1&id='.$editing->id.'" method="post" name="add_article">
<table>
<tr>
	<td align="right"><b>Title:</b></td>
	<td><input type="text" name="article_title" class="tbox" size="70" value="'.text_out($editing->title).'" /></td>
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
	<td><textarea name="article_text" class="tbox" rows="20" cols="68">'.to_raw($editing->body).'</textarea></td>
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

if(isset($_REQUEST['delete_article']) )
{
	if(isset($_REQUEST['id']) )
	{
		$q_edit = mysql_query("SELECT title FROM $table_articles WHERE id = '".$_REQUEST['id']."'");

	print $header;
		while($blog = mysql_fetch_object($q_edit) )
		{
			print '<center>Are you sure you wish to remove the entry entitled, &quot;'.text_out($blog->title).'&quot;?<br />
<a href="text.php?deleting_article=1&amp;id='.$_REQUEST['id'].'">Yes</a> / <a href="'.$_SERVER['HTTP_REFERER'].'">No</a></center>';
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
		$q_delete = "DELETE FROM $table_articles WHERE id = '$id'";

		if(mysql_query($q_delete) )
		{
			print $header;
			print "<i>>>> Post deleted.</i>";
			print $footer;
		}
		else
		{
			print $header;
			print "<i>>>> <b>Failed</b> with the following error:</i> ".mysql_error;
			print $footer;
		}
	}
	exit();
}


/* main menu */

print $header;

print '<div class="title">Textual content</div><br />
Blogs :
<ul>
<li><a href="text.php?add=1&amp;type=blogs">Add an entry</a></li>
<li><a href="text.php?edit_blogs=1&amp;select=1">Edit entries</a></li>
</ul>
Articles :
<ul>
<li><a href="text.php?add=1&amp;type=articles">Add an article</a></li>
<li><a href="text.php?edit_articles=1&amp;select=1">Edit articles</a></li>
</ul>';

print $footer;

?>
