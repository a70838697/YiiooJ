// ----------------------------------------------------------------------------
// markItUp!
// ----------------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
// Html tags
// http://en.wikipedia.org/wiki/html
// ----------------------------------------------------------------------------
// Basic set. Feel free to add more tags
// ----------------------------------------------------------------------------
mySettings = {    
    markupSet:  [
        {name:'RSS Feed Grabber', className:'rssFeedGrabber', replaceWith:function(markItUp) { return miu.rssFeedGrabber(markItUp) } }
	]
}
    
// mIu nameSpace to avoid conflict.
miu = {
    rssFeedGrabber: function(markItUp) {
        var feed, 
			limit = 10,
			url = prompt('Rss Feed Url', 'http://rss.news.yahoo.com/rss/topstories');
		if (url == null) {
			return false;
		}
        if (markItUp.altKey) {
            limit = prompt('Top stories', '5');
			if (limit == null) {
				return false;
			}
        }
        $.ajax({
                async:   false,
                type:    "POST",
                url:     markItUp.root+"utils/rssfeed/grab.php",
                data:    "url="+url+"&limit="+limit,
                success:function(content) {
                    feed = content;
                }
            }
        );    
        if (feed == "MIU:ERROR") {
            alert("Can't find a valid RSS Feed at "+url);
            return false;
        }
        return feed;
    }
}
