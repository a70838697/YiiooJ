// ----------------------------------------------------------------------------
// markItUp!
// ----------------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
mySettings = {	
	markupSet:  [	
		{	name:'Sort',	
			className:"sort", 
			replaceWith:function(h) { 
				var s = h.selection.split((($.browser.mozilla) ? "\n" : "\r\n"));
				s.sort();
				if (h.altKey) s.reverse();
				return s.join("\n");
			}
		}
	]
}