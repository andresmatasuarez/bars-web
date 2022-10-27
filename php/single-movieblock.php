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

<div class="movieblock" id="movieblock-<?php the_ID(); ?>">
	<div class="screenings-info text-oswald">
		<div class="section">
			<?php echo getMovieSectionLabel(get_post_meta($post->ID, '_movieblock_section', true));?>
		</div>

		<div class="screenings">
		<?php
			$screeningsValue = get_post_meta($post->ID, '_movieblock_screenings', true);
			$streamingLink = trim(get_post_meta($post->ID, '_movie_streamingLink', true));
			renderScreenings($screeningsValue, $streamingLink, $venues);
		?>
		</div>
	</div>

	<div class="info-container text-oswald">
		<div class="image">
			<?php the_post_thumbnail('movieblock-post-image'); ?>
		</div>
		<div class="info">
			<div class="title"><?php the_title(); ?></div>
			<div class="runtime"><?php echo get_post_meta($post->ID, '_movieblock_runtime', true); ?> minutos</div>
		</div>
	</div>
	<div class="clear"></div>
	<div class="scratch"></div>
	<div class="movie-selectors">
	<?php
		$query = new WP_query(array(
			'post_type' => 'movie',
			'meta_key' => '_movie_movieblock',
			'meta_value' => $post->ID,
			'posts_per_page' => -1,
			'post_status' => 'publish'
		));

		while($query->have_posts()){
			$query->next_post();

			$year = get_post_meta($query->post->ID, '_movie_year', true);
			$country = get_post_meta($query->post->ID, '_movie_country', true);
			$runtime = get_post_meta($query->post->ID, '_movie_runtime', true);
			$info = ($year != '') ? $year : '';
			if ($country != '')
				$info = ($info != '' ? $info . ' - ' : '') . $country;
			if ($runtime)
				$info = ($info != '' ? $info . ' - ' : '') . $runtime . ' min.';

			echo '<div id="movie-selector" class="movie-post" data-movie-id="' . $query->post->ID . '">';
				echo '<div class="movie-post-thumbnail">';
					echo get_the_post_thumbnail($query->post->ID, 'movie-post-thumbnail');
				echo '</div>';
				echo '<div class="movie-post-title">';
					echo '<span class="movie-post-title-text">';
						echo get_the_title($query->post->ID);
					echo '</span>';
				echo '</div>';
				echo '<div class="movie-post-info">' . $info . '</div>';
			echo '</div>';

		}

	?>
	</div>
	<div class="movie-info-displayer">
	<?php
		$query->rewind_posts();

		while($query->have_posts()){
			$query->next_post();
			echo '<div id="movie-' . $query->post->ID . '" class="movieblock-movie" data-movie-id="' . $query->post->ID . '" >';
				echo '<div class="info-container">';
					echo '<div class="image">' . get_the_post_thumbnail($query->post->ID, 'movie-post-image') . '</div>';
					echo '<div class="info">';
						echo '<div class="year country">';
							$year = get_post_meta($post->ID, '_movie_year', true);
							$country = get_post_meta($post->ID, '_movie_country', true);
							$runtime = get_post_meta($post->ID, '_movie_runtime', true);
							$info = ($year != '') ? $year : '';
							if ($country != '')
								$info = ($info != '' ? $info . ' | ' : '') . $country;

							echo $info;

						echo '</div>';
						echo '<div class="title">' . get_the_title($query->post->ID) . '</div>';
						echo '<div class="links">';
							$website = get_post_meta($query->post->ID, '_movie_website', true);
							$imdb = get_post_meta($query->post->ID, '_movie_imdb', true);

							if ($website != ''){
								echo '<a target="_blank" href="' . addHttp($website) . '">Sitio oficial</a>';
								if ($imdb != '')
									echo ' | ';
							}

							if ($imdb != '')
								echo '<a target="_blank" href="' . addHttp($imdb) . '">IMDB</a>';
						echo '</div>';

						if ($runtime != '')
							echo '<div class="runtime">' . $runtime . ' minutos.</div>';

						echo '<div class="directors">';
							$directors = array_filter(explode(',', get_post_meta($query->post->ID, '_movie_directors', true)));

							if (!empty($directors)){
								echo 'Directores: ' . trim(current($directors));

								foreach(array_slice($directors, 1) as $director){
									echo ', ' . trim($director);
								}
							}
						echo '</div>';
						echo '<div class="cast">';
							$cast = array_filter(explode(',', get_post_meta($query->post->ID, '_movie_cast', true)));

							if (!empty($cast)){
								echo 'Elenco: ' . current($cast);

								foreach(array_slice($cast, 1) as $cast){
									echo ', ' . $cast;
								}
							}
						echo '</div>';
					echo '</div>';
					echo '<div class="synopsis text-opensans indented">';
						echo '<p>' . get_post_meta($query->post->ID, '_movie_synopsis', true) . '</p>';
					echo '</div>';
				echo '</div>';

				$movie_trailer = get_post_meta($post->ID, '_movie_trailer', true);
				if (!empty($movie_trailer)){
					echo '<div class="trailer-container">' . wp_oembed_get($movie_trailer, array('width' => '300')) . '</div>';
				}
			echo '</div>';
			echo '<div class="scratch"></div>';
		}

		wp_reset_postdata();

	?>
	</div>
</div>
