<?php
/**
 *
 * @package WordPress
 * @subpackage bars2013
 */
 ?>
	
	</td><!-- #content -->

	<td id="sidebar-container">
		<div id="sidebar" >
		
			<?php echo add_youtube_sidebar_widget('sidebar relative video', 'Spot 2013', '3KvJWUCNtUQ', '100%', '200px'); ?>
			
			<?php echo add_image_sidebar_widget('sidebar relative image', 'Programación 2013', get_bloginfo('template_directory') . '/images/bars2013_programacion.jpg'); ?>
			
			<?php echo add_image_sidebar_widget('sidebar relative image', 'Afiche 2013', get_bloginfo('template_directory') . '/images/bars2013_afiche.jpg'); ?>
			
			<?php echo add_image_sidebar_widget('sidebar relative image', 'Fiesta de clausura!', get_bloginfo('template_directory') . '/images/bars2013_fiesta-clausura.jpg'); ?>
			
			<div class="bars-widget facebook" >
				<iframe src="//www.facebook.com/plugins/likebox.php?href=https%3A%2F%2Fwww.facebook.com%2FBuenosAiresRojoSangre&amp;width=100&amp;height=560&amp;colorscheme=dark&amp;show_faces=true&amp;header=false&amp;stream=true&amp;show_border=false" frameborder="0" style="border:none; overflow:hidden; width:100%; height:560px;" allowtransparency="true"></iframe>
			</div>
			
		</div>
	</td>