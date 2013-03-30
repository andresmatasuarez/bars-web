<form id="searchform" method="get" action="<?php bloginfo('siteurl')?>/">
	<input type="text" name="s" id="s" class="textbox" value="<?php echo wp_specialchars($s, 1); ?>" />
	<input id="btnSearch" type="submit" name="submit" value="<?php _e('Go'); ?>" />
</form>