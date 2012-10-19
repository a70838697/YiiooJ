<?
	define('EOL', 			"\n");
	define('STRIP_TAGS', 	true);
	define('STRIP_NL', 		true);
	define('STRIP_SPACES', 	true);
	
	// Url of the Rss Feed
	$rssFeed = 'http://rss.news.yahoo.com/rss/topstories';
	
	// Top stories
	$topStories = 10;

	// Templates
	// Neutral tempalte
	$template = '%s'.EOL;
	$template.= '%s'.EOL;
	$template.= '%s'.EOL;
	$template.= 'Read more at: %s'.EOL;
	$template.= EOL;
	
	// Html template
	//	$template = '<h2>%s</h2>'.EOL;
	//	$template.= '<p><em>%s</small></em></p>'.EOL;
	//	$template.= '<p>%s</p>'.EOL;
	//	$template.= '<p><a href="%s">Read more...</a></p>'.EOL;
	//	$template.= EOL;
	
	// Textile template example
	//	$template = 'h2. %s'.EOL;
	//	$template.= '_%s_'.EOL;
	//	$template.= '%s'.EOL;
	//	$template.= '"Read more...":%s'.EOL;
	//	$template.= EOL;
	
	// Markdown template example
	//	$template = '## %s'.EOL;
	//	$template.= '_%s_'.EOL;
	//	$template.= '%s'.EOL;
	//	$template.= '[Read more...](%s)'.EOL;
	//	$template.= EOL;
?>