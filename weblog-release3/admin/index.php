<?php

$admin_area = TRUE;
$dir_path = "../";
require("../config.php");
require("../functions/db.php");
require("../functions/authentication.php");
$table = $mysql_prefix."category";

print $header;

print '<div class="title">Manage your website</div>
<ul>
<li><a href="text.php">Manage your blog, &amp; articles</a></li>
<li><a href="category.php">Manage categories</a></li>
<li><a href="links.php">Manage your links</a></li>
<li><a href="uploads.php">Manage your uploads</a></li>
<li><a href="account.php">Update your details &amp; settings</a></li>';
if($_SESSION['rank'] == 0){
print '<li><a href="admin.php">Manage users, &amp; their content</a></li>
<li><a href="permissions.php">Manage blog permissions</a></li>';
}
print '<li><a href="./?logout=1">logout</a></li>
</ul>';

print $footer;

?>
