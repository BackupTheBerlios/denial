<?php

$dir_path = "./";
require("config.php");
require("functions/db.php");
require("functions/stats.php");
require("functions/textparse.php");

# start older/newer items


if(isset($_REQUEST['id'])){

	print $header;

	@$id = $_REQUEST['id'];
	$query = mysql_query(" SELECT id , author , title , body , mood , listening , category , UNIX_TIMESTAMP(date) as date FROM $table_blogs WHERE id = '$id' LIMIT 1");
	$search = array("<%id%>","<%author%>","<%title%>","<%body%>","<%date%>","<%comments%>","<%category%>","<%mood%>","<%listening%>");
	$content = '';

	while($blogs = mysql_fetch_array($query)){

		$id = $blogs['id'];
		$comments = mysql_query("SELECT id FROM $table_comments WHERE p_id = '$id' AND type = '3'");
		$comments = mysql_num_rows($comments);

		$replace = array($blogs['id'],$blogs['author'],text_out($blogs['title']),text_out($blogs['body']),date($date_format,$blogs['date']),$comments,text_out($blogs['category']),text_out($blogs['mood']),text_out($blogs['listening']));
		$content .= str_replace($search,$replace,$theme_blogs_style);
	}

	if(mysql_num_rows($query) == 0){
		$content = '<div class="title">no entries for this person</div><br />';
	}

	print $content;
	print $footer;

	exit();

}


if(!isset($_REQUEST['start'])){
	$start = 0;
	$GLOBALS['start'] = $start;
}else{
	$start = $_REQUEST['start'];
	$GLOBALS['start'] = $start;
}

$limit = 10;

@$author = $_REQUEST['name'];

$query = mysql_query("SELECT DISTINCT username FROM $table_admins");

if(mysql_num_rows($query) < 2)
{
	$query = mysql_query("SELECT id FROM $table_blogs");
}
else
{
	$query = mysql_query("SELECT id FROM $table_blogs WHERE author='$author'");
}

if(mysql_num_rows($query)> 10){

	$limit_link = '[ <a href="'.$_SERVER['PHP_SELF'].'?name='.$_REQUEST['name'].'">home</a> ] ';

	if($start < mysql_num_rows($query)-10){
		$start2 = $start+10;
		$limit_link .= '[ <a href="'.$_SERVER['PHP_SELF'].'?name='.$_REQUEST['name'].'&amp;start='.$start2.'">older items</a> ] ';
	}

	if($start> 9){
		$start2 = $start-10;
		$limit_link .= ' [ <a href="'.$_SERVER['PHP_SELF'].'?name='.$_REQUEST['name'].'&amp;start='.$start2.'">newer items</a> ]';
	}

	$limit_link .= "<br />";

}else{
	$limit_link = "";
}


#end older/newer items

if(!isset($_REQUEST['name'])){

	$query = mysql_query("SELECT DISTINCT username FROM $table_admins");

	if(mysql_num_rows($query) < 2)
	{

		print $header;

		$query = mysql_query(" SELECT id , author , title , body , mood , listening , category , UNIX_TIMESTAMP(date) as date FROM $table_blogs ORDER BY date DESC LIMIT $start,$limit");
		$search = array("<%id%>","<%author%>","<%title%>","<%body%>","<%date%>","<%comments%>","<%category%>","<%mood%>","<%listening%>");
		$content = '';

		while($blogs = mysql_fetch_array($query)){

			$id = $blogs['id'];
			$comments = mysql_query("SELECT id FROM $table_comments WHERE p_id = '$id' AND type = '3'");
			$comments = mysql_num_rows($comments);

			$replace = array($blogs['id'],$blogs['author'],text_out($blogs['title']),text_out($blogs['body']),date($date_format,$blogs['date']),$comments,text_out($blogs['category']),text_out($blogs['mood']),text_out($blogs['listening']));
			$content .= str_replace($search,$replace,$theme_blogs_style);
		}

		if(mysql_num_rows($query) == 0){
			$content = '<div class="title">no entries for this person</div><br />';
		}

		print $content.$limit_link;
		print $footer;
		exit();

	}

	print $header;

	$query = mysql_query("SELECT DISTINCT username FROM $table_admins");
	$content = '<div class="title">For,</div><ul>';

	while($blogs = mysql_fetch_array($query)){

		$content .= '<li><a href="blogs.php?name='.$blogs['username'].'">'.$blogs['username'].'</a></li>';

	}

	print $content."</ul>";
	print $footer;

}

if(isset($_REQUEST['name'])){

	print $header;

	$author = $_REQUEST['name'];
	$query = mysql_query(" SELECT id , author , title , body , mood , listening , category , UNIX_TIMESTAMP(date) as date FROM $table_blogs WHERE author='$author' ORDER BY date DESC LIMIT $start,$limit");
	$search = array("<%id%>","<%author%>","<%title%>","<%body%>","<%date%>","<%comments%>","<%category%>","<%mood%>","<%listening%>");
	$content = '<div class="title">for '.$author.',</div><br />';

	while($blogs = mysql_fetch_array($query)){

		$id = $blogs['id'];
		$comments = mysql_query("SELECT id FROM $table_comments WHERE p_id = '$id' AND type = '3'");
		$comments = mysql_num_rows($comments);

		$replace = array($blogs['id'],$blogs['author'],text_out($blogs['title']),text_out($blogs['body']),date($date_format,$blogs['date']),$comments,text_out($blogs['category']),text_out($blogs['mood']),text_out($blogs['listening']));
		$content .= str_replace($search,$replace,$theme_blogs_style);
	}

	if(mysql_num_rows($query) == 0){
		$content = '<div class="title">no entries for this person</div><br />';
	}

	print $content.$limit_link;
	print $footer;

}

?>
