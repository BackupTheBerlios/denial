<?php

require("functions/xmlrpc.php");

$client = new IXR_Client('http://192.168.1.2/denial/rpc.php');
$client->debug = true;

if(!$client->query('blogger.getUsersBlogs', "943478217472357234234938249", "Kevin", "heh") )
{
	die('<b>Error</b> '.$client->getErrorCode().' : '.$client->getErrorMessage());
}
else
{
	$response = $client->getresponse();
}
	
?>
