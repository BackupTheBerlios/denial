<?php

function remove_user_content($username,$category)
{
	global $table_blogs, $table_articles, $table_uploads, $dir_path;
	
	if($category != NULL)
	{
		$extend	= " AND category = '$category'";
	}
	else
	{
		$extend = "";
	}

	$q_blog		= "DELETE FROM $table_blogs WHERE author = '$username'".$extend;
	$q_articles	= "DELETE FROM $table_articles WHERE author = '$username'".$extend;
	$q_downloads	= mysql_query("SELECT id, filename FROM $table_uploads WHERE owner = '$username'".$extend);

	if(mysql_query($q_blog) )
	{
		$output .= '<i>>>> Blogs removed</i><br />';
	}
	else
	{
		$output .= '<i>>>> Blogs weren\'t removed. An error occured: '.mysql_error().'</i><br />';
	}

	if(mysql_query($q_articles) )
	{
		$output .= '<i>>>> Articles removed</i><br />';
	}
	else
	{
		$output .= '<i>>>> Articles weren\'t removed. An error occured: '.mysql_error().'<br />';
	}

	while($remove = mysql_fetch_object($q_downloads) )
	{
		unlink($dir_path."uploads/".$_SESSION['identity']."/".$remove->filename);
		unlink($dir_path."uploads/".$_SESSION['identity']."/thumbnails/".$remove->filename);
	}
	
	mysql_query("DELETE FROM $table_uploads WHERE owner = '".$_SESSION['identity']."' AND category = '$category'");
	
	$output .= '<i>>>> '.mysql_num_rows($q_downloads).' files removed.</i><br />';
	
	return $output;
}
