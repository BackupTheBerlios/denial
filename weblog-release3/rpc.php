<?php
require( "functions/xmlrpc.php" );
require( "config.php" );
require( "functions/db.php" );

function authenticate( $username, $password )
{
	global $table_admins;
	
	$password = md5( $password );
	
	$q_user = mysql_query("SELECT username, password FROM $table_admins WHERE username = '$username' AND password = '$password'");
	
	if( mysql_num_rows( $q_user ) >= 0 )
	{
		if( $user = mysql_fetch_array( $q_user ) )
		{
			if( $username == $user['username'] )
			{
				return TRUE;
			}
		}
	}
}

function getUsersBlogs( $args )
{
	global $table_blog_owners;

	$q_blogs = mysql_query( "SELECT id, blog FROM $table_blog_owners WHERE owner = '".$args[1]."'" );
	$counter = 0;
	$blogs = array();
	
	while( $blog = mysql_fetch_object( $q_blogs ) )
	{
		$blogs[$counter] = array( 'blogName' => $blog->blog, 'blogid' => $blog->id);
		$counter++;
	}
	
	if( authenticate( $args[1], $args[2] ) == TRUE )
	{
		return $blogs;
	}
	else
	{
		return new IXR_Error(-1, 'You did not provide the correct username and password');
	}
}

function newPost( $args )
{
	global $table_blogs;

	$blogid = $args[0];
	$username = $args[1];
	$password = $args[2];
	$content = addslashes($args[3]);
	$publish = $args[4];
	
	if( authenticate( $username, $password ) == TRUE )
	{
		$blog_name = addslashes($username."'s blog");
		preg_match('/<title>(.*?)<\/title>/i', $title);
		$title = $title[0];
		$body = str_replace($title, "", $content);
		$category = "XML";
		$mood  = "XML";
		$listening = "XML";
		$open = 0;
		
		$q_newpost = mysql_query("INSERT INTO $table_blogs VALUES( '', '$username', '$title', '$body', '$category', '$mood', '$listening', NOW(), '$blog_name', $open)");
		
		$log_file = fopen("log.txt", w);
		
		$f_content = $content[title];
		
		fwrite($log_file, $f_content);
		
		if( $q_newpost )
		{
			$status = 344324234;
		}
		else
		{
			$status = new IXR_Error(-1, 'An error occured:'.mysql_error());
		}			
	}
	else
	{
		$status = new IXR_Error(-1, 'You did not provide the correct username and password');
	}
	
	return $status;

}
	
	
$server = new IXR_Server( array(	'blogger.getUsersBlogs' => 'getUsersBlogs',
																	'metaWeblog.newPost' => 'newPost' ) );

?>
