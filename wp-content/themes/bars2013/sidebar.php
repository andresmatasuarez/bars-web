<?php
/**
 *
 * @package WordPress
 * @subpackage bars2013
 */
 ?>
	
	</div><!-- #content -->

	<div id="sidebar-container">
		<div id="sidebar" >
		
			<?php echo add_youtube_sidebar_widget('sidebar-youtube-video', 'Spot 2013', '3KvJWUCNtUQ', '100%', '200px'); ?>
			
			<?php echo add_image_sidebar_widget('sidebar-image', 'Programación 2013', get_bloginfo('template_directory') . '/images/bars2013/bars2013_programacion.jpg', '300px', '150px'); ?>
			
			<?php echo add_image_sidebar_widget('sidebar-image', 'Afiche 2013', get_bloginfo('template_directory') . '/images/bars2013/bars2013_afiche.jpg', '300px', '150px'); ?>
			
			<?php echo add_image_sidebar_widget('sidebar-image', 'Fiesta de clausura!', get_bloginfo('template_directory') . '/images/bars2013/bars2013_fiesta-clausura.jpg', '300px', '150px'); ?>
			
		</div>
	</div>