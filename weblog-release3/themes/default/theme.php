<?php

/*
	this file defines your theme
															*/
// paths

$image_path = $dir_path.$theme_path.$theme_path."images/";
$css_path = $dir_path.$theme_path.$theme_path."stylesheets/";
$misc_path = $dir_path.$theme_path.$theme_path."misc/";

$header_links = file_get_contents($dir_path.$theme_path."templates/header_links.html");
//$header_links_unauth = file_get_contents($dir_path.$theme_path."templates/header_links_unauth.html");
$admin_links = file_get_contents($dir_path.$theme_path."templates/admin_links.html");
$copyright = file_get_contents($dir_path.$theme_path."templates/copyright.html");

$blog_style = file_get_contents($dir_path.$theme_path."templates/blog_style.html");
$blog_style_no_comments = file_get_contents($dir_path.$theme_path."templates/blog_style_no_comments.html");
$article_style = file_get_contents($dir_path.$theme_path."templates/article_style.html");
$article_style_no_comments = file_get_contents($dir_path.$theme_path."templates/article_style_no_comments.html");

$comment_blog_style = file_get_contents($dir_path.$theme_path."templates/comment_blog_style.html");
$comment_article_style = file_get_contents($dir_path.$theme_path."templates/comment_article_style.html");

$articles_list_style = file_get_contents($dir_path.$theme_path."templates/articles_list_style.html");
$articles_list_page_style = file_get_contents($dir_path.$theme_path."templates/articles_list_page_style.html");
$articles_category_style = file_get_contents($dir_path.$theme_path."templates/articles_category_style.html");
$articles_categories_style = file_get_contents($dir_path.$theme_path."templates/articles_categories_style.html");

$downloads_style = file_get_contents($dir_path.$theme_path."templates/downloads_style.html");

$downloads_list_style = file_get_contents($dir_path.$theme_path."templates/downloads_list_style.html");
$downloads_list_page_style = file_get_contents($dir_path.$theme_path."templates/downloads_list_page_style.html");
$downloads_category_style = file_get_contents($dir_path.$theme_path."templates/downloads_category_style.html");
$downloads_categories_style = file_get_contents($dir_path.$theme_path."templates/downloads_categories_style.html");

$links_list_style = file_get_contents($dir_path.$theme_path."templates/links_list_style.html");
$links_list_page_style = file_get_contents($dir_path.$theme_path."templates/links_list_page_style.html");
$links_category_style = file_get_contents($dir_path.$theme_path."templates/links_category_style.html");
$links_categories_style = file_get_contents($dir_path.$theme_path."templates/links_categories_style.html");

$blog_comments_style = file_get_contents($dir_path.$theme_path."templates/blog_comments_style.html");
$blog_comments_style_a = file_get_contents($dir_path.$theme_path."templates/blog_comments_style_a.html");
$article_comments_style = file_get_contents($dir_path.$theme_path."templates/article_comments_style.html");
$article_comments_style_a = file_get_contents($dir_path.$theme_path."templates/article_comments_style_a.html");

// dates

//$header = file_get_contents("templates/header.html");
//$footer = file_get_contents("templates/footer.html");

require("processor.php");
//require("plugins.php");
require("preferences.php");

?>
