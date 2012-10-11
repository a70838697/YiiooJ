(function ($) {
	$.fn.formDialog=function(options){
		var link=$(this);

		link.click(function(e){
			var link2=$(this);
			e.preventDefault();
			$.ajax({
				'url': link2.attr('href'),
				'dataType': 'json',
				'success': function(data){
					var dialog=$('<div style="display:none;"><div class="forView'+options['id']+'"></div></div>');			
					$('body').append(dialog);
					dialog.dialog(options);
					dialog.find('.forView'+options['id']).html(data.view || data.form);
					
					dialog.delegate('form', 'submit', function(e){
						e.preventDefault();
						$.ajax({
							'url': link2.attr('href'),
							'type': 'post',
							'data': $(this).serialize(),
							'dataType': 'json',
							'success': function(data){
								if (data.status=='failure')
									dialog.find('.forView'+options['id']).html(data.view || data.form);
								else if (data.status=='success'){
									dialog.dialog('close').detach();
									options['onSuccess'](data, e);
								}
							}
						});

					});
				}
			});
		});
	}
})(jQuery);	