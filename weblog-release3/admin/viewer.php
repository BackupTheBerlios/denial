<?php

$admin_area = TRUE;
$dir_path = "../";
require("../config.php");
require("../functions/authentication.php");
print $header;
print '<img src="../uploads/'.$_REQUEST['filename'].'" />';
print $footer;
?>