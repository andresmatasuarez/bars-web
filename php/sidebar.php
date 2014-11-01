<?php
/**
 *
 * @package WordPress
 * @subpackage bars2013
 */

	require_once 'editions.php';

	$edition = Editions::current();

 ?>

	</td><!-- #content -->

	<td id="sidebar-container">
		<div id="sidebar" >

			<?php
				if (isset($edition['spot'])) {
					$vars = array();
					parse_str(parse_url($edition['spot'], PHP_URL_QUERY), $vars);
					echo add_youtube_sidebar_widget('sidebar relative video', 'Spot ' . $edition['title'], $vars['v'], '100%', '200px');
				}
			?>

			<?php
				if (isset($edition['programme'])) {
					echo add_image_sidebar_widget('sidebar relative image', 'Programación ' . $edition['title'], get_bloginfo('template_directory') . '/' . $edition['programme']);
				}
			?>

			<?php
				if (isset($edition['poster'])) {
					echo add_image_sidebar_widget('sidebar relative image', 'Afiche ' . $edition['title'], get_bloginfo('template_directory') . '/' . $edition['poster']);
				}
			?>

			<?php
				if (isset($edition['closing_party'])) {
					echo add_image_sidebar_widget('sidebar relative image', 'Fiesta de clausura', get_bloginfo('template_directory') . '/' . $edition['closing_party']);
				}
			?>

			<div class="bars-widget facebook" >
				<iframe src="//www.facebook.com/plugins/likebox.php?href=https%3A%2F%2Fwww.facebook.com%2FBuenosAiresRojoSangre&amp;width=100&amp;height=560&amp;colorscheme=dark&amp;show_faces=true&amp;header=false&amp;stream=true&amp;show_border=false" frameborder="0" style="border:none; overflow:hidden; width:100%; height:560px;" allowtransparency="true"></iframe>
			</div>

		</div>
	</td>
