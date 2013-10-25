jQuery(document).ready(function($) {
	
	// Hide movie section selector if movie belongs to a movie block.
	$('select#_movie_movieblock').change(function(){
		if ($(this).val() == -1){
			$(this).closest('#movie_meta_box').find('#_movie_section').closest('tr').show();
		} else {
			$(this).closest('#movie_meta_box').find('#_movie_section').closest('tr').hide();
		}
	}).change();
	
});