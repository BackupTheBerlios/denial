<?php

#connect

$connection = mysql_connect($mysql_server,$mysql_user,$mysql_password);
//$connection = mysql_connect($mysql_server);
mysql_select_db($mysql_default_db);

# add 

function insert_db($query){

	if(mysql_query($query)){
		return "successful";
	}else{
		return "failed - ".mysql_error();
	}
}

function update_db($query){

	if(mysql_query($query)){
		return "successful";
	}else{
		return "failed - ".mysql_error();
	}
}

function remove_db($query){

	if(mysql_query($query)){
		return "successful";
	}else{
		return "failed - ".mysql_error();
		
	}
}

?>