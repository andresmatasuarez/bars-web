jQuery(document).ready(function($){
	'use strict';

	// Call is open 2015
	var callNavItem = $('#header-menu .nav-menu li:nth-child(5)');
	var callNavItemLink = callNavItem.find('a');
	var callPageHref = callNavItemLink.attr('href');
	var callNavItemLabel = callNavItemLink.html();
	callNavItem.addClass('call-is-open');
	callNavItemLink.css('visibility', 'hidden');
	callNavItemLink.html('WWWWWWWW'); // Longer text so it takes more horizontal space
	callNavItem.html([
		callNavItem.html(),
		'<span class="call-is-open-label call-is-open-label-pre">Abierta la</span>',
		'<a class="call-is-open-label call-is-open-link" href="' + callPageHref + '">',
			callNavItemLabel,
		'</a>',
		'<span class="call-is-open-label call-is-open-label-post">',
			$('#current-edition-year').html(),
		'</span>'
	].join('\n'));


	// Sticky navigation menu
	$('#header-menu').stickymenu();

	// Focuspoint
	$('.focuspoint').focusPoint({
		throttleDuration: 100 //re-focus images at most once every 100ms.
	});

	// Slider
	$('#slider').slider();

	// Selections
	$('#schedule-section-filters').movieSectionFilter();
	$('.movie-post .movie-post-title').dotdotdot();

	// Home page posts image resize & cropping
	$('.posts .post-thumbnail img').wrap(function() {
		return '<div style="width:270px; height:170px;"></div>';
	});
	$('.posts .post-thumbnail img').parent().imgLiquid();

	// Single post related posts
	$('#page-single .post-related-posts .post-related-post img').wrap(function() {
		return '<div style="width:200px; height:170px;"></div>';
	});
	$('#page-single .post-related-posts .post-related-post img').parent().imgLiquid();

	// Sidebar image widget resize & cropping
	$('#sidebar .bars-widget.sidebar.image img').wrap(function() {
		return '<div style="width:300px; height:150px;"></div>';
	});
	$('#sidebar .bars-widget.sidebar.image img').parent().imgLiquid({ horizontalAlign: 'center', verticalAlign: 'center'});

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
		padding: 2,
		type: 'image',
		scrolling: 'hidden',
		helpers: { overlay: { locked: true } }
  });

	// Fancybox initialization for movie displayers.
	$('#page-selection .movie-post > a').fancybox({
		padding: 2,
		scrolling: 'hidden',
		helpers: { overlay: { locked: true } },
		beforeLoad: function(){
			jQuery.ajax({
				async: false,
				url: $(this.element).attr('link'),
				success: function(data) {
					$('#movie-container').html(data);
				}
			});
		},
		afterClose: function(){
			$('#movie-container').empty();
		}
	});

	// Sort movies by hour asc.
	$('.schedule-day .movie-posts').each(function(){
		var movies = $(this).find('.movie-post');
		movies.sort(function(x, y){
			var hourX = $(x).find('.movie-post-hour').html().split(':');
			var hourY = $(y).find('.movie-post-hour').html().split(':');

			var dateX = new Date();
			dateX.setHours(parseInt(hourX[0], 10));
			dateX.setMinutes(parseInt(hourX[1], 10));

			var dateY = new Date();
			dateY.setHours(parseInt(hourY[0], 10));
			dateY.setMinutes(parseInt(hourY[1], 10));

			x = dateX.getTime();
			y = dateY.getTime();

			return ((x < y) ? -1 : ((x > y) ?  1 : 0));
		});

		$(this).find('.movie-post').remove();

		$(this).append(movies);

	});

});

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
	};

	// Initialize movie section filter.
	$.fn.movieSectionFilter = function (){
		var object = this;

		// Default filter: all sections.
		object.val('all');

		object.change(function(){
			var selected = object.val();

			// First of all, hide all schedule days and movie posts.
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

	};

}(jQuery));
