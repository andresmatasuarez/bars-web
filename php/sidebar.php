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
				if (isset($edition['spot']) && !empty($edition['spot'])){
					$vars = array();
					parse_str(parse_url($edition['spot'], PHP_URL_QUERY), $vars);
					echo add_youtube_sidebar_widget('sidebar relative video', 'Spot ' . $edition['title'], $vars['v'], '100%', '200px');
				}
			?>

			<?php
				if (isset($edition['programme']) && !empty($edition['programme'])){
					echo add_image_sidebar_widget('sidebar relative image', 'Programación ' . $edition['title'], get_bloginfo('template_directory') . '/' . $edition['programme']);
				}
			?>

			<?php
				if (isset($edition['poster']) && !empty($edition['poster'])){
					echo add_image_sidebar_widget('sidebar relative image', 'Afiche ' . $edition['title'], get_bloginfo('template_directory') . '/' . $edition['poster']);
				}
			?>

			<?php
				if (isset($edition['closing_party']) && !empty($edition['closing_party'])){
					echo add_image_sidebar_widget('sidebar relative image', 'Fiesta de clausura', get_bloginfo('template_directory') . '/' . $edition['closing_party']);
				}
			?>

			<div class="bars-widget facebook" >
				<div class="fb-page" data-href="https://www.facebook.com/BuenosAiresRojoSangre" data-tabs="timeline" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><div class="fb-xfbml-parse-ignore"><blockquote cite="https://www.facebook.com/BuenosAiresRojoSangre"><a href="https://www.facebook.com/BuenosAiresRojoSangre">Buenos Aires Rojo Sangre film festival</a></blockquote></div></div>
			</div>


		</div>
	</td>
