<?php

$admin_area = TRUE;
$dir_path = "../";
require("../config.php");
require("../functions/db.php");
require("../functions/authentication.php");
require("../functions/textparse.php");

if($_SESSION['rank'] != 0){
	die("you are not authorized to view this page");
}

if(isset($_REQUEST['removing'])){

	$username = $_REQUEST['username'];

	$query = "DELETE FROM $table_admins WHERE username='$username' LIMIT 1";

	print $header;
	print "removal of user was ".remove_db($query)."<br />";
	print $footer;

	exit();

}


if(isset($_REQUEST['remove'])){

	print $header;
	print '<div align="center">are you sure you wish to remove the specified user [ '.$_REQUEST['removeuser'].' ] ?
<br /><br /><br />
<b><a href="users.php?removing=1&amp;username='.$_REQUEST['removeuser'].'">yes</a> / <a href="users.php">no</a></b></div><br />';
	print $footer;
exit();

}

if(isset($_REQUEST['adding'])){

	$username = $_REQUEST['username'];
	$password = md5($_REQUEST['password']);
	if(empty($rank)){
		$rank = 10;
	}else{
		$rank = $_REQUEST['rank'];
	}
	$email = $_REQUEST['email'];
	$info = to_html($_REQUEST['info']);
	$timezone = $_REQUEST['timezone'];

	$query = "INSERT INTO $table_admins VALUES('','$username','$password','$rank','$email','$info','$timezone')";

	print $header;

	print "new user creation ".insert_db($query);

	print $footer;

	exit();

}

print $header;

if($_SESSION['rank'] == 0){
	print '<form name="form" method="post" action="users.php?remove=1">
	<div class="title">[ remove a user ]</div><br />
	<select name="removeuser" class="tbox">';
	$query = mysql_query("SELECT id,username,rank FROM $table_admins");
	while($people = mysql_fetch_array($query)){
		if($people['rank'] != 0){
			print '<option value="'.$people['username'].'">'.$people['username'].'</option>';
		}
	}
	print '</select><br /><br />
	<input class="tbox" name="submit" type="submit" value="remove" /></form>';
}

print '<form name="form" method="post" action="users.php?adding=1">
<div class="title">[ add a user ]</div>'.$bbCodeText.'
<table width="600">
<tr>
	<td>username :</td>
	<td><input type="text" class="tbox" name="username" /></td>
</tr>
<tr>
	<td>password :</td>
	<td><input type="text" class="tbox" name="password" /></td>
</tr>
<tr>
	<td>email address :</td>
	<td><input type="text" class="tbox" name="email" /></td>
</tr>
<tr>
	<td>rank (0-20) :</td>
	<td><input type="text" class="tbox" name="rank" /></td>
</tr>
<tr>
	<td>timezone (+/-N) :</td>
	<td><input type="text" class="tbox" name="timezone" /></td>
</tr>
<tr>
	<td>info (use absolute links) :</td>
	<td><textarea class="tbox" name="info" cols="35" rows="10"></textarea></td>
</tr>
<tr>
	<td><input class="tbox" name="submit" type="submit" value="update" /></td>
</tr>
</table>
</form>';

print $footer;
?>