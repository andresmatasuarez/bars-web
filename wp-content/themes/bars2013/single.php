a<?php
/**
 * @package WordPress
 * @subpackage bars2013
 */

	get_header();
?>

				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

					<div id="page-single" >
					
						<div class="post-header">
							<div class="post-category">
								<?php
								if (count(get_the_category()) >= 1)
									the_category(', ');
								?>
							</div>
							
							<div class="clear scratch"></div>
							
							<div class="post-title">
								<?php the_title(); ?>
							</div>
							
							<div class="post-below-title">
								<div class="post-date">
									Publicado en <?php the_date(); ?>
								</div>
								
								<div class="post-social">
									<iframe class="post-fb-like" src="//www.facebook.com/plugins/like.php?href=<?php echo urlencode(get_permalink($recent["ID"])); ?>&amp;layout=standard&amp;show_faces=false&amp;action=like&amp;share=true" scrolling="no" frameborder="0" allowTransparency="true" ></iframe>
								</div>
							</div>
							
							<div class="post-image">
								<?php the_post_thumbnail(); ?>
							</div>
							
						</div>
						
						<div class="post-content text-opensans indented">
							<?php the_content(); ?>
						</div>
						
						<?php						
							$orig_post = $post;
							global $post;
							
							$categories = get_the_category($post->ID);
							$tags = wp_get_post_tags($post->ID);
							
							if ($categories){
								$category_ids = array();
								foreach($categories as $category)
									$category_ids[] = $category->term_id;
							}
							
							if ($tags){
								$tag_ids = array();
								foreach($tags as $tag)
									$tag_ids[] = $tag->term_id;
							}
							
							$args=array(
								'tag__in' => $tag_ids,
								'category__in' => $category_ids,
								'post__not_in' => array($post->ID),
								'posts_per_page'=> 3, // Number of related posts that will be shown.
								'caller_get_posts'=>1
							);
							
							$my_query = new wp_query( $args );
							if( $my_query->have_posts() ) {
						?>
						
						<div class="clear scratch"></div>
						
						<div class="post-related-posts-header posts-header">
							Relacionadas
						</div>

						<div class="post-related-posts">
						<?php
								while( $my_query->have_posts() ) {
									$my_query->the_post();
						?>
								<div class="post-related-post">
									<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
										<?php the_post_thumbnail(); ?>
									</a>
									<div class="post-related-post-date">
										<?php the_time('M j, Y'); ?>
									</div>
									<div class="post-related-post-title">
										<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
											<?php the_title(); ?>
										</a>
									</div>
								</div>
						<?php
								}
						?>
						
						</div>
						
						<?php
							}
							
							$post = $orig_post;
							wp_reset_query();
						?>
						
						<div class="post-navigation">
							<div class="scratch"></div>
							<div class="post-previous">
								<?php previous_post_link('%link', '%title'); ?>
							</div>
							<div class="post-next">
								<?php next_post_link('%link', '%title'); ?>
							</div>
						</div>
						
						
						<div class="clear scratch"></div>
						<div class="post-comments-header posts-header">Comentarios</div>
						<div class="clear scratch"></div>
								
						<div class="post-comments">
							<?php disqus_embed('buenosairesrojosangre'); ?>
						</div>
						
					</div>
					
				<?php endwhile; endif; ?>
				
				
	<?php
		get_sidebar();
		get_footer();
	?>