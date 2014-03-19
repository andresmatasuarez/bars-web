
jQuery(document).ready(function($) {

	// Place & show festival ribbon

	// Sticky navigation menu
	$('#header-menu').stickymenu();
	
	$('#slider').slider();
	
	// Home page. Clickable latest posts.
	$('.bars-recent-posts .latest-post').click(function(){
		window.location.href = $('.post-title a', $(this)).attr('href');
		return false;
	});
	
	// Programación
	$('#schedule-section-filters').movieSectionFilter();
	$('.movie-post .movie-post-title').ThreeDots({ text_span_class: 'movie-post-title-text', max_rows: 1, alt_text_e: true, alt_text_t: true });
	$('#movieblock .movie-selectors #movie-selector').live('click', function(){
		var id = $(this).attr('movieid');
		$('#movieblock .movie-info-displayer .movie').fadeOut(300);
		$('#movieblock .movie-info-displayer #movie-' + id).fadeIn(300);
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
	
	// Movie section filter.
	$.fn.movieSectionFilter = function (){
		var object = this;
		
		// Default filter: all sections.
		object.val('all');
		
		object.change(function(){
			var selected = object.val();
			if (selected == 'all'){
				object.closest('article').find('.schedule-day, .movie-post').fadeIn(300);
			} else {
				object.closest('article').find('.schedule-day, .movie-post').fadeOut(300);
				object.closest('article').find('.movie-post[section="' + object.val() + '"]').each(function(){
					$(this).fadeIn(300);
					$(this).closest('.schedule-day').fadeIn(300);
				});
			}
			
			object.closest('article').find('.schedule-day:visible:odd').removeClass('odd even').addClass('odd');
			object.closest('article').find('.schedule-day:visible:even').removeClass('odd even').addClass('even');
		});
		
	}

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
	
	// Recent posts slider.
	$.fn.slider = function(){
		var object = this;
		var w = object.width();
		var h = object.height();
		var slides = object.find('.slide');
		var slidesCount = slides.length;
		var interval = null;
		
		var currentSlide = object.find('.slide[slide-position=0]');
		
		var slideFunction = function(toLeft){
			var currentSlidePosition = parseInt(currentSlide.attr('slide-position'));
			var d;
			if (currentSlidePosition == 0 && toLeft)
				d = slidesCount - 1;
			else if (currentSlidePosition == slidesCount-1 && !toLeft)
				d = 0;
			else
				d = currentSlidePosition + (toLeft ? -1 : 1);
			
			slides.each(function(){
				$(this).animate({'left': ((parseInt($(this).attr('slide-position')) - d) * w) + 'px'}, 'slow', function(){
					if (interval == null){
						interval = setInterval(function(){
							slideFunction(false);
						}, 5000);
					}
				});
			});
			currentSlide = object.find('.slide[slide-position=' + d + ']');
		};
		
		// Slider controls behaviour
		object.find('.slider-control').on('click', function(el, event){
			if (interval != null){
				clearInterval(interval);
				interval = null;
			}
			slideFunction($(this).hasClass('left'));
		});
		
		// Slider interval function.
		interval = setInterval(function(){
			slideFunction(false);
		}, 5000);
		
		// Hide slider controls.
		object.find('.slider-controls').hide();
		object.hover(function(){
			object.find('.slider-controls').fadeToggle(200);
		});
		
		// Link slides to respective posts.
		slides.each(function(){
			$(this).click(function(){
				window.location = $(this).attr('permalink');
			});
		});
		
		// Place slides in a consecutive way.
		var left = 0;
		slides.each(function(){
			$(this).css('left', left + 'px');
			$(this).find('img').resizecrop({
				width: w,
				height: h
			});
			left += w;
		});
	}
	
}(jQuery));