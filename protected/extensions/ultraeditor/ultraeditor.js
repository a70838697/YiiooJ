(function ($) {
	$.fn.editorselection=function(editor,options){
		var selector=$(this);
		var oldval=$(this).val();

		function bindEditor()
		{
			var newval=selector.val();
			if(newval=='1'){//markitup.markdown
				$(editor).markItUp(markdownSettings,options[newval].settings);
			}
			if(newval=='2'){//markitup.wiki
				$(editor).markItUp(wikiSettings,options[newval].settings);
			}
			if(newval=='4')//html.xheditor
			{
				$(editor).xheditor(options[newval].settings);
			}
			oldval=newval;
		}
		
		bindEditor();
		
		selector.change(function(e){
			var newval=$(this).val();
			if(oldval=='1'||oldval=='2'){
				$(editor).markItUpRemove();
			}
			if(oldval=='4')
				$(editor).xheditor(false); 
			bindEditor();
		});
	}
})(jQuery);	