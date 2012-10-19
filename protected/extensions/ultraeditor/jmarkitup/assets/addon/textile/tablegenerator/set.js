// ----------------------------------------------------------------------------
// markItUp!
// ----------------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
mySettings = {	
	markupSet:  [
	{	name:'Table generator', 
		className:'tablegenerator', 
		placeholder:"Your text here...",
		replaceWith:function(h) {
			cols = prompt("How many cols?");
			rows = prompt("How many rows?");
			html = "";
			for (r = 0; r < rows; r++) {
				for (c = 0; c < cols; c++) {
					html += "|"+(h.placeholder||"");	
				}
				html += "|\n";
			}
			return html;
		}
	}
	]
}