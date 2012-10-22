(function ($) {
	jQuery.fn.extend({
		insertAtCaret : function(myValue) {
			return this.each(function(i) {
				if (document.selection) {
					this.focus();
					sel = document.selection.createRange();
					sel.text = myValue;
					this.focus();
				} else if (this.selectionStart || this.selectionStart == '0') {
					var startPos = this.selectionStart;
					var endPos = this.selectionEnd;
					var scrollTop = this.scrollTop;
					this.value = this.value.substring(0, startPos) + myValue
							+ this.value.substring(endPos, this.value.length);
					this.focus();
					this.selectionStart = startPos + myValue.length;
					this.selectionEnd = startPos + myValue.length;
					this.scrollTop = scrollTop;
				} else {
					this.value += myValue;
					this.focus();
				}
			})
		}
	});	
	$.fn.editorselection=function(sel,options){
		var editor=$(this);
		var selector=null;
		var val='4';
		if(parseInt(sel)+''==sel)
		{
			val=sel;
		}
		else
		{
			val=$(sel).val();
			selector=$(sel);
		}

		function bindEditor()
		{
			if(val=='1'){//markitup.wiki
				editor.markItUp(wikiSettings,options[val].settings);
				var FileUploader_uploadFile = new qq.FileUploaderBasic({'debug':false,'multiple':false,'button':jQuery("#fileUploader")[0],'action':options[val].action,'allowedExtensions':['jpg','jpeg','png','gif','txt','rar','zip','ppt','chm','pdf','doc','7z'],'sizeLimit':10485760,'minSizeLimit':10,'onComplete':function(id, fileName, responseJSON){ if (typeof(responseJSON.success)!="undefined" && responseJSON.success){insertFile(fileName,responseJSON);}},'params':{'PHPSESSID':options[val].PHPSESSID,'YII_CSRF_TOKEN':options[val].YII_CSRF_TOKEN}}); 
			}
			if(val=='2'){//markitup.markdown
				editor.markItUp(markdownSettings,options[val].settings);
				var FileUploader_uploadFile = new qq.FileUploaderBasic({'debug':false,'multiple':false,'button':jQuery("#fileUploader")[0],'action':options[val].action,'allowedExtensions':['jpg','jpeg','png','gif','txt','rar','zip','ppt','chm','pdf','doc','7z'],'sizeLimit':10485760,'minSizeLimit':10,'onComplete':function(id, fileName, responseJSON){ if (typeof(responseJSON.success)!="undefined" && responseJSON.success){insertFile(fileName,responseJSON);}},'params':{'PHPSESSID':options[val].PHPSESSID,'YII_CSRF_TOKEN':options[val].YII_CSRF_TOKEN}}); 
			}
			if(val=='4')//html.xheditor
			{
				editor.xheditor(options[val].settings);
			}
		}

		function insertFile(fileName,responseJSON)
		{
			if(val=='2'){
				if(responseJSON.ext=="jpg"||responseJSON.ext=="jpeg"||responseJSON.ext=="png"||responseJSON.ext=="gif")
					editor.insertAtCaret('!['+fileName+']'+'('+responseJSON.url+')');
				else
					editor.insertAtCaret('!['+fileName+']'+'('+responseJSON.url+')');
			}
			if(val=='1'){
				if(responseJSON.ext=="jpg"||responseJSON.ext=="jpeg"||responseJSON.ext=="png"||responseJSON.ext=="gif")
					editor.insertAtCaret('{{'+fileName+'|'+responseJSON.url+'}}');
				else
					editor.insertAtCaret('[['+fileName+'|'+responseJSON.url+']]');
			}			
		}
		
		bindEditor();
		
		if(selector!=null)
		selector.change(function(e){
			if(val=='1'||val=='2'){
				editor.markItUpRemove();
			}
			if(val=='4')
				editor.xheditor(false); 
			val=$(this).val();
			bindEditor();
		});
	}
})(jQuery);	