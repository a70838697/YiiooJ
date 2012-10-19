// ----------------------------------------------------------------------------
// markItUp!
// ----------------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
mySettings = {	
	markupSet:  [	
		{	name:'Calculator', 
			className:'calculator',
			replaceWith:function(h) { 
				try { 
					return eval(h.selection); 
				} 
				catch(e){
					alert("The selection is impossible to calculate: '"+h.selection+"'");
				} 
			}
		}
	]
}