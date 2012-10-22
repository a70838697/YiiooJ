// -------------------------------------------------------------------
// markItUp!
// -------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// -------------------------------------------------------------------
// Mediawiki Wiki tags example
// -------------------------------------------------------------------
// Feel free to add more tags
// -------------------------------------------------------------------
wikiSettings = {
	previewParserPath:	'', // path to your Wiki parser
	onShiftEnter:		{keepDefault:false, replaceWith:'\n\n'},
	markupSet: [
		{name:'Heading 1', key:'1', openWith:'== ', closeWith:' ==', placeHolder:'Your title here...' },
		{name:'Heading 2', key:'2', openWith:'=== ', closeWith:' ===', placeHolder:'Your title here...' },
		{name:'Heading 3', key:'3', openWith:'==== ', closeWith:' ====', placeHolder:'Your title here...' },
		{name:'Heading 4', key:'4', openWith:'===== ', closeWith:' =====', placeHolder:'Your title here...' },
		{name:'Heading 5', key:'5', openWith:'====== ', closeWith:' ======', placeHolder:'Your title here...' },
		{separator:'---------------' },		
		{name:'Bold', key:'B', openWith:"'''", closeWith:"'''"}, 
		{name:'Italic', key:'I', openWith:"''", closeWith:"''"}, 
		{name:'Stroke through', key:'S', openWith:'<s>', closeWith:'</s>'}, 
		{separator:'---------------' },
		{name:'Bulleted list', openWith:'(!(* |!|*)!)'}, 
		{name:'Numeric list', openWith:'(!(# |!|#)!)'}, 
		{separator:'---------------' },
		{name:'Picture',className:'markItUpButton11',  key:"P", replaceWith:'[[Image:[![Url:!:http://]!]|[![name]!]]]'}, 
		//added by casper
		{name:'Upload File',div:' ' , className:'markItPictureAdd',id:'fileUploader',text:'Up'},
		// Added by CF Mitrah
		//{name:'Upload Photo',className:'markItPictureAdd', key:'M' 	},
		//{name:'Browse',className:'markItBriefcase', key:'F',beforeInsert: function(markItUp) { InlineUpload.display(markItUp,false) } 	},
		{name:'Link', className:'markItUpButton12', key:"L", openWith:"[[![Link]!] ", closeWith:']', placeHolder:'Your text to link here...' },
		{name:'Url', className:'markItUpButton13', openWith:"[[![Url:!:http://]!] ", closeWith:']', placeHolder:'Your text to link here...' },
		{separator:'---------------' },
		{name:'Quotes',className:'markItUpButton14',  openWith:'(!(> |!|>)!)', placeHolder:''},
		{name:'Code',className:'markItUpButton15',  openWith:'(!(<source lang="[![Language:!:php]!]">|!|<pre>)!)', closeWith:'(!(</source>|!|</pre>)!)'}, 
		{separator:'---------------' },
		{name:'Table generator', 
			className:'tablegenerator', 
			placeholder:"Your text here...",
			replaceWith:function(h) {
				var cols = prompt("How many cols?"),
					rows = prompt("How many rows?"),
					html = "{|\n";
				if (h.altKey) {
					for (var c = 0; c < cols; c++) {
						html += "! [![TH"+(c+1)+" text:]!]\n";	
					}	
				}
				for (var r = 0; r < rows; r++) {
					html+= "|-\n";
					for (var c = 0; c < cols; c++) {
						html += "| "+(h.placeholder||"")+"\n";	
					}
				}
				html += "|}\n";
				return html;
			}
		},		
		{name:'Preview', call:'preview', className:'preview'}
	]
}