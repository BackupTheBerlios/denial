<?php

// render.php

$dir_path = "../";

//require("../config.php");
//require("../functions/db.php");

function render_date($type, $date)
{

	if($type = 1)
	{
		$output = date($date_format_one);
	}
	elseif($type = 2)
	{
		$output = date($date_format_two);
	}
	else
	{
		$output = date($date_format_three);
	}
	
	return $output;	
}

function render_categories_menu($name,$default,$username)
{

	global $table_category;

	if($username == "ALL")
	{
		$q_category = mysql_query("SELECT category FROM ".$table_category."");
	}
	else
	{
		$q_category = mysql_query("SELECT category FROM ".$table_category." WHERE owner = '".$username."'");
	}

	$output = '<select name="'.$name.'" class="tbox">';

	if($default != NULL)
	{
		$output .= '<option value="'.text_out($default).'">'.text_out($default).'</option>
<option value=""></option>';
	}
	
	while($category = mysql_fetch_object($q_category) )
	{
		$output .= '<option value="'.text_out($category->category).'">'.text_out($category->category).'</option>';
	}

	$output .= '</select>';

	return $output;

}

function render_links_menu($name, $default, $category, $username)
{
	global $table_links, $table_category;
	
	if($category != NULL && is_int($categories) )
	{
		$q_cat = mysql_query("SELECT category FROM $table_category WHERE id = '$category'");
		
		while($sthing = mysql_fetch_object($q_cat))
		{
			$category = $cat->category;
		}
	}
	
	if($category != NULL)
	{
		$extend = " AND category = '$category'";
	}
	
	$q_category = mysql_query("SELECT id, name FROM ".$table_links." WHERE owner = '".$username."'".$extend);

	$output = '<select name="'.$name.'" class="tbox">';

	if($default != NULL)
	{
		$output .= '<option value="'.text_out($default).'">'.text_out($default).'</option>
<option value=""></option>';
	}
	
	while($link = mysql_fetch_object($q_category) )
	{
		$output .= '<option value="'.text_out($link->id).'">'.text_out($link->name).'</option>';
	}

	$output .= '</select>';

	return $output;	

}

function render_users_menu($name, $default)
{

	global $table_admins;
	
	$q_admins = mysql_query("SELECT username FROM ".$table_admins);

	$output = '<select name="'.$name.'" class="tbox">';

	if($default != NULL)
	{
		$output .= '<option value="'.text_out($default).'">'.$default.'</option>
<option></option>';
	}
	
	while($admin = mysql_fetch_object($q_admins) )
	{
		$output .= '<option value="'.text_out($admin->username).'">'.text_out($admin->username).'</option>';
	}

	$output .= '</select>';

	return $output;

}

function render_users_blogs($user,$name,$default)
{

	global $table_blog_owners;

	if($user == "ALL")
	{
		$q_blogs = mysql_query("SELECT blog FROM ".$table_blog_owners."");
	}
	else
	{
		$q_blogs = mysql_query("SELECT blog FROM ".$table_blog_owners." WHERE owner = '".$user."'");
	}

	$output = '<select name="'.$name.'" class="tbox">';
	
	if($default != NULL)
	{
		$output .= '<option value="'.text_out($default).'">'.text_out($default).'</option>
<option value=""></option>';
	}
	
	while($blogs = mysql_fetch_object($q_blogs) )
	{
		$output .= '<option value="'.text_out($blogs->blog).'">'.text_out($blogs->blog).'</option>';
	}

	$output .= '</select>';

	return $output;
}


function render_blog_titles($name, $start, $limit)
{
	global $table_blogs;
	$username = $_SESSION['identity'];
	$output = NULL;

	$q_title = mysql_query("SELECT id, title, UNIX_TIMESTAMP(date) as date FROM $table_blogs WHERE author = '$username' ORDER BY date DESC LIMIT $start,$limit");

	if(mysql_num_rows($q_title) < 1)
	{
		$output .= print "<i>>>> An error has occured or there is no such user</i>";
	}
	else
	{
		while($title = mysql_fetch_object($q_title) )
		{
			$output .= '<b><a href="text.php?edit_blog=1&amp;id='.$title->id.'">'.$title->title.'</a></b><br /><span class="small">'.date("r",$title->date).'</span><br />';
		}

	}

	return $output;

}


//print render_categories_menu("global_categories");
//print render_users_menu("all_users");

?>
