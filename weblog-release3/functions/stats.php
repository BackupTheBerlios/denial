<?php

# stats.

$ip = $_SERVER['REMOTE_ADDR'];
$mask = gethostbyaddr($ip);

if(isset($_SERVER['HTTP_REFERER'])){
	$referrer = $_SERVER['HTTP_REFERER'];
}else{
	$referrer = "none";
}

if(!isset($_SESSION['identity'])){
	insert_db("INSERT INTO $table_stats VALUES('','$ip','$mask','$referrer',NOW())");
}

/*

function getmicrotime(){ 
    list($usec, $sec) = explode(" ",microtime()); 
    return ((float)$usec + (float)$sec); 
}

$startTime = getmicrotime();

if(!isset($_SESSION['time'])){
	$_SESSION['time'] = getmicrotime();
}elseif(isset($_SESSION['time'])){
	if(getmicrotime()-$_SESSION['time'] < 0.5){
		print "time since you last visit : ".substr(getmicrotime()-$_SESSION['time'],0,6)." seconds<br />";
		exit("too many connections. either that, or there is someone running his own little DoS attack. please retry in a few seconds (give it some time will ya?). if you still see this ... wait a little more then refresh, ad infinitum");
	}else{
		$_SESSION['time'] = time();
	}
}

*/

?>