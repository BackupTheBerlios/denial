<?php

if(!isset($_REQUEST['installing'])){

	print '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>The Denial/CMS Installer ( or if you prefer, "teh dnl/cms installeh" )</title>
<style>
body {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	background-color: #FFFFFF;
	padding: 9px;
	color: #737373;
}
#top {
	padding: 10px 10px 10px 10px;
	height: 100px;
	position: absolute;
	left:0px;
	top:0px;
}

#main {
	padding: 10px 10px 10px 10px;
	margin-bottom: 8px;
	line-height: 16px;
}
.title {
	font-size: 14px;
	font-style: italic;
	line-height: 18px;
	color: #4B4B4B;
}
.small {
	font-size: 11px;
	line-height: 11px;
}
a:link, a:active, a:visited {
	color: #0076A3;
	background-color: transparent; 
	text-decoration: none;
}
a:hover {
	color: #0076A3;
	background-color: transparent; 
	text-decoration: none;
	font-weight:bold;
}
.tbox{
	background-color: #FFFFFF;
	border: 1px solid #737373;
	color: #737373;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
}
img{
	border: 0px;
}
blockquote {
	margin-left:30px;
	margin-top:0px;
	margin-bottom:0px;
	margin-right:0px;
}
</style>
</head>
<body>
<div id="top">
<i>denial<b>site</b>system<b>installer</b></i>
</div>
<div id="main">
<div class="title">
Introduction
</div><br />
A word from the author : <br /><br />
<blockquote>
&quot; The Denial System was initially written for my own personal use. I had a site and i didn\'t want to use something large and heavy. I was, and still am a PHP coder. So i set out to make my own Content Management System ( CMS, for short ). This is the 8th revision of that effort. I decided to release my code at some point, hoping that maybe, just maybe, someone might find it useful. Knock yourself out. &quot;<br /><br />
-- seraph
</blockquote><br />
<div class="title">
The Installation
</div><br />
First please make sure that you have a web host that supports :
<ul>
<li> Apache ( its a webserver ) </li>
<li> PHP ( its an intepreted scripting OOP language ) </li>
<li> MySQL ( its a database ) </li>
</ul>
Then, fill in the form below with the appropriate details ( after actually uploading the files to the webserver ). You should be able to the details necessary from
your hosting service. I suggest that you read the readme first.<br /><br />
<form name="form1" id="form1" method="post" action="'.$_SERVER['PHP_SELF'].'?installing=1">
<table style="font-size: 12px;">
	<tr>
		<td align="right">default username ( this is the God Admin ) :</td>
		<td><input type="text" name="default_username" maxlength="50" class="tbox"></td>
	</tr>
	<tr>
		<td align="right">default password ( don\'t forget this! ) :</td>
		<td><input type="text" name="default_password" maxlength="50" class="tbox"></td>
	</tr>
	<tr>
		<td align="right">database server :</td>
		<td><input type="text" name="d_server" maxlength="50" class="tbox"></td>
	</tr>
	<tr>
		<td align="right">database :</td>
		<td><input type="text" name="d_name" maxlength="50" class="tbox"></td>
	</tr>
		<td align="right">table prefix :</td>
		<td><input type="text" name="d_t_prefix" maxlength="50" class="tbox"></td>
	</tr>
	</tr>
		<td align="right">database username :</td>
		<td><input type="text" name="d_username" maxlength="50" class="tbox"></td>
	</tr>
	</tr>
		<td align="right">database password :</td>
		<td><input type="text" name="d_password" maxlength="50" class="tbox"></td>
	</tr><br /><br />
	</tr>
		<td align="right">site name :</td>
		<td><input type="text" name="s_name" maxlength="50" class="tbox"></td>
	</tr>
	</tr>
		<td align="right">site address ( full http://address.com/ URL ) :</td>
		<td><input type="text" name="s_address" maxlength="50" class="tbox"></td>
	</tr>
	</tr>
		<td align="right">site image ( a button of sorts ) :</td>
		<td><input type="text" name="s_image" maxlength="50" class="tbox"></td>
	</tr>
	</tr>
		<td align="right">site description :</td>
		<td><input type="text" name="s_desc" maxlength="50" class="tbox"></td>
	</tr>
	</tr>
		<td align="right">site language ( please use "en-us" style language definitions ) :</td>
		<td><input type="text" name="s_lang" maxlength="50" class="tbox"></td>
	</tr>
	</tr>
		<td align="right">site webmaster :</td>
		<td><input type="text" name="s_webmaster" maxlength="50" class="tbox"></td>
	</tr>
	</tr>
		<td align="right">site copyright statement ( you may use HTML ) :</td>
		<td><textarea class="tbox" name="s_copyright" cols="40" rows="10" class="tbox"></textarea></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="submit" name="submit" value="Install"  class="tbox"></td>
	</tr>
</table>
</form>
</div>
<a href="http://validator.w3.org/check/referer"><img src="http://www.w3.org/Icons/valid-html401" alt="Valid HTML 4.01!"></a><a href="http://jigsaw.w3.org/css-validator/"><img src="http://jigsaw.w3.org/css-validator/images/vcss" alt="Valid CSS!"></a>
</body>
</html>';

}

if(isset($_REQUEST['installing'])){


	$mysql_server		= $_REQUEST['d_server'];
	$mysql_default_db	= $_REQUEST['d_name'];
	$mysql_prefix		= $_REQUEST['d_t_prefix'];
	$mysql_user			= $_REQUEST['d_username'];
	$mysql_password		= $_REQUEST['d_password'];

	$prefsFile = fopen("prefs.php","w");

	$text = '<?php

// feel free to edit these vars

$siteName			= "'.$_REQUEST['s_name'].'";
$siteAddress		= "'.$_REQUEST['s_address'].'";	# please note that this is used only for the RSS feed
$siteDescription	= "'.$_REQUEST['s_desc'].'";
$siteLanguage		= "'.$_REQUEST['s_lang'].'";
$siteWebmaster		= "'.$_REQUEST['s_webmaster'].'";
$siteCopyright		= "'.$_REQUEST['s_copyright'].'";

// choose the index page, and xml backend type. either news or blogs. if its 
// set to blogs, the the xml file is for the blogs page, and the index will redirect you 
// straight to the blogs page trasparently. else, it will show the index, and the xml file 
// will have the indexes entries. choose well, "news" or "blogs".

$rssType = "blogs";
$rssTable = $mysql_prefix.$rssType;

//choose the theme. provide a path like "themes/themeName/", with proper capitalisation.

$theme_path = "themes/steev/";

$copyright = $siteCopyright;

?>';

	if(fwrite($prefsFile,$text)){

		print "Prefs file written : success<br />";

	}else{

		print "Prefs file written : failed<br />";

	}

	$configFile = fopen("config.php","w");

	$text = '<?php

session_start();

ini_set("arg_separator.output","&amp;");
ini_set("default_mimetype","text/html");
ini_set("default_charset","UTF-8");

$mysql_server	= "'.$mysql_server.'";		#host is usually localhost unless the database server isn\'t on the same machine
$mysql_user		= "'.$mysql_user.'";	#hehe this one is tricky eh ?
$mysql_password	= "'.$mysql_password.'";	#ooooh
$mysql_default_db	= "'.$mysql_default_db.'";	#choose a database to use :)
$mysql_prefix = "'.$mysql_prefix.'";	#choose the prefix, the installer should take care of this.

require($dir_path."prefs.php");

# don\'t mess with anything below.

$GLOBALS[\'mysql_prefix\'] = $mysql_prefix;

$table_comments = $mysql_prefix."comments";
$table_news = $mysql_prefix."news";
$table_blogs = $mysql_prefix."blogs";
$table_articles = $mysql_prefix."articles";
$table_downloads = $mysql_prefix."downloads";
$table_admins = $mysql_prefix."admins";
$table_stats = $mysql_prefix."stats";
$table_category = $mysql_prefix."category";
$table_links = $mysql_prefix."links";
$table_moods = $mysql_prefix."moods";

require($dir_path.$theme_path."theme.php");

?>';

	if(fwrite($configFile,$text)){

		print "Config file written : success<br />";

	}else{

		print "Config file written : failed<br />";

	}

	$query1 = "CREATE TABLE ".$mysql_prefix."admins (
		id int(2) unsigned NOT NULL auto_increment,
		username varchar(30) NOT NULL default '',
		password varchar(60) NOT NULL default '',
		rank int(2) unsigned NOT NULL default '0',
		email varchar(50) NOT NULL default '',
		info text NOT NULL,
		timezone varchar(6) NOT NULL default '',
		PRIMARY KEY  (id)
		) TYPE=MyISAM";

	$query2 = "CREATE TABLE ".$mysql_prefix."articles (
		id int(5) unsigned NOT NULL auto_increment,
		author varchar(20) NOT NULL default '',
		title varchar(255) NOT NULL default '',
		body text NOT NULL,
		category varchar(255) NOT NULL default '',
		date timestamp(14) NOT NULL,
		PRIMARY KEY  (id)
		) TYPE=MyISAM";

	$query3 = "CREATE TABLE ".$mysql_prefix."blogs (
		id int(5) unsigned NOT NULL auto_increment,
		author varchar(20) NOT NULL default '',
		title varchar(255) NOT NULL default '',
		body text NOT NULL,
		category varchar(255) NOT NULL default '',
		mood varchar(255) NOT NULL default '',
		listening varchar(255) NOT NULL default '',
		date timestamp(14) NOT NULL,
		PRIMARY KEY  (id)
		) TYPE=MyISAM";

	$query4 = "CREATE TABLE ".$mysql_prefix."category (
		id int(3) unsigned NOT NULL auto_increment,
		category varchar(255) NOT NULL default '',
		PRIMARY KEY  (id)
		) TYPE=MyISAM";

	$query5 = "CREATE TABLE ".$mysql_prefix."comments (
		id int(10) unsigned NOT NULL auto_increment,
		author varchar(50) NOT NULL default '',
		email varchar(70) NOT NULL default '',
		comment text NOT NULL,
		date timestamp(14) NOT NULL,
		p_id int(10) unsigned NOT NULL default '0',
		type char(2) NOT NULL default '',
		ip varchar(15) NOT NULL default '',
		mask varchar(255) NOT NULL default '',
		PRIMARY KEY  (id)
		) TYPE=MyISAM";

	$query6 = "CREATE TABLE ".$mysql_prefix."downloads (
		id int(4) unsigned NOT NULL auto_increment,
		filename varchar(50) NOT NULL default '',
		filename2 varchar(50) NOT NULL default '',
		description text NOT NULL,
		date timestamp(14) NOT NULL,
		owner varchar(30) NOT NULL default '',
		public int(1) unsigned NOT NULL default '0',
		category varchar(20) NOT NULL default '',
		counter int(11) unsigned NOT NULL default '0',
		PRIMARY KEY  (id)
		) TYPE=MyISAM";

	$query7 = "CREATE TABLE ".$mysql_prefix."links (
		id int(11) NOT NULL auto_increment,
		name varchar(255) NOT NULL default '',
		url varchar(255) NOT NULL default '',
		category varchar(255) NOT NULL default '',
		PRIMARY KEY  (id)
		) TYPE=MyISAM";

	$query8 = "CREATE TABLE ".$mysql_prefix."news (
		id int(5) unsigned NOT NULL auto_increment,
		author varchar(20) NOT NULL default '',
		title varchar(255) NOT NULL default '',
		blurb text NOT NULL,
		body text NOT NULL,
		category varchar(255) NOT NULL default '',
		date timestamp(14) NOT NULL,
		PRIMARY KEY  (id)
		) TYPE=MyISAM";

	$query9 = "CREATE TABLE ".$mysql_prefix."stats (
		id smallint(10) unsigned NOT NULL auto_increment,
		ip varchar(15) NOT NULL default '0',
		mask varchar(255) NOT NULL default '',
		referrer varchar(255) NOT NULL default '',
		date timestamp(14) NOT NULL,
		PRIMARY KEY  (id)
		) TYPE=MyISAM";


	$query10 = "INSERT INTO ".$mysql_prefix."admins VALUES (1, '".$_REQUEST['default_username']."', '".md5($_REQUEST['default_password'])."', 0, '', '', '+0')";

	function insert_db($query){

		if(mysql_query($query)){
			return "successful";
		}else{
			return "failed - ".mysql_error();
		}
	}

	$query11 = "CREATE TABLE ".$mysql_prefix."moods (
		id int(10) unsigned NOT NULL auto_increment,
		mood varchar(255) NOT NULL default '',
		PRIMARY KEY  (id)
		) TYPE=MyISAM";

	$query12 = "INSERT INTO ".$mysql_prefix."moods VALUES ('','Nothing in particular')";

	$connection = mysql_connect($mysql_server,$mysql_user,$mysql_password);
	//$connection = mysql_connect($mysql_server);
	if(!mysql_select_db($mysql_default_db)){
		print mysql_error()."<br />";
	}

	print "Query 1 : ".insert_db($query1)."<br />";
	print "Query 2 : ".insert_db($query2)."<br />";
	print "Query 3 : ".insert_db($query3)."<br />";
	print "Query 4 : ".insert_db($query4)."<br />";
	print "Query 5 : ".insert_db($query5)."<br />";
	print "Query 6 : ".insert_db($query6)."<br />";
	print "Query 7 : ".insert_db($query7)."<br />";
	print "Query 8 : ".insert_db($query8)."<br />";
	print "Query 9 : ".insert_db($query9)."<br />";
	print "Query 10 : ".insert_db($query10)."<br />";
	print "Query 11 : ".insert_db($query11)."<br />";
	print "Query 12 : ".insert_db($query12)."<br />";
	print "<br /><br />please delete install.php. your new site is <a href=\"./\">here</a>";

}

?>