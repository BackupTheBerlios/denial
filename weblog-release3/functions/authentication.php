<?php

$table = $mysql_prefix."admins";
$GLOBALS['admin_table'] = $table;

function authform(){

	print "<form name=\"form1\" method=\"post\" action=\"index.php?auth=1\">
		  <table width=\"271\" border=\"0\" align=\"center\">
			<tr>
			  <td width=\"2%\">Username</td>
			  <td width=\"98%\">:
				<input class=\"tbox\" type=\"text\" name=\"username\">
			  </td>
			</tr>
			<tr>
			  <td width=\"2%\">Password</td>
			  <td width=\"98%\">:
				<input class=\"tbox\" type=\"password\" name=\"password\">
			  </td>
			</tr>
			<tr>
			  <td width=\"2%\">
				<input class=\"tbox\" type=\"submit\" name=\"Submit\" value=\"Login\">
			  </td>
			  <td width=\"98%\">&nbsp;</td>
			</tr>
		  </table>
		</form>";

}

function authenticate($username,$password){

	global $table_admins;

	$password = md5($password);

	$query = "SELECT username , password , rank FROM $table_admins WHERE password = '$password' AND username = '$username' ";
	$query = mysql_query($query);

	if(mysql_num_rows($query) == 1){

		while($login = mysql_fetch_array($query)){
			if($username == $login['username'])
			{
				$_SESSION['identity'] = $username;
				$_SESSION['rank'] = $login['rank'];
				$_SESSION['SERVER_NAME'] = $_SERVER['SERVER_NAME'];

				if(eregi("logout",$_SERVER['HTTP_REFERER'])){
					header("Location:index.php");
				}elseif(isset($_SERVER['HTTP_REFERER'])){
					header("Location:".$_SERVER['HTTP_REFERER']);
				}else{
					header("Location:index.php");
				}
			}
			else
			{
				print "wrong password or username - ".mysql_error();
				die();
			}
		}

	}else{ 
		
		print "wrong password or username - ".mysql_error();
		die();
		
	}
}

function logout(){

	session_unset("identity");
	$_SESSION = array();
	unset($_SESSION);

}

function checkrank($level)
{
	if($_SESSION['rank'] > $level)
	{
		print $header;
		print '<b>You are not authorized to perform this action.</b>';
		print $footer;
		exit();
	}
}

if(isset($_REQUEST['logout'])){

	logout();

}

if (isset($_REQUEST['auth'])){
	
	authenticate($_POST['username'],$_POST['password']);

}

if(!isset($_SESSION['identity']) || $_SERVER['SERVER_NAME'] != $_SESSION['SERVER_NAME']){

	print $header;
	authform();
	print $footer;
	exit();

}

?>
