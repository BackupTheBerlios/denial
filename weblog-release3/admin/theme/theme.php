<?php
//these are the links contained within the header.

if(isset($_SESSION['identity']) )
{
	$header_links = '<a href="index.php">Menu</a> | 
<a href="text.php">Add a post</a> | 
<a href="uploads.php">Upload a file</a> | 
<a href="index.php?logout=1">Logout</a> | 
<a href="../index.php">Go to site</a>';
}
else
{
	$header_links = "<div class=\"title\">Please login</div>";
}

// this adds a link to the admin section, very nice feature to have, rather than typing it out all the freaking time.
// please note that you will have to include the links using '.$header_links.' rather than <%header_links%> because
// of my inability to figure out a better way. please see below for an example.

if(isset($_SESSION['identity'])){
$header_links .= '';
}

$header = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><%site_title%></title>
<link rel="stylesheet" type="text/css" href="<%CSS_path%>styles.css" title="default" />
</head>
<body>
<div class="box">
<%links%>
</div>

<div class="box">';

$footer = '</div><div class="box"><%copyright%></div>
</body>
</html>';

$theme_news_style = '<div class="postTitle"><b><%title%></b><br />
<a href="comments.php?id=<%id%>&amp;type=1"><%comments%> Comment(s)</a><br />
Posted At: <%date%></div><br />
<div class="postBody">
<%body%>
</div>';

$theme_news_blurb_style = '<div class="postTitle"><b><%title%></b><br />
<a href="index.php?extended=1&amp;id=<%id%>">Read More</a><br />
<a href="comments.php?id=<%id%>&amp;type=1"><%comments%> Comment(s)</a><br />
Posted At: <%date%></div><br />
<div class="postBody">
<%body%>
</div>';

$theme_blogs_style = '<div class="postTitle"><b><%title%></b><br />
<a href="comments.php?id=<%id%>&amp;type=3"><%comments%> Comment(s)</a><br />
Posted At: <%date%><br />
Listening to: <%listening%><br />
Feeling: <%mood%></div>
<div class="postBody">
<%body%>
</div>';

$theme_articles_style = '<div class="postTitle"><b><%title%></b><br />
<a href="comments.php?id=<%id%>&amp;type=2"><%comments%> Comment(s)</a><br />
Posted At: <%date%></div><br />
<div class="postBody">
<%body%>
</div>';

$theme_comments_post_style = '<div class="postTitle"><b><%title%></b><br />
Posted At: <%date%></div><br />
<div class="postBody">
<%body%>
</div>';

$theme_comments_style = '<%comment%><br />
<span class="small"><a href="mailto:<%email%>"><%author%></a> @ <%date%></span><br /><br />';

$theme_comments_style_a = '<%comment%><br />
<span class="small"><a href="mailto:<%email%>"><%author%></a> @ <%date%> [ <a href="comments.php?delete=1&amp;id=<%id%>&amp;type=<%type%>">delete</a> ]</span><br /> [ <%ip%> / <%mask%> ] <br /><br />';


$theme_news_style_a = '<div class="title"><%title%></div>
<div class="small"><%author%> @ <%date%> - <a href="text.php?remove=1&amp;type=news&amp;id=<%id%>">remove item</a> <a href="text.php?type=news&amp;editing=1&amp;id=<%id%>">edit item</a></div><br />';

$theme_blogs_style_a = '<div class="title"><%title%></div>
<div class="small"><%author%> @ <%date%> - <a href="text.php?type=blogs&amp;remove=1&amp;id=<%id%>">remove item</a> <a href="text.php?type=blogs&amp;editing=1&amp;id=<%id%>">edit item</a></div><br />';

$theme_articles_style_a = '<div class="title"><%title%></div>
<div class="small"><%author%> @ <%date%> - <a href="text.php?remove=1&amp;type=articles&amp;id=<%id%>">remove item</a> <a href="text.php?type=articles&amp;editing=1&amp;id=<%id%>">edit item</a></div><br />';

$GLOBALS['xmlTitleTheme'] = '<div class="title"><%title%></div>';

$GLOBALS['xmlContentTheme'] = '<a href="<%url%>" alt="<%full%>"><%text%></a><br />';

// look at php.net/date ( date() ) for the specs of the date format.

$date_format_one = "g:i:s A l, F jS";
$date_format_two = "g:i:s A l, F jS";
$date_format_three = "g:i:s A l, F jS";

// ##################################################

$image_path = $dir_path.$theme_path."images/";
$CSS_path = $dir_path.$theme_path."stylesheets/";
$misc_path = $dir_path.$theme_path."misc/";
// NOTE : the $theme_path variable is available for use, it leads to themes/<themename>/ and the substitution variable is <%theme_path%>.

$site_title = "Denial Weblog 0.3 \"Simplicity\"";
$site_name = "<i></i>";

//$rss1 = $xmlParse->parseFile("http://jaykul.fragmetized.com/index.xml","jaykul.xml",20,5);
//$randomQuote = randomQuote($misc_path."quotes.txt");
//$nowlistening = parseNowListeningLocal ($dir_path."nowlistening.txt",10,"apparently, winamp is off. therefore, we must agree that I is not listening to any music, or that I am not online. well I'm just showing off my Now Listening plugin anyway :D","fyi, I am listening to: ");

$search = array("<%site_title%>","<%site_name%>","<%dir_path%>","<%image_path%>","<%CSS_path%>","<%copyright%>","<%theme_path%>");
$replace = array($site_title,$site_name,$dir_path,$image_path,$CSS_path,$copyright,$theme_path);

$header_links = str_replace($search,$replace,$header_links);

$search = array("<%site_title%>","<%site_name%>","<%dir_path%>","<%image_path%>","<%CSS_path%>","<%copyright%>","<%theme_path%>","<%links%>","<%quote%>");
$replace = array($site_title,$site_name,$dir_path,$image_path,$CSS_path,$copyright,$theme_path,$header_links,$randomQuote);

$theme_news_style			= str_replace($search,$replace,$theme_news_style);
$theme_news_blurb_style		= str_replace($search,$replace,$theme_news_blurb_style);
$theme_blogs_style			= str_replace($search,$replace,$theme_blogs_style);
$theme_articles_style		= str_replace($search,$replace,$theme_articles_style);
$theme_comments_post_style	= str_replace($search,$replace,$theme_comments_post_style);
$theme_comments_style		= str_replace($search,$replace,$theme_comments_style);
$theme_comments_style_a		= str_replace($search,$replace,$theme_comments_style_a);
$theme_news_style_a			= str_replace($search,$replace,$theme_news_style_a);
$theme_blogs_style_a		= str_replace($search,$replace,$theme_blogs_style_a);
$theme_articles_style_a		= str_replace($search,$replace,$theme_articles_style_a);

$header = str_replace($search,$replace,$header);
$footer = str_replace($search,$replace,$footer);

?>
