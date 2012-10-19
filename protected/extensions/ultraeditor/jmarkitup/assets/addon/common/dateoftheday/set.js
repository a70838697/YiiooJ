// ----------------------------------------------------------------------------
// markItUp!
// ----------------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
mySettings = {	
	markupSet:  [
		{	name:'Date of the Day', 
			className:"dateoftheday", 
			replaceWith:function(h) { 
				var date = new Date(),
					weekday = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"],
					monthname = ["January","February","March","April","May","June","July","August","September","October","November","December"],
					D = weekday[date.getDay()],
					d = date.getDate(),
					m = monthname[date.getMonth()],
					y = date.getFullYear(),
					h = date.getHours(),
					i = date.getMinutes(),
					s = date.getSeconds();
				return (D +" "+ d + " " + m + " " + y + " " + h + ":" + i + ":" + s);
			}
		}
	]
}