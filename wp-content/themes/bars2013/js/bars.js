jQuery(document).ready(function($) {

	// Sticky navigation menu
	$('#header-menu').stickymenu();
	
	// Slider
	$('#slider').slider();
	
	// Selections
	$('#schedule-section-filters').movieSectionFilter();
	
	// Home page posts image resize & cropping
	$('#page-home .latest-post .post-thumbnail img').resizecrop({ width: 205, height: 170 });
	$('#page-home .recent-post .post-thumbnail img').resizecrop({ width: 280, height: 170 });
	
	// Sidebar iage widget resize & cropping
	//$('#sidebar .bars-widget.sidebar.image .image-container').imgLiquid({ horizontalAlign: 'center', verticalAlign: 'center'});
	
	// Programación
	$('.movieblock .movie-selectors #movie-selector').live('click', function(){
		var id = $(this).attr('movieid');
		var scroll = 0;
		$('#movie-' + $(this).attr('movieid')).prevAll().each(function(index,elem){
			scroll += $(elem).outerHeight(true);
		});
		
		$('.movie-info-displayer').animate({
			scrollTop: scroll
		});
	});
	
	// Fancybox initialization for sidebar image widgets
	$(".fancybox.sidebar").fancybox({
		scrolling: 'no',
		type: 'image',
        padding: 2,
		beforeShow: function(){
			// Hide body overflow to simulate scroll lock.
			$(document.body).addClass('overflow-hidden');
		},
		beforeClose: function(){
			// Re-enable scrolling.
			$(document.body).removeClass('overflow-hidden');
		}
    });
	
	// Fancybox initialization for movie displayers.
	$('.movie-post .fancybox').fancybox({
		scrolling: 'no',
		padding: 2,
		beforeLoad: function(){
			$('#movie-container').load($(this.element).attr('link'));
		},
		beforeShow: function(){
			// Hide body overflow to simulate scroll lock.
			$(document.body).addClass('overflow-hidden');
		},
		beforeClose: function(){
			// Re-enable scrolling.
			$(document.body).removeClass('overflow-hidden');
		},
		afterClose: function(){
			$('#movie-container').empty();
		}
	});
	
	// Fancybox initialization for entry forms in call.php.
	$('#page-call .fancybox').fancybox({
		padding: 2,
		beforeShow: function(){
			// Hide body overflow to simulate scroll lock.
			$(document.body).addClass('overflow-hidden');
		},
		beforeClose: function(){
			// Re-enable scrolling.
			$(document.body).removeClass('overflow-hidden');
		}
	});
	
	// Home page. Clickable latest posts.
	$('.bars-recent-posts .latest-post').click(function(){
		window.location.href = $('.post-title a', $(this)).attr('href');
		return false;
	});
	
	// Sort movies by hour asc.
	$('.schedule-day .movie-posts').each(function(){
		var movies = $(this).find('.movie-post');
		movies.sort(function(x, y){
			var hourX = $(x).find('.movie-post-hour').html().split(':');
			var hourY = $(y).find('.movie-post-hour').html().split(':');
			
			var dateX = new Date();
			dateX.setHours(parseInt(hourX[0]));
			dateX.setMinutes(parseInt(hourX[1]));
			
			var dateY = new Date();
			dateY.setHours(parseInt(hourY[0]));
			dateY.setMinutes(parseInt(hourY[1]));
			
			x = dateX.getTime();
			y = dateY.getTime();	

			return ((x < y) ? -1 : ((x > y) ?  1 : 0));
		});
		
		$(this).find('.movie-post').remove();
		
		$(this).append(movies);
		
	});
	
	
	// Convocatoria
	$('.bars-hidden-info').css('display', 'none');

    $('.bars-info-displayer-header').click(function(e){
		var info = $(this).parent().find('.bars-hidden-info');
		info.slideToggle('slow');
		
		var arrow = $(this).find('.bars-info-displayer-arrow');
		if (arrow.hasClass('arrow-right')){
			arrow.removeClass('arrow-right');
			arrow.addClass('arrow-down');
		} else {
			arrow.removeClass('arrow-down');
			arrow.addClass('arrow-right');
		}
		
		return false;
	});
	
	// Search and archive
	var linkToPost = function(obj){
		var postId;
		var classes = $(this).closest('article').attr('class').split(/\s+/);
		$.each(classes, function(index, item){
			if (item.match('^post-'))
			   postId = item.replace('post-','');
		});
		window.location = '/?p=' + postId;
	};
	$('body.search .bars-post-thumbnail, body.archive .bars-post-thumbnail').click(linkToPost);
	$('body.search .entry-title, body.archive .entry-title').click(linkToPost);
	
	$('body.search .bars-post-thumbnail, body.archive .bars-post-thumbnail').hover(
		function(obj){
			barsSearch_postHoverIn($(this), $('.entry-title', $(this).closest('.bars-post-search')));
			return false;
			
		}, function(obj){
			barsSearch_postHoverOut($(this), $('.entry-title', $(this).closest('.bars-post-search')));
			return false;
		}
	);
	
	$('body.search .entry-title, body.archive .entry-title').hover(
		function(obj){
			barsSearch_postHoverIn($('.bars-post-thumbnail', $(this).closest('.bars-post-search')), $(this));
			return false;
		}, function(obj){
			barsSearch_postHoverOut($('.bars-post-thumbnail', $(this).closest('.bars-post-search')), $(this));
			return false;
		}
	);
	
});

function barsSearch_postHoverIn(thumb, title){
	thumb.css('cursor', 'pointer');
	title.css('cursor', 'pointer');
	thumb.css('border', '7px solid rgba(255,0,0,0.5)');
	title.css('background-color', 'rgba(201,0,0,0.5)');
}

function barsSearch_postHoverOut(thumb, title){
	thumb.css('cursor', 'default');
	title.css('cursor', 'default');
	thumb.css('border', 'border: 7px solid rgba(201,201,201, 0.2)');
	title.css('background-color', 'rgba(0,0,0,0.5)');
}

(function ($) {

	// Sticky navigation menu.
	$.fn.stickymenu = function (){
		var object = $(this);
		var adminBarOffset = $('#wpadminbar').length ? $('#wpadminbar').height() : 0;
		var stickyTop = object.offset().top;
		
		var initialPosition = object.css('position');
		var initialTop = object.css('top');

		$(window).scroll(function(){
			var st = $(this).scrollTop();
			var scrollTop = $(window).scrollTop(); 

			// If we scroll more than the navigation, change its position to fixed and add class 'sticky'.
			// Otherwise, change it back to absolute and remove the class.
			if (scrollTop > stickyTop) {
				object.css({ 'position': 'fixed', 'top': adminBarOffset }).addClass('sticky');	
			} else {	
				object.css({ 'position': initialPosition, 'top': initialTop }).removeClass('sticky'); 
			}
			
		});
	}
	
	// Initialize movie section filter.
	$.fn.movieSectionFilter = function (){
		var object = this;
		
		// Default filter: all sections.
		object.val('all');
		
		object.change(function(){
			var selected = object.val();
			
			// First of all, fade out all schedule days and movie posts.
			$('.schedule .schedule-day, .schedule .scratch, .schedule .schedule-day .movie-post').hide();
			
			if (selected == 'all'){
				$('.schedule .schedule-day, .schedule .scratch, .schedule .schedule-day .movie-post').show();
			} else {
				$('.schedule .schedule-day .movie-post[section="' + selected + '"]').each(function(){
					$(this).show();
					$(this).closest('.schedule .schedule-day').show();
					$(this).closest('.schedule .schedule-day').next('.schedule .scratch').show();
				});
			}
			
			$('.schedule .schedule-day:visible:odd').removeClass('odd even').addClass('odd');
			$('.schedule .schedule-day:visible:even').removeClass('odd even').addClass('even');
		});
		
	}
	
}(jQuery));