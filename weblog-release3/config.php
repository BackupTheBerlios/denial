<?php

session_start();

ini_set("arg_separator.output","&amp;");
ini_set("default_mimetype","text/html");
ini_set("default_charset","UTF-8");


$mysql_server	= "localhost";		#host is usually localhost unless the database server isn't on the same machine
$mysql_user		= "kevin";	#hehe this one is tricky eh ?
$mysql_password	= "hehword";	#ooooh
$mysql_default_db	= "devdb";	#choose a database to use :)
$mysql_prefix = "dev1_";	#choose the prefix, the installer should take care of this.


require($dir_path."prefs.php");

# don't mess with anything below.

$GLOBALS['mysql_prefix'] = $mysql_prefix;

$table_comments = $mysql_prefix."comments";
$table_news = $mysql_prefix."news";
$table_blogs = $mysql_prefix."blogs";
$table_blog_owners = $mysql_prefix."blog_owners";
$table_articles = $mysql_prefix."articles";
$table_uploads = $mysql_prefix."downloads";
$table_admins = $mysql_prefix."users";
$table_stats = $mysql_prefix."stats";
$table_category = $mysql_prefix."category";
$table_links = $mysql_prefix."links";
$table_moods = $mysql_prefix."moods";

$admin_date = "r";

if($admin_area == TRUE)
{
	$theme_path = "admin/theme/";
	require($dir_path.$theme_path."theme.php");
}
else
{
	$theme_path = "themes/".$theme_name."/";
	require($dir_path.$theme_path."theme.php");
}

?>
