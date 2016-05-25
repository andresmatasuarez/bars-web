<?php
/**
 *
 * @package WordPress
 * @subpackage bars2013
 */

	get_header();
?>

					<div id="page-search" class="page" >

						<div class="search-box">
							<div class="search-summary">
								<div class="label">Resultados de búsqueda para </div>
								<div class="keyword"><?php echo get_search_query(); ?></div>
							</div>
							<div class="search-form-container">
								<?php get_search_form(); ?>
							</div>
						</div>

						<?php
							if (have_posts()){
								echo '<div class="page-section-title">
												Resultados
											</div>
											<div class="posts">';
								while (have_posts()){
									the_post();
									renderPostInList(get_the_ID());
								}
								echo '</div>';
							} else {
						?>
							<div class="warning">
								<div><span class="fa fa-exclamation-circle"></span></div>
								<div class="reason">No se encontraron resultados</div>
								<div class="description">Volvé a intentar la búsqueda utilizando otras palabras.</div>
							</div>
						<?php
							}
						?>

						<?php pagination(esc_attr(get_query_var('paged') ? absint(get_query_var('paged')) : 1)); ?>

					</div>


	<?php
		get_sidebar();
		get_footer();
	?>
