jQuery(document).ready(function($) {
	
	// Home page. Clickable latest posts.
	$('.bars-recent-posts .latest-post').click(function(){
		window.location.href = $('.post-title a', $(this)).attr('href');
		return false;
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
		});
	
});

function barsSearch_postHoverIn(thumb, title){
	thumb.css('cursor', 'pointer');
	title.css('cursor', 'pointer');
	thumb.css('border', '7px solid rgba(255,0,0,0.5)');
	thumb.css('-webkit-filter', 'grayscale(0%)');
	thumb.css('filter', 'filter: url("data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\'><filter id=\'grayscale\'><feColorMatrix type=\'matrix\' values=\'1 0 0 0 0, 0 1 0 0 0, 0 0 1 0 0, 0 0 0 1 0\'/></filter></svg>#grayscale");');
	title.css('background-color', 'rgba(201,0,0,0.5)');
}

function barsSearch_postHoverOut(thumb, title){
	thumb.css('cursor', 'default');
	title.css('cursor', 'default');
	thumb.css('border', 'border: 7px solid rgba(201,201,201, 0.2)');
	thumb.css('-webkit-filter', 'grayscale(100%)');
	thumb.css('filter', 'url("data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\'><filter id=\'grayscale\'><feColorMatrix type=\'matrix\' values=\'0.3333 0.3333 0.3333 0 0 0.3333 0.3333 0.3333 0 0 0.3333 0.3333 0.3333 0 0 0 0 0 1 0\'/></filter></svg>#grayscale")');
	title.css('background-color', 'rgba(0,0,0,0.5)');
}


/** WaitUntilExists 
 * http://stackoverflow.com/questions/5525071/how-to-wait-until-an-element-exists
 */
 (function ($) {

/**
* @function
* @property {object} jQuery plugin which runs handler function once specified element is inserted into the DOM
* @param {function} handler A function to execute at the time when the element is inserted
* @param {bool} shouldRunHandlerOnce Optional: if true, handler is unbound after its first invocation
* @example $(selector).waitUntilExists(function);
*/

$.fn.waitUntilExists = function (handler, shouldRunHandlerOnce, isChild) {
    var found       = 'found';
    var $this       = $(this.selector);
    var $elements   = $this.not(function () { return $(this).data(found); }).each(handler).data(found, true);

    if (!isChild)
    {
        (window.waitUntilExists_Intervals = window.waitUntilExists_Intervals || {})[this.selector] =
            window.setInterval(function () { $this.waitUntilExists(handler, shouldRunHandlerOnce, true); }, 500)
        ;
    }
    else if (shouldRunHandlerOnce && $elements.length)
    {
        window.clearInterval(window.waitUntilExists_Intervals[this.selector]);
    }

    return $this;
}

}(jQuery));