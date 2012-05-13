// requires access to jquery
quicktoctoggle = function ()
{
	body = $('#quicktoc-body').get(0);
	caption = $('#quicktoc-caption').get(0);
	if ($(body).css('display') == 'none')
	{
		$(body).css('display','block');
		$(caption).removeClass('quicktoc-closed').addClass('quicktoc-open');
	}
	else
	{
		$(body).css('display','none');
		$(caption).removeClass('quicktoc-open').addClass('quicktoc-closed');
	}
}
$(function() {
 $("#quicktoc-header").click(quicktoctoggle);
});