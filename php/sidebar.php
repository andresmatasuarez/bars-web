<?php
/**
 *
 * @package WordPress
 * @subpackage bars2013
 */

	require_once 'editions.php';

	// $edition = Editions::current();
	$edition = Editions::getByNumber(Editions::current()['number'] - 1);

 ?>

	</td><!-- #content -->

	<td id="sidebar-container">
		<div id="sidebar" >

			<?php
				$barsStoreImgPath = get_bloginfo('template_directory') . '/resources/tienda-bars.jpg';
				echo widgetify('sidebar relative image bars-store', null, '
					<a class="sidebar" target="_blank" rel="noopener noreferrer" href="https://festivalrojosangre.empretienda.com.ar/">
						<img src="' . $barsStoreImgPath . '" />
					</a>
				');
			?>

			<?php
				if (!empty($edition['programme'])){
					echo add_image_sidebar_widget('sidebar relative image', 'Programación ' . Editions::getTitle($edition), get_bloginfo('template_directory') . '/' . $edition['programme']);
				}
			?>

			<?php
				if (!empty($edition['programme_gs'])){
					echo add_image_sidebar_widget('sidebar relative image', 'Programación ByN', get_bloginfo('template_directory') . '/' . $edition['programme_gs']);
				}
			?>

			<?php
				if (!empty($edition['programme_yt'])){
					echo add_image_sidebar_widget('sidebar relative image', 'Actividades vía YouTube', get_bloginfo('template_directory') . '/' . $edition['programme_yt']);
				}
			?>

			<?php
				if (!empty($edition['catalogue'])){
					$barsCatalogueUrl = get_bloginfo('template_directory') . '/' . $edition['catalogue'];
					$barsStoreImgPath = get_bloginfo('template_directory') . '/resources/bars2020/bars2020_catalogue_thumbnail.jpeg';
					echo widgetify('sidebar relative image bars-store', null, '
						<a class="sidebar" target="_blank" rel="noopener noreferrer" href="' . $barsCatalogueUrl . '">
							<img src="' . $barsStoreImgPath . '" />
						</a>
					');
				}
			?>

			<?php
				if (!empty($edition['spot'])){
					$vars = array();
					parse_str(parse_url($edition['spot'], PHP_URL_QUERY), $vars);
					echo add_youtube_sidebar_widget('sidebar relative video', 'Spot ' . Editions::getTitle($edition), $vars['v'], '100%', '200px');
				}
			?>

			<?php
				if (!empty($edition['poster'])){
					echo add_image_sidebar_widget('sidebar relative image', 'Afiche ' . Editions::getTitle($edition), get_bloginfo('template_directory') . '/' . $edition['poster']);
				}
			?>

			<?php
				if (!empty($edition['closing_party'])){
					echo add_image_sidebar_widget('sidebar relative image', 'Fiesta de clausura', get_bloginfo('template_directory') . '/' . $edition['closing_party']);
				}
			?>

			<div class="bars-widget facebook" >
				<div class="fb-page" data-href="https://www.facebook.com/BuenosAiresRojoSangre" data-tabs="timeline" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><div class="fb-xfbml-parse-ignore"><blockquote cite="https://www.facebook.com/BuenosAiresRojoSangre"><a href="https://www.facebook.com/BuenosAiresRojoSangre">Buenos Aires Rojo Sangre film festival</a></blockquote></div></div>
			</div>


		</div>
	</td>
