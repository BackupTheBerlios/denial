<?php

require("common.php");

if(isset($_REQUEST['delete'])){
	if(isset($_REQUEST['id'])){
		if(isset($_REQUEST['type'])){
			if(isset($_SESSION['identity'])){
				$id = $_REQUEST['id'];
				$type = $_REQUEST['type'];
				$query = "DELETE FROM $table_comments WHERE id='$id' AND type = '$type' LIMIT 1";
				if(!mysql_query($query) )
				{
				exit(mysql_error());
				}
				header("Location:".$_SERVER['HTTP_REFERER']);
				exit();
			}
		}
	}
}

if(isset($_REQUEST['commenting'])){
	if(isset($_REQUEST['id']) && isset($_REQUEST['type']) && !empty($_REQUEST['comment'])){

		$p_id		= $_REQUEST['id'];
		$author		= text_in($_REQUEST['name']);
		$email		= text_in($_REQUEST['url']);
		$comment	= substr(urlify(text_in($_REQUEST['comment'])),0,1000);
		$type		= $_REQUEST['type'];
		$ip			= $_SERVER['REMOTE_ADDR'];
		$mask		= gethostbyaddr($ip);

		if(empty($author)){
			$author = "anonymous";
		}
		if(empty($email)){
			$email = "no email";
		}

		insert_db("INSERT INTO $table_comments VALUES ('', '$author', '$email', '$comment', NOW() , '$p_id', '$type' , '$ip' , '$mask')");
		header("Location:".$_SERVER['HTTP_REFERER']);
	}
	else
	{
		header("Location:".$_SERVER['HTTP_REFERER']);
	}
	exit();
}


?>
