<?php

$admin_area = TRUE;
$dir_path = "../";
require("../config.php");
require("../functions/db.php");
require("../functions/authentication.php");
require("../functions/textparse.php");

if(isset($_REQUEST['updating_user']) )
{
	print $header;
	
	if(!empty($_REQUEST['edit_password']) || !empty($_REQUEST['edit_password_confirm']) )
	{
		if($_REQUEST['edit_password'] == $_REQUEST['edit_password_confirm'])
		{
			$password = $_REQUEST['edit_password'];
			$id = $_REQUEST['id'];
			$q_update = "UPDATE $table_admins SET password = '".md5($password)."' WHERE username = '".$_SESSION['identity']."'";
			
			if(mysql_query($q_update) )
			{
				print '<i>>>> Password updated.</i><br />';
			}
			else
			{
				print '<i>>>> An error has occured: '.mysql_error().'<br />
				Password was not updated. Please go back and try again.</i><br />';
			}
		}
		else
		{
			print '<i>>>> Passwords did not match. Please go back and try again.</i><br />';
		}
	}
	
	$id = $_REQUEST['id'];
	$q_update2 = "UPDATE $table_admins SET email = '".text_in($_REQUEST['edit_email'])."',
						info = '".to_html($_REQUEST['edit_info'])."',
						timezone = '".text_in($_REQUEST['edit_timezone'])."'
						WHERE username = '".$_SESSION['identity']."'";
						
	if(mysql_query($q_update2) )
	{
		print '<i>>>> Profile updated.
					<blockquote><a href="index.php">Go to Main</a></blockquote></i>';
	}
	else
	{
		print '<i>>>> An error has occured: '.mysql_error().'<br />
			Please go back and try again.</i>';
	}
	
	print $footer;
	exit();

}

function rank($rank)
{
	if($rank == 0)
	{
		$output = "Founder";
	}
	elseif($rank == 10)
	{
		$output = "Primary admin";
	}
	elseif($rank == 20)
	{
		$output = "Secondary admin";
	}
	elseif($rank == 30)
	{
		$output = "Restricted admin";
	}
	elseif($rank == 40)
	{
		$output = "Priviledged user";
	}
	elseif($rank == 50)
	{
		$output = "Standard user";
	}
	elseif($rank == 60)
	{
		$output = "Restricted user";
	}
	else
	{
		$output = "Invalid rank";
	}
	return $output;
}	

$id = $_REQUEST['id'];
$q_user = mysql_query("SELECT id, real_name, username, rank, email, info, timezone FROM $table_admins WHERE username = '".$_SESSION['identity']."'");

print $header;

while($edit = mysql_fetch_object($q_user) )
{

	print '<div class="title">Updating your settings</div><br />
	<form action="account.php?updating_user=1" method="post" name="edit_user">
	<table>
	<tr>
		<td valign="top" align="right"><b>Name:</b></td>
		<td><input type="text" name="edit_name" class="tbox" value="'.text_out($edit->real_name).'" /></td>
	</tr>
	<tr>
		<td valign="top" align="right"><b>Password:</b></td>
		<td><input type="password" name="edit_password" class="tbox" /></td>
	</tr>
	<tr>
		<td valign="top" align="right"><b>Confirm:</b></td>
		<td><input type="password" name="edit_password_confirm" class="tbox" />
		<div class="small"><i style="color: red;">*</i>( Type your password into both fields to change. Otherwise, leave it empty)</td>
	</tr>
	<tr>
		<td valign="top" align="right"><b>Timezone:</b></td>
		<td><input type="text" name="edit_timezone" class="tbox" value="'.$edit->timezone.'" />
		<div class="small"><i style="color: red;">*</i>( Please enter timezones in this format: +|- # ie. +800 )</div></td>
	</tr>
	<tr>
		<td valign="top" align="right"><b>Email:</b></td>
		<td><input type="text" name="edit_email" class="tbox" value="'.$edit->email.'" /></td>
	</tr>
	<tr>
		<td valign="top" align="right"><b>Biography:</b></td>
		<td><textarea name="edit_info" class="tbox" cols="60" rows="10">'.text_out($edit->info).'</textarea>
		<div class="small"><i style="color: red;">*</i>( You may use HTML in this field )</div></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="submit" name="submit" value="Apply changes" class="tbox" /></td>
	</table>
	</form>';
	
	exit();
}

print $footer;


?>
