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
		var type='4';
		var oldtype='';
		if(parseInt(sel)+''==sel)
		{
			type=sel;
		}
		else
		{
			type=$(sel).val();
			selector=$(sel);
		}

		function bindEditor()
		{
			if(type=='1'){//markitup.wiki
				if(oldtype=='4')
				{
					editor.val(Wiky.toWiki(oldval));
				}
				editor.markItUp(wikiSettings,options[type].settings);
				var FileUploader_uploadFile = new qq.FileUploaderBasic({'debug':false,'multiple':false,'button':jQuery("#fileUploader")[0],'action':options[type].action,'allowedExtensions':['jpg','jpeg','png','gif','txt','rar','zip','ppt','chm','pdf','doc','7z'],'sizeLimit':10485760,'minSizeLimit':10,'onComplete':function(id, fileName, responseJSON){ if (typeof(responseJSON.success)!="undefined" && responseJSON.success){insertFile(fileName,responseJSON);}},'params':{'PHPSESSID':options[type].PHPSESSID,'YII_CSRF_TOKEN':options[type].YII_CSRF_TOKEN}}); 
			}
			if(type=='2'){//markitup.markdown
				editor.markItUp(markdownSettings,options[type].settings);
				var FileUploader_uploadFile = new qq.FileUploaderBasic({'debug':false,'multiple':false,'button':jQuery("#fileUploader")[0],'action':options[type].action,'allowedExtensions':['jpg','jpeg','png','gif','txt','rar','zip','ppt','chm','pdf','doc','7z'],'sizeLimit':10485760,'minSizeLimit':10,'onComplete':function(id, fileName, responseJSON){ if (typeof(responseJSON.success)!="undefined" && responseJSON.success){insertFile(fileName,responseJSON);}},'params':{'PHPSESSID':options[type].PHPSESSID,'YII_CSRF_TOKEN':options[type].YII_CSRF_TOKEN}}); 
			}
			if(type=='4')//html.xheditor
			{
				if(oldtype=='1')
				{
					editor.val(Wiky.toHtml(editor.val()));
				}
				editor.xheditor(options[type].settings);
			}
		}

		function insertFile(fileName,responseJSON)
		{
			if(type=='2'){
				if(responseJSON.ext=="jpg"||responseJSON.ext=="jpeg"||responseJSON.ext=="png"||responseJSON.ext=="gif")
					editor.insertAtCaret('!['+fileName+']'+'('+responseJSON.url+')');
				else
					editor.insertAtCaret('!['+fileName+']'+'('+responseJSON.url+')');
			}
			if(type=='1'){
				if(responseJSON.ext=="jpg"||responseJSON.ext=="jpeg"||responseJSON.ext=="png"||responseJSON.ext=="gif")
					editor.insertAtCaret('{{'+fileName+'|'+responseJSON.url+'}}');
				else
					editor.insertAtCaret('[['+fileName+'|'+responseJSON.url+']]');
			}			
		}
		
		bindEditor();
		
		if(selector!=null)
		selector.change(function(e){
			oldtype=type;
			if(type=='1'||type=='2'){
				editor.markItUpRemove();
			}
			if(type=='4')
			{
				editor.xheditor(false); 
				oldval=editor.val();
			}
			type=$(this).val();
			bindEditor();
		});
	}
})(jQuery);	