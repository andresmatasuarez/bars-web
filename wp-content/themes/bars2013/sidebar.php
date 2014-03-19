<?php
/**
 * The sidebar containing the secondary widget area, displays on posts and pages.
 *
 * If no active widgets in this sidebar, it will be hidden completely.
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */
 ?>

	<div id="sidebar" >
	
		<?php echo add_sidebar_widget('spot', 'Spot 2013', youtube_sidebar_widget('sidebar-youtube-video', '3KvJWUCNtUQ', '100%', '200px')); ?>
		
		<?php echo add_sidebar_widget('programme', 'Programación 2013', image_sidebar_widget('sidebar-image-crop', 'http://rojosangre.quintadimension.com/2.0/wp-content/uploads/2013/10/triptico-bars14-corregido.jpg', '300px', '150px')); ?>
		
		<?php echo add_sidebar_widget('poster', 'Afiche 2013', image_sidebar_widget('sidebar-image-crop', 'http://rojosangre.quintadimension.com/2.0/wp-content/uploads/2013/10/1209167_10151877594825132_1816521572_n.jpg', '300px', '150px')); ?>
		
		<?php echo add_sidebar_widget('party', 'Fiesta de clausura!', image_sidebar_widget('sidebar-image-crop', 'http://rojosangre.quintadimension.com/2.0/wp-content/uploads/2013/10/festival_300dpi_rgb.jpg', '300px', '150px')); ?>
		
		<div class=".clear"></div>
		
	</div><!-- #sidebar -->