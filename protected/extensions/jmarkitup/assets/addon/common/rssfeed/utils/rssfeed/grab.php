<?
	// ----------------------------------------------------------------------------
	// markItUp! Universal MarkUp Engine, JQuery plugin
	// Add-on Rss Feed grabber
	// Dual licensed under the MIT and GPL licenses.
	// ----------------------------------------------------------------------------
	// Copyright (C) 2008 Jay Salvat
	// http://markitup.jaysalvat.com/
	// ----------------------------------------------------------------------------
	
	include "config.php";

	function formatString($string) {
		$string = trim($string);
		if (STRIP_TAGS)		$string = strip_tags($string);
		if (STRIP_NL)		$string = ereg_replace("\n", "", $string);
		if (STRIP_SPACES)	$string = ereg_replace(" {2,}", " ", $string);
		return $string;
	}

	if (isset($_REQUEST['url'])) {
		$rssFeed = $_REQUEST['url'];
	}
	if (isset($_REQUEST['limit']) && $_REQUEST['limit'] !== '') {
		$topStories = $_REQUEST['limit'];
	}

	$xml = @simplexml_load_file($rssFeed);
	
	if (!$xml) {
		echo "MIU:ERROR";
		exit;
	}
	
	$x = 0;
	foreach($xml->channel->item as $item) {
		printf($template,
			formatString($item->title),
			formatString($item->pubDate),
			formatString($item->description),
			formatString($item->link)
		);
		$x++;
		if ($x >= $topStories) {
			break;
		}
	}
?>