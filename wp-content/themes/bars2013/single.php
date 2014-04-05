<?php
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
							
							<div class="post-date">
								Publicado en <?php the_date(); ?>
							</div>
							
							<div class="post-image">
								<?php the_post_thumbnail(); ?>
							</div>
							
						</div>
						
						<div class="post-content text-opensans indented">
							<?php the_content(); ?>
						</div>
						
						<div class="post-leave-us-a-comment">
							<a href="#post-comments">Tenés algo que decir? Dejanos tu comentario!</a>
						</div>
						
						<div class="post-social">
							<div class="fb-like post-fb-like" data-href="<?php get_permalink($recent["ID"]); ?>" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>
							
							<a href="http://twitter.com/share" class="post-twitter twitter-share-button" data-url="<?php the_permalink($recent["ID"]); ?>" data-via="wpbeginner" data-text="<?php $recent["post_title"]; ?>" data-count="horizontal">Tweet</a>
						</div>
						
						<div class="clear"></div>

						
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
						
						<div class="post-related-posts-header posts-header">
							Relacionadas
						</div>
						
						<div class="clear scratch"></div>

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
						
						<div class="post-comments-header posts-header">Comentarios</div>
						<div class="clear scratch"></div>
								
						<div id="post-comments" class="post-comments">
							<?php disqus_embed('buenosairesrojosangre'); ?>
						</div>
						
					</div>
					
				<?php endwhile; endif; ?>
				
				
	<?php
		get_sidebar();
		get_footer();
	?>