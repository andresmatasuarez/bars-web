

(function ($) {

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
			$(this).find('img').wrap(function() {
				return '<div style="left: ' + left + 'px; display:block; width:' + w + 'px; height:' + h + 'px;"></div>';
			});
			$(this).find('img').parent().imgLiquid();
			left += w;
		});
	};
	
}(jQuery));