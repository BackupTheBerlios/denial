<?php

ini_set("max_execution_time","90");

$admin_area = TRUE;
$dir_path ="../";
$base_upload_path = $dir_path."uploads";

require($dir_path."config.php");
require($dir_path."functions/db.php");
require($dir_path."functions/authentication.php");
require($dir_path."functions/textparse.php");
require($dir_path."functions/render.php");

$upload_path = $base_upload_path."/".$_SESSION['identity']."/";
$thumbnail_path = $base_upload_path."/".$_SESSION['identity']."/thumbnails/";

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

if(isset($_REQUEST['new']) )
{

	checkrank(40);

	$category = render_categories_menu("file_category",$editing->category,$_SESSION['identity']);

	print $header;
	
	print '<form name="uploadform" method="post" action="./uploads.php?uploading=1" enctype="multipart/form-data">
<div class="title">Upload a file</div>
<table>
<tr>
	<td align="right"><b>File:</b></td>
	<td><input class="tbox"  type="file" name="filename" /></td>
</tr>
<tr>
	<td align="right"><b>Rename:</b></td>
	<td><input class="tbox" type="text" name="rename" />
	<div class="small"><i style="color: red;">*</i>( If you wish to rename the file, fill in this box complete with the extension. )</div></td>
</tr>
<tr>
	<td align="right"><b>Category:</b></td>
	<td>'.$category.'</td>
</tr>
<tr>
	<td align="right"><b>Public:</b></td>
	<td><input type="checkbox" class="tbox" name="public" checked /></td>
</tr>';
	if(function_exists("gd_info"))
	{
	print '<tr>
	<td align="right"><b>Thumbnail:</b></td>
	<td><input type="checkbox" class="tbox" name="thumbnail" />
	<div class="small"><i style="color: red;">*</i>( Please use this <b>only</b> if you are uploading a .PNG or .JPG/.JPEG file. )</div></td>
</tr>
<tr>
	<td align="right" valign="top"><b>Dimensions:</b></td>
	<td><select name="thumb_width" class="tbox">
		<option value="64">64 pixels wide</option>
		<option value="128">128 pixels wide</option>
		<option value="256">256 pixels wide</option>
		<option value="512">512 pixels wide</option>
	</select></td>
</tr>';
}

print '<tr>
	<td align="right" valign="top"><b>Description:</b></td>
	<td><textarea name="description" cols="40" rows="10" class="tbox"></textarea></td>
</tr>
<tr>
	<td></td>
	<td><input type="submit" value="Upload file" class="tbox" /></td>
</tr>
</table>
</form>';
	
	print $footer;
	exit();
}

if(isset($_REQUEST['uploading']) )
{

	checkrank(40);
	
	$error = NULL;
	function error_check($status,$reason)
	{
		global $header, $footer;
		if($status == FALSE)
		{
			print $header;
			print "<i>>>> <b>An error occurred.</b><br />".$reason."</i>";
			print $footer;
			exit();
		}
	}
	
	if(isset($_REQUEST['rename']) )
	{
		$filename = $_FILES['filename']['name'];
	}
	else
	{
		$filename = $_REQUEST['filename'];
	}
	
	$filename2 = text_in($filename);
	$filename = preg_replace("/[^-.&()a-zA-Z0-9\\[\\]]/","_",$filename);
	$i = "";
	while(file_exists($upload_path.$filename)) { 
		$i++; 
		$filename = $i.$filename;
	}	
	$description = text_in($_REQUEST['description']);
	$owner = $_SESSION['identity'];
	if(isset($_REQUEST['public']))
	{
		$public = 1;
	}
	else
	{
		$public = 0;
	}
	$category = text_in($_REQUEST['file_category']);
	
	if(!is_uploaded_file($_FILES['filename']['tmp_name']) && $uploaded == TRUE)
	{
		$uploaded = FALSE;
		$error .= "The file was not transferred.<br />";
		error_check($uploaded, $error);
	}
	
	if(is_dir($upload_path))
	{
		if(!move_uploaded_file($_FILES['filename']['tmp_name'],$upload_path.$filename))
		{
			$uploaded = FALSE;
			$error .= "The file was not moved to it's directory.<br />";
			error_check($uploaded, $error);
		}
	}
	else
	{
		mkdir($upload_path);
		if(is_dir($upload_path))
		{
			if(!move_uploaded_file($_FILES['filename']['tmp_name'],$upload_path.$filename))
			{
				$uploaded = FALSE;
				$error .= "The file was not moved to it's directory.<br />";
				error_check($uploaded, $error);
			}
		}
		else
		{
			$uploaded = FALSE;
			$error .= "The directory did not exist, and failed to be created.<br />";
			error_check($uploaded, $error);
		}
	}
	
	/* image thumbnailing */
	
	if(isset($_REQUEST['thumbnail']))
	{
		$image_type = get_imagesize($upload_path.$filename);
		$image_type = $image_type[2];
		if($image_type != 2 && $image_type != 3)
		{
			$uploaded = FALSE;
			$error .= ">>> Thumbnailing was selected, but the image was not a PNG or JPEG/JPG. You may only use thumbnailing with those file types.<br />'.$image_type.'";
			error_check($uploaded, $error);
		}
		else
		{
			if($image_type == 2)
			{
				$image = imagecreatefromjpeg($upload_path.$filename);
			}
			elseif($image_type == 3)
			{
				$image = imagecreatefrompng($upload_path.$filename);
			}
			
			$image_attr = array();
			$image_attr = getimagesize($upload_path.$filename);
			
			$image_width = $image_attr[0];
			$image_height = $image_attr[1];
			$image_new_width = $_REQUEST['thumb_width'];
			
			$image_ratio = $image_width/$image_new_width;
			$image_new_height = $image_height/$image_ratio;
			
			$thumbnail = imagecreatetruecolor($image_new_width, $image_new_height);
			imagecopyresized($thumbnail, $image, 0, 0, 0, 0, $image_new_width, $image_new_height, $image_width, $image_height);

			if(!is_dir($thumbnail_path) )
			{
				mkdir($thumbnail_path);
			}

			if($image_type == 2)
			{
				if(!imagejpeg($thumbnail, $thumbnail_path.$filename))
				{
					$uploaded = FALSE;
					$error = "Failed to generate JPEG.<br />";
					error_check($uploaded, $error);
				}
			}
			elseif($image_type == 3)
			{
				if(!imagepng($thumbnail, $thumbnail_path.$filename))
				{
					$uploaded = FALSE;
					$error = "Failed to generate PNG.<br />";
					error_check($uploaded, $error);
				}
			}
			else
			{
				$uploaded = FALSE;
				$error = "Image thumbnail copy failed. Perhaps the file was not a jpeg or a png.<br />";
				error_check($uploaded, $error);
			}
			
			$uploaded == TRUE;
		}
	}
	
	$q_upload = "INSERT INTO $table_uploads VALUES('', '$filename', '$filename2', '$description', NOW(), '$owner', '$public', '$category', '0')";
	
	if(mysql_query($q_upload))
	{
		$output = '<i>>>> File was uploaded.<br />
		>>> You may link to this file with this relative URL: '.substr($upload_path.$filename, 3).'<br />';
		if(isset($_REQUEST['thumbnail']))
		{
			$output .= '>>> You may link to this file\'s thumbnail with this relative URL: '.substr($thumbnail_path.$filename, 3);
		}
	}
	else
	{
		$uploaded = FALSE;
		$error .= "Failed at database: ".mysql_error()."<br />";
		error_check($uploaded, $error);
	}

	print $header.$output."</i>".$footer;
	exit();	
}

if(isset($_REQUEST['browse']) )
{
	$username = $_SESSION['identity'];
	if(isset($_REQUEST['select']) )
	{
		$q_cat = mysql_query("SELECT DISTINCT category FROM $table_uploads WHERE owner = '$username'");
		
		if(mysql_num_rows($q_cat) < 1)
		{
			print $header;
			print '<i>>>> No uploads to browse</i>';
			print $footer;
			exit();
		}
		
		print $header;
		print '<div class="title">Pick a viewing method</div><br />
		You may <a href="uploads.php?browse=1">view all</a>, or<br /><br />';
		
		if(mysql_num_rows($q_cat) > 0)
		{		
			print '<b>By category</b>
			<ul>';
			
			while($cat = mysql_fetch_object($q_cat) )
			{
				print '<li><a href="uploads.php?browse=1&amp;category='.text_out($cat->category).'">'.text_out($cat->category).'</a></li>';
			}
			
			print '</ul>';			
		}
		print $footer;
		exit();
	}

	$extend = NULL;
	
	if(!isset($_REQUEST['category']) )
	{
		$_REQUEST['category'] = NULL;
	}	
	
	if(isset($_REQUEST['category']) && !empty($_REQUEST['category']) )
	{
		$extend .= " AND category = '".addslashes($_REQUEST['category'])."' ";
	}

	$q_np = mysql_query("SELECT id FROM $table_uploads WHERE owner = '$username' ".$extend."");
	$up_rows = mysql_num_rows($q_np);
	
	$q_edit = mysql_query("SELECT id, owner, filename, filename2, UNIX_TIMESTAMP(date) as date, category, public, counter FROM $table_uploads WHERE id ".$extend." and owner = '".$_SESSION['identity']."' LIMIT $start, $limit");
	
	print $header;
	
	if($up_rows < 1)
	{
		print '<i>>>> No uploads have been made</i>';
	}
	else
	{
		$previous = NULL;
		if($start < ($up_rows-$limit))
		{
			
			if(isset($_REQUEST['category']) )
			{
				$previous .= '&amp;category='.stripslashes($_REQUEST['category']);
			}
			$previous .= '&amp;start='.($start+$limit);
			$previous = '<a href="uploads.php?browse=1'.$previous.'">Older &gt;&gt;</a>';
		}
		else
		{
			$previous = 'No older entries';
		}
		
		$next = NULL;
		if($start < ($up_rows+$limit) && ($start+$limit) > $limit)
		{
			
			if(isset($_REQUEST['category']) )
			{
				$next .= '&amp;category='.stripslashes($_REQUEST['category']);
			}
			$next .= '&amp;start='.($start-$limit);
			$next = '<a href="uploads.php?browse=1'.$next.'">&lt;&lt; Newer</a>';
		}
		else
		{
			$next = 'No newer entries';
		}		
		
		print '<div class="title">Browsing your uploads</div><br />
		<table>
		<tr>
		<td colspan="6" class="boxen"><center>'.$next.' | '.$previous.'</center></td>
		</tr>
		<tr>
			<th class="boxen">File</th>
			<th class="boxen">Date</th>
			<th class="boxen">Owner</th>
			<th class="boxen"><a href="uploads.php?browse=1">Category</a></th>
			<th class="boxen">Status</th>
			<th class="boxen">Downloads</th>
		</tr>';
		
		while($edit = mysql_fetch_object($q_edit) )
		{
			if($edit->open == 0)
			{
				$status = "Private";
			}
			else
			{
				$status = "Public";
			}
			
			print '<tr>
						 <td class="boxen"><a href="uploads.php?viewfile=1&amp;id='.$edit->id.'">'.text_out($edit->filename2).'</a></td>
						 <td class="boxen">'.date($admin_date,$edit->date).'
						 <td class="boxen">'.text_out($edit->owner).'
						 <td class="boxen"><a href="uploads.php?browse=1&amp;category='.text_out($edit->category).'">'.text_out($edit->category).'</a></td>
						 <td class="boxen">'.$status.'</td>
						 <td class="boxen">'.$edit->counter.'</td>
						 </tr>';
		}
	
		print '</table>';
	}
	print mysql_error().$footer;	
	exit();
}

if(isset($_REQUEST['viewfile']) )
{
	if(isset($_REQUEST['id']) )
	{
		$username = $_SESSION['identity'];
		$id = $_REQUEST['id'];
		$q_file = mysql_query("SELECT id, filename, filename2, description, UNIX_TIMESTAMP(date) as date, owner, public, category, counter FROM $table_uploads WHERE owner = '$username' AND id = '$id'");
		
			while($file = mysql_fetch_object($q_file) )
			{
				$upload_path = $base_upload_path."/".$file->owner."/";
				$thumbnail_path = $base_upload_path."/".$file->owner."/thumbnails/";
			
				$handle = popen("/usr/bin/file -b '".addslashes($upload_path.$file->filename2)."'", "r");
				$filetype = fread($handle, 100);
				pclose($handle);			
			
				print $header;
				print '<div class="title">Viewing a file</div>
					<table class="boxen">
					<tr>
					<td align="right"><b>Options:</b></td>
					<td><a href="'.$upload_path.$file->filename2.'">Download</a> / <a href="uploads.php?edit=1&amp;id='.$file->id.'">Edit details</a> / <a href="uploads.php?remove=1&amp;id='.$file->id.'">Remove file</a></td>
					</tr>
					<tr>
					<td align="right"><b>Name:</b></td>
					<td>'.text_out($file->filename2).'</td>
					</tr>
					<tr>
					<tr>
					<td align="right"><b>Category:</b></td>
					<td>'.text_out($file->category).'</td>
					</tr>
					<tr>					
					<td align="right"><b>Size:</b></td>
					<td>'.round(filesize($upload_path.$file->filename2)/1024, 1).' KB</td>
					</tr>
					<tr>
					<td align="right"><b>Date:</b></td>
					<td>'.date($date_format_three,filectime($upload_path.$file->filename2)).'</td>
					</tr>
					<tr>
					<td align="right" valign="top"><b>Type:</b></td>
					<td>'.text_out($filetype).'<div class="small"><i style="color:red;">*</i>( These readings might not be accurate )</div></td>
					</tr>';
					
					if(strstr($filetype, "image") )
					{
						print '
							<tr>
							<td align="right" valign="top"><b>Description:</b></td>
							<td>'.text_out($file->description).'</td>
							</tr>						
							<tr>
							<td align="right" valign="top"><b>Preview:</b></td>
							<td>Click to view full sized image<div class="small"><i style="color:red;">*</i>( There might not be an preview here if your browser doesn\'t support the filetype )</div><br /><br /><a href="viewer.php?filename='.$file->owner."/".text_out($file->filename).'"><img src="'.$thumbnail_path.$file->filename.'" alt="" /></a></td>
							</tr>
					</table>';
					}
					else
					{
						print '
					<tr>
					<td align="right" valign="top"><b>Description:</b></td>
					<td>'.text_out($file->description).'</td>
					</tr>
					</table>';
					}
					
				print $footer;
			}
	}
	else
	{
		print $header;
		print '<i>>>> Invalid ID</i>';
		print $footer;
	}
	exit();
}

if(isset($_REQUEST['edit']) )
{
	if(isset($_REQUEST['id']) )
	{
		$username = $_SESSION['identity'];
		$id = $_REQUEST['id'];
		
		$q_file = mysql_query("SELECT id, filename, filename2, description, UNIX_TIMESTAMP(date) as date, owner, public, category, counter FROM $table_uploads WHERE owner = '$username' AND id = '$id'");

		print $header;
		
		while($file = mysql_fetch_object($q_file) )
		{
			$category = render_categories_menu("category",$file->category,$_SESSION['identity']);
			if($file->public == 1)
			{
				$public = 'checked';
			}
		
	print '<form name="editform" method="post" action="uploads.php?updating=1">
<div class="title">Updating file details</div>
<table class="boxen">
<tr>
	<td align="right"><b>File:</b></td>
	<td>'.text_out($file->filename2).'</td>
</tr>
<tr>
	<td align="right"><b>Category:</b></td>
	<td>'.$category.'</td>
</tr>
<tr>
	<td align="right"><b>Public:</b></td>
	<td><input type="checkbox" class="tbox" name="public" '.$public.' /></td>
</tr>
<tr>
	<td align="right"><b>Description:</b></td>
	<td><textarea name="description" cols="40" rows="10" class="tbox">'.to_raw($file->description).'</textarea></td>
</tr>
<tr>
	<td></td>
	<td><input type="submit" value="Update" class="tbox" /></td>
</tr>
</table>';
		}

		print $footer;
	}
	else
	{
		print $header;
		print '<i>>>> Invalid ID</i>';
		print $footer;
	}
	exit();
}

if(isset($_REQUEST['updating']) )
{
	if(isset($_REQUEST['id']) );
	{
		$id = $_REQUEST['id'];
		$category = text_in($_REQUEST['category']);
		$description = text_in($_REQUEST['description']);
		
		if(isset($_REQUEST['public']))
		{
			$public = 1;
		}
		else
		{
			$public = 0;
		}
		
		$q_update = "UPDATE $table_uploads SET category = '$category', public = $public, description = '$description' WHERE id = '$id'";
	
		print $header;
		
		if(mysql_query($q_update) )
		{
			print '<i>>>> File details have been updated<br />
							<blockquote><a href="index.php">Return to main</a> / <a href="uploads.php?browse=1">Continue browsing</a></blockquote></i>';
		}
		else
		{
			print '<i>>>> <b>An error occured</b>. The file details were not update</i>';
		}
		
		print $footer;
	}
	exit();
}

if(isset($_REQUEST['remove']) )
{
	if(isset($_REQUEST['id']) )
	{
		$id = $_REQUEST['id'];
		$username = $_SESSION['identity'];
		$q_file = mysql_query("SELECT filename FROM $table_uploads WHERE id = '$id'");
		
		print $header;
		while($file = mysql_fetch_object($q_file) )
		{
			print '<center>Are you sure you wish to remove the file &quot;'.$file->filename.'&quot;?<br />
							<a href="uploads.php?removing=1&amp;id='.$id.'">Yes</a> / <a href="'.$_SERVER['HTTP_REFERER'].'">No</a></center>';
		}
		print $footer;
	}
	else
	{
		print $header;
		print '<i>>>> Invalid ID</i>';
		print $footer;
	}
	exit();
}

if(isset($_REQUEST['removing']))
{
	if(isset($_REQUEST['id']) )
	{
		$id = $_REQUEST['id'];
		$username = $_SESSION['identity'];
		
		$q_file = mysql_query("SELECT id, filename FROM $table_uploads WHERE id = '$id'");
		
		print $header;
		
		while($file = mysql_fetch_object($q_file) )
		{
			$filename = $file->filename;
		}
		
		$q_remove = "DELETE FROM $table_uploads WHERE id = '$id'";
		
		if(mysql_query($q_remove) && unlink($upload_path.$filename))
		{
			if(file_exists($thumbnail_path.$filename) )
			{
				unlink($thumbnail_path.$filename);
			}
			print '<i>>>> File was removed.<br />
			<blockquote><a href="index.php">Return to main</a> / <a href="uploads.php?browse=1">Continue browsing</a></blockquote></i>';
		}
		else
		{
			print '<i>>>> <b>An error occured</b>. The file was not removed.</i>';
		}
		
		print $footer;
	}
	exit();
}

print $header;

print '<div class="title">Manage your uploads</div>
<ul>
<li><a href="uploads.php?new=1">Upload a file</a></li>
<li><a href="uploads.php?browse=1&amp;select=1">Browse uploaded files</a></li>
</ul>';

print $footer;

?>
