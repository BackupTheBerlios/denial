<?php

function render_page($template)
{
	global $dir_path, $theme_path, $header_links, $admin_links, $copyright, $start, $limit, $id, $type, $category;
	$template = file_get_contents($dir_path.$theme_path."templates/".$template);
	
	if(!isset($_SESSION['identity']) )
	{
		$admin_links = NULL;
	}
	
	if(@$type == "blog")
	{
		$item = render_single_blog($id);
	}
	else
	{
		$item = render_single_article($id);
	}
	
	$search = array('<$css_path$>',
									'<$image_path$>',
									'<$misc_path$>',
									'<$header_links$>',
									'<$admin_links$>',
									'<$blogs$>',
									'<$copyright$>',
									'<$item$>',
									'<$comments$>',
									'<$downloads$>',
									'<$links$>',
									'<$articles$>',
									'<$id$>',
									'<$type$>');
	
	$replace = array($dir_path.$theme_path."stylesheets/",
										$dir_path.$theme_path."images/",
										$dir_path.$theme_path."misc/",
										$header_links,
										$admin_links,
										render_many_blogs($start, $limit, $blog),
										$copyright,
										$item,
										render_comments($id, $type),
										render_downloads($id, $category),
										render_links($id, $category),
										render_articles($id, $category),
										$id,
										$type);	
										
	$template = str_replace($search,$replace,$template);
	
	print $template;

}

function render_many_blogs($start, $limit, $blog)
{
	global $blog_style, $blog_style_no_comments, $table_blogs, $table_comments, $blog_day, $blog_date, $blog_time;
	
	$search = array('<$blog_day$>',
									'<$blog_date$>',
									'<$blog_time$>',
									'<$blog_title$>',
									'<$blog_id$>',
									'<$blog_text$>',
									'<$blog_comments$>',
									'<$blog_author$>',
									'<$blog_category$>',
									'<$blog_mood$>',
									'<$blog_song$>');

	$query = mysql_query(" SELECT id , author , title , body , mood , listening , category , UNIX_TIMESTAMP(date) as date, open FROM $table_blogs ORDER BY id DESC LIMIT $start, $limit");
	$content = NULL;

	while($blog = mysql_fetch_array($query, MYSQL_ASSOC)){

		$id = $blog['id'];
		$comments = mysql_query("SELECT id FROM $table_comments WHERE p_id = '$id' AND type = 'blog'");
		$comments = mysql_num_rows($comments);

		if($blog['open'] == 0)
		{
			$style = $blog_style_no_comments;
		}
		else
		{			$style = $blog_style;
		}
		
		$replace = array(date($blog_day,$blog['date']),
										date($blog_date,$blog['date']),
										date($blog_time,$blog['date']),
										text_out($blog['title']),
										$blog['id'],
										text_out($blog['body']),
										$comments,
										text_out($blog['author']),
										text_out($blog['category']),
										text_out($blog['mood']),
										text_out($blog['listening']));
												
		$content .= str_replace($search,$replace,$style);
		}

		if(mysql_num_rows($query) == 0){
			$content = 'No entries yet';
		}
	return $content;
}

function render_single_blog($id)
{
	global $comment_blog_style, $table_blogs, $table_comments, $blog_day, $blog_date, $blog_time;
	
	$search = array('<$blog_day$>',
									'<$blog_date$>',
									'<$blog_time$>',
									'<$blog_title$>',
									'<$blog_id$>',
									'<$blog_text$>',
									'<$blog_comments$>',
									'<$blog_author$>',
									'<$blog_category$>',
									'<$blog_mood$>',
									'<$blog_song$>');

	$query = mysql_query(" SELECT id , author , title , body , mood , listening , category , UNIX_TIMESTAMP(date) as date, open FROM $table_blogs WHERE id = '$id'");
	$content = NULL;

	while($blog = mysql_fetch_array($query, MYSQL_ASSOC)){

		$id = $blog['id'];
		$comments = mysql_query("SELECT id FROM $table_comments WHERE p_id = '$id' AND type = 'blog'");
		$comments = mysql_num_rows($comments);
				$replace = array(date($blog_day,$blog['date']),
										date($blog_date,$blog['date']),
										date($blog_time,$blog['date']),
										text_out($blog['title']),
										$blog['id'],
										text_out($blog['body']),
										$comments,
										text_out($blog['author']),
										text_out($blog['category']),
										text_out($blog['mood']),
										text_out($blog['listening']));
												
		$content .= str_replace($search, $replace, $comment_blog_style);
		}

		if(mysql_num_rows($query) == 0){
			$content = '';
		}
	return $content;
}

function render_comments($id, $type)
{
	
	global $table_comments, $blog_comments_style, $blog_comments_style_a, $article_comments_style, $article_comments_style_a, $date_comment_format;
	
	$search = array('<$comment_id$>',
									'<$comment_name$>',
									'<$comment_url$>',
									'<$comment_text$>',
									'<$comment_date$>',
									'<$comment_ip$>',
									'<$comment_mask$>');

	if(isset($_SESSION['identity']) )
	{
		$content = NULL;
		$q_comments = mysql_query("SELECT id, author, url, comment, UNIX_TIMESTAMP(date) as date, type, ip, mask FROM $table_comments WHERE p_id = '$id' AND type = '$type'");
		
		while($comment = mysql_fetch_array($q_comments, MYSQL_ASSOC) )
		{
			$replace = array($comment['id'],
												text_out($comment['author']),
												text_out($comment['url']),
												text_out($comment['comment']),
												date($date_comment_format,$comment['date']),
												$comment['ip'],
												text_out($comment['mask']));
			if($type == "blog")
			{
				$content .= str_replace($search, $replace, $blog_comments_style_a);
			}
			elseif($type == "article")
			{
				$content .= str_replace($search, $replace, $article_comments_style_a);
			}
		}
		if(mysql_num_rows($q_comments) < 1)
		{
			$content = "There are no comments for this item yet";
		}
	}
	else
	{
		$content = NULL;
		$q_comments = mysql_query("SELECT id, author, url, comment, UNIX_TIMESTAMP(date) as date, type, ip, mask FROM $table_comments WHERE p_id = '$id' AND type = '$type'");
		
		while($comment = mysql_fetch_array($q_comments, MYSQL_ASSOC) )
		{
			$replace = array($comment['id'],
												text_out($comment['author']),
												text_out($comment['url']),
												text_out($comment['comment']),
												date($date_comment_format,$comment['date']),
												$comment['ip'],
												text_out($comment['mask']));
			if($type == "blog")
			{
				$content .= str_replace($search, $replace, $blog_comments_style);
			}
			elseif($type == "article")
			{
				$content .= str_replace($search, $replace, $article_comments_style);
			}
		}
		if(mysql_num_rows($q_comments) < 1)
		{
			$content = "There are no comments for this item yet";
		}
	}
	return $content;
}

function render_articles($id, $category)
{
	global $table_articles, $table_comments, $articles_date, $article_style, $article_style_no_comments, $articles_list_style, $articles_category_style, $articles_categories_style, $articles_list_page_style, $id, $category;
	
	if($id != NULL)
	{
		$q_article = mysql_query("SELECT id, author, title, body, UNIX_TIMESTAMP(date) as date, category, open FROM $table_articles WHERE id = '$id'");
		$content = NULL;
		
		$search = array('<$article_id$>', '<$article_title$>', '<$article_text$>', '<$article_author$>', '<$article_title$>', '<$article_category$>', '<$article_date$>', '<$article_comments$>');
		
		while($article = mysql_fetch_array($q_article, MYSQL_ASSOC) )
		{
			$q_comments = mysql_query("SELECT id FROM $table_comments WHERE p_id = '".$article['id']."' AND type = 'article'");
			$comments = mysql_num_rows($q_comments);
			
			if($article['open'] == 0)
			{
				$article_style = $article_style_no_comments;
			}
			
			$replace = array($article['id'], text_out($article['title']), text_out($article['body']), text_out($article['author']), text_out($article['title']), text_out($article['category']), date($articles_date,$article['date']), $comments);
			$content .= str_replace($search, $replace, $article_style);
		}
	}
	elseif($category != NULL)
	{

		$search = array('<$article_id$>',
										'<$article_author$>',
										'<$article_title$>',
										'<$article_date$>');

		$q_articles = mysql_query("SELECT id, author, title, UNIX_TIMESTAMP(date) as date FROM $table_articles WHERE category = '$category'");
		$content = $articles_list_page_style;
		$article = NULL;
		
		while($articles = mysql_fetch_array($q_articles, MYSQL_ASSOC) )
		{
		
			$replace = array($articles['id'], $articles['author'], $articles['title'], date($articles_date, $articles['date']));
		
			$article .= str_replace($search, $replace, $articles_list_style);
		}
		
		$content = str_replace('<$articles$>', $article, $content);
		
		if(mysql_num_rows($q_articles) < 1){
			$content = "No articles have been posted yet";
		}
	}
	else
	{
		$q_cat = mysql_query("SELECT DISTINCT category FROM $table_articles");
		$content = $articles_category_style;
		$categories = NULL;
		
		while($category = mysql_fetch_array($q_cat) )
		{
			$categories .= str_replace('<$article_category$>', $category['category'] ,$articles_categories_style);
		}
		if(mysql_num_rows($q_cat) < 1){
			$content = "No articles have been posted";
		}		
	}
	
	return str_replace('<$articles_categories$>',$categories, $content);
}

function render_downloads($id, $category)
{
	global $table_uploads, $downloads_date, $downloads_style, $downloads_list_style, $downloads_category_style, $downloads_categories_style, $downloads_list_page_style, $id, $category;
	
	if($id != NULL)
	{
		$q_downloads = mysql_query("SELECT id, filename, filename2, description, UNIX_TIMESTAMP(date) as date, owner, public, category, counter FROM $table_uploads WHERE id = '$id' AND public = 1");
		$content = NULL;
		
		$search = array('<$downloads_id$>', '<$downloads_filename$>', '<$downloads_filename2$>', '<$downloads_description$>', '<$downloads_date$>', '<$downloads_owner$>', '<$downloads_category$>', '<$downloads_counter$>');
		
		while($file = mysql_fetch_array($q_downloads, MYSQL_ASSOC) )
		{
			
			$replace = array($file['id'], text_out($file['filename']), text_out($file['filename2']), text_out($file['description']), date($downloads_date, $file['date']), text_out($file['owner']), text_out($file['category']), $file['counter']);
			$content .= str_replace($search, $replace, $downloads_style);
		}
	}
	elseif($category != NULL)
	{

		$search = array('<$downloads_id$>',
										'<$downloads_owner$>',
										'<$downloads_filename$>',
										'<$downloads_date$>',
										'<$downloads_counter$>');

		$q_downloads = mysql_query("SELECT id, filename2, owner, UNIX_TIMESTAMP(date) as date, counter FROM $table_uploads WHERE category = '$category'");
		$content = $downloads_list_page_style;
		$article = NULL;
		
		while($files = mysql_fetch_array($q_downloads, MYSQL_ASSOC) )
		{
		
			$replace = array($files['id'], $files['owner'], $files['filename2'], date($downloads_date, $files['date']), $files['counter']);
		
			$file .= str_replace($search, $replace, $downloads_list_style);
		}
		
		$content = str_replace('<$downloads$>', $file, $content);
		
		if(mysql_num_rows($q_downloads) < 1){
			$content = "No files have been uploaded yet";
		}
	}
	else
	{
		$q_cat = mysql_query("SELECT DISTINCT category FROM $table_uploads");
		$content = $downloads_category_style;
		$categories = NULL;
		
		while($category = mysql_fetch_array($q_cat) )
		{
			$categories .= str_replace('<$downloads_category$>', $category['category'] ,$downloads_categories_style);
		}
		if(mysql_num_rows($q_cat) < 1){
			$content = "No files have been uploaded yet";
		}		
	}
	
	return str_replace('<$downloads_categories$>',$categories, $content);
}

function render_links($id, $category)
{
	global $table_links, $links_list_style, $links_category_style, $links_categories_style, $links_list_page_style, $id, $category;
	
	if($category != NULL)
	{

		$search = array('<$link_id$>',
										'<$link_name$>',
										'<$link_url$>',
										'<$link_category$>');

		$q_links = mysql_query("SELECT id, name, url, category FROM $table_links WHERE category = '$category'");
		$content = $links_list_page_style;
		$article = NULL;
		
		while($links = mysql_fetch_array($q_links, MYSQL_ASSOC) )
		{
		
			$replace = array($links['id'], text_out($links['name']), text_out($links['url']), text_out($links_category));
		
			$link .= str_replace($search, $replace, $links_list_style);
		}
		
		$content = str_replace('<$links$>', $link, $content);
		
		if(mysql_num_rows($q_links) < 1){
			$content = "No links have been posted yet";
		}
	}
	else
	{
		$q_cat = mysql_query("SELECT DISTINCT category FROM $table_links");
		$content = $links_category_style;
		$categories = NULL;
		
		while($category = mysql_fetch_array($q_cat) )
		{
			$categories .= str_replace('<$links_category$>', $category['category'] ,$links_categories_style);
		}
		$content = str_replace('<$links_categories$>',$categories, $content);
		if(mysql_num_rows($q_cat) < 1){
			$content = "No links have been posted yet";
		}		
	}
	
	return $content;
}

function render_single_article($id)
{
	global $table_articles, $articles_date, $comment_article_style;
	
	$q_article = mysql_query("SELECT id, author, title, body, UNIX_TIMESTAMP(date) as date, category, open FROM $table_articles WHERE id = '$id'");
	$content = NULL;
		
	$search = array('<$article_id$>', '<$article_title$>', '<$article_text$>', '<$article_author$>', '<$article_title$>', '<$article_category$>', '<$article_date$>');
		
	while($article = mysql_fetch_array($q_article, MYSQL_ASSOC) )
	{
		$replace = array($article['id'], text_out($article['title']), text_out($article['body']), text_out($article['author']), text_out($article['title']), text_out($article['category']), date($articles_date,$article['date']));
		$content .= str_replace($search, $replace, $comment_article_style);
	}
	return $content;
}

?>
