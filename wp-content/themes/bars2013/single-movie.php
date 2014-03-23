<?php
/**
 *
 * @package WordPress
 * @subpackage bars2013
 */

?>

<div class="movie" id="movie-<?php the_ID(); ?>">
	<div class="screenings-info">
		<div class="section">
			<?php echo sectionByValue(get_post_meta($post->ID, '_movie_section', true));?>
		</div>
		
		<div class="screenings">
			<div class="screenings-caption">Mirala los días</div>
			<div class="clear"></div>
		<?php
			$datetimes = array_map('trim', explode(',', get_post_meta($post->ID, '_movie_screenings', true)));
			foreach($datetimes as $key => $datetime){
				$dt = preg_split('/[\s]+/', $datetime);
				$dayName = ucwords(getSpanishDayName(DateTime::createFromFormat('m-d-Y', $dt[0])->format('l')));
				$dayNumber = DateTime::createFromFormat('m-d-Y', $dt[0])->format('d');
				$time = $dt[1];
				echo '<div class="screening">';
					echo '<div class="screening-dayname">';
						echo $dayName;
					echo '</div>';
					echo '<div class="screening-daynumber">';
						echo $dayNumber;
					echo '</div>';
					echo '<div class="screening-hour">';
						echo $time;
					echo '</div>';
				echo '</div>';
			}
		?>
		</div>
	</div>

	<div class="info-container">
		<div class="image">
			<?php the_post_thumbnail('movie-post-image'); ?>
		</div>
		<div class="info">
			<div class="links">
				<?php
					$website = get_post_meta($post->ID, '_movie_website', true);
					$imdb = get_post_meta($post->ID, '_movie_imdb', true);
					
					if ($website != '')
						echo '<a target="_blank" href="' . $website . '">Sitio oficial</a><span> </span>';
					
					if ($imdb != '')
						echo '<a target="_blank" href="' . $imdb . '">IMDB</a>';
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
	<div class="overflow-hidden">
		<div class="trailer-container">
			<iframe src="//www.youtube.com/embed/<?php echo get_post_meta($post->ID, '_movie_trailer', true); ?>" frameborder="0" allowfullscreen></iframe>
		</div>
		<p><?php echo get_post_meta($post->ID, '_movie_synopsis', true); ?></p>
	</div>
</div>