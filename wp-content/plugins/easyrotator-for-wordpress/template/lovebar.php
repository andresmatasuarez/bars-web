<?php

/*
Copyright 2011 DWUser.com.
Email contact: support {at] dwuser.com

This file is part of EasyRotator for WordPress.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/



	// defined externally: $lovebarTitle

	// Add the ajax hide method
	$ajax_nonce = wp_create_nonce('nonce_easyrotator_lovebar_actions');	
	
?>

<div class="updated easyrotator_love_notice">
      <p class="er_titleLine" style="font-weight: bold;"><?php echo($lovebarTitle); ?></p> <!-- Hey there, EasyRotator user!  Do you love EasyRotator?  Tell others and win! -->
      <div class="er_contentHolder" style="display: none;">
      	<!-- content here --><p style="color: #999; font-style: italic;">Loading...</p>
      </div>
      <div class="er_notice_footer">
        <p><a class="er_expand_button" href="#" style="font-weight: bold;">Learn More</a> &nbsp;&nbsp;<a class="er_remind_button" href="#" title="Remind me again tomorrow">Remind me later</a> &nbsp;&nbsp;<a class="er_close_button" href="#" title="Hide this message and don't show again">No Thanks</a></p>
      </div>
</div>
<script type="text/javascript">
(function($){
	$(function(){
		
		var box = $('div.easyrotator_love_notice'),
			contentHolder = box.find('div.er_contentHolder');
			
		var dataBase = {
			action: 'easyrotator_lovebar_actions',
			security: '<?php echo($ajax_nonce); ?>'
		};
		
		// Get content
		$.post(ajaxurl, $.extend({eraction:'content'}, dataBase), function(result){  
			contentHolder.html(result);
		});
		
		// Configure buttons
		box.find('a.er_expand_button').click(function(e){
			e.preventDefault();
			var btn = $(this);
			btn.text( btn.text() == 'Collapse' ? 'Expand' : 'Collapse' );
			box.find('a.er_close_button').text('Hide and don\'t show again');
			contentHolder.slideToggle();
		});
		
		box.find('a.er_remind_button').click(function(e){
			e.preventDefault();
			$.post(ajaxurl, $.extend({eraction:'remindLater'}, dataBase), function(result){  
				if (result == '1')
					box.slideUp();
				else
					alert('Server communication error occurred.  Please try again or reload the page.');
			});
		});
		
		box.find('a.er_close_button').click(function(e){
			e.preventDefault();
			$.post(ajaxurl, $.extend({eraction:'hide'}, dataBase), function(result){  
				if (result == '1')
					box.slideUp();
				else
					alert('Server communication error occurred.  Please try again or reload the page.');
			});
		});
		
	});
})(jQuery);
</script>