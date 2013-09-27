jQuery(document).ready(function($) {

	$('.bars-hidden-info').css('display', 'none');

    $('.bars-info-displayer').click(function(){
		var info = $(this).find('.bars-hidden-info');
		info.toggle(400);
		return false;
	});
	
});