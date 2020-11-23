<?php
/**
 *
 * @package WordPress
 * @subpackage bars2013
 */

	require_once 'helpers.php';
	require_once 'elements.php';
	require_once 'editions.php';

	if (!isset($_GET['edition'])){
		$currentEdition = Editions::current();
	} else {
		$currentEdition = Editions::getByNumber(htmlspecialchars($_GET['edition']));
	}
	$venues = Editions::venues($currentEdition);
?>

<div class="movie" id="movie-<?php the_ID(); ?>">
	<div class="screenings-info text-oswald">
		<div class="section">
			<?php echo sectionByValue(get_post_meta($post->ID, '_movie_section', true));?>
		</div>

		<div class="screenings">
		<?php
			$screeningValue = get_post_meta($post->ID, '_movie_screenings', true);
			renderScreenings($screeningValue, $venues);
		?>
		</div>

		<?php
			$streamingLink = trim(get_post_meta($post->ID, '_movie_streamingLink', true));
			if ($streamingLink !== '') {
				renderStreamingLinkButton($streamingLink);
			}
		?>
	</div>

	<div class="info-container text-oswald">
		<div class="image">
			<?php the_post_thumbnail('movie-post-image'); ?>
		</div>
		<div class="info">
			<div class="links">
				<?php
					$website = get_post_meta($post->ID, '_movie_website', true);
					$imdb = get_post_meta($post->ID, '_movie_imdb', true);

					if ($website != '')
						echo '<a target="_blank" href="' . addHttp($website) . '">Sitio oficial</a><span> </span>';

					if ($imdb != '')
						echo '<a target="_blank" href="' . addHttp($imdb) . '">IMDB</a>';
				?>
			</div>
			<div class="title">
				<?php the_title(); ?>
			</div>

			<div class="specs">
				<?php
					$year = get_post_meta($post->ID, '_movie_year', true);
					$country = get_post_meta($post->ID, '_movie_country', true);
					$runtime = get_post_meta($post->ID, '_movie_runtime', true);
					$info = ($year != '') ? $year : '';
					if ($country != '')
						$info = ($info != '' ? $info . ' ' : '') . '(' . $country . ')';

					if ($runtime != '')
						echo $info . ' - ' . $runtime . ' min.';
				?>
			</div>
			<div class="directors">
				<?php
					$directors = explode(',', get_post_meta($post->ID, '_movie_directors', true));

					if (sizeof($directors) != 0){
						echo 'Directores: ' . trim(current($directors));

						foreach(array_slice($directors, 1) as $director){
							echo ', ' . trim($director);
						}
					}

				?>
			</div>
			<div class="cast">
				<?php
					$cast = explode(',', get_post_meta($post->ID, '_movie_cast', true));

					if (sizeof($cast) != 0){
						echo 'Elenco: ' . current($cast);

						foreach(array_slice($cast, 1) as $cast){
							echo ', ' . $cast;
						}
					}
				?>
			</div>
		</div>
	</div>
	<div class="clear"></div>
	<div class="scratch"></div>
	<div class="movie-synopsis text-opensans indented">
		<?php
			$movie_trailer = get_post_meta($post->ID, '_movie_trailer', true);
			if (!empty($movie_trailer)){
				echo '<div class="trailer-container">' . wp_oembed_get($movie_trailer, array('width' => '300')) . '</div>';
			}
		?>
		<p class="text-opensans"><?php echo get_post_meta($post->ID, '_movie_synopsis', true); ?></p>
		<br/>
		<p class="text-opensans"><?php echo get_post_meta($post->ID, '_movie_comments', true); ?></p>
	</div>
</div>
