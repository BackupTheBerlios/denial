<?php

$dir_path = "./";
require("config.php");
require("functions/db.php");
require("functions/stats.php");
require("functions/textparse.php");
require("functions/user_render.php");

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

if(isset($_REQUEST['id']) )
{
	$id = $_REQUEST['id'];
}

if(isset($_REQUEST['type']) )
{
	$type = $_REQUEST['type'];
}
else
{
	$type = NULL;
}

if(isset($_REQUEST['category']) )
{
	$category = $_REQUEST['category'];
}
else
{
	$category = NULL;
}

?>
