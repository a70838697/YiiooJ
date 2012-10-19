(function ($) {
	$.fn.editorselection=function(editor,options){
		var selector=$(this);
		var oldval=$(this).val();

		
		selector.change(function(e){
			var newval=$(this).val();
			if(oldval=="markitup.wiki"||oldval=="markitup.markdown"||oldval=="markitup.html"){
				$(editor).markItUpRemove();
			}
			if(oldval=="xheditor")
				$(editor).xheditor(false); 
			if(newval=="markitup.markdown"){
				$(editor).markItUp(markdownSettings,options[newval].settings);
			}
			if(newval=="markitup.wiki"){
				$(editor).markItUp(wikiSettings,options[newval].settings);
			}
			if(newval=="xheditor")
				$(editor).xheditor(options[newval].settings); 
			oldval=newval;
		});
	}
})(jQuery);	