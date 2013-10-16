<?php
/**
 * The default template for displaying content. Used for both single and index/archive/search.
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	
	<?php if ( is_search() || is_archive() ) : // Only display Excerpts for Search ?>
		<div class="bars-post-search">
			<div class="bars-post-thumbnail">
				<?php if ( has_post_thumbnail() )
					the_post_thumbnail('search-thumbnail');
				?>
			</div>
			<div class="bars-post-excerpt">
				<div class="entry-date"><?php the_date(); ?></div>
				<div class="entry-title"><?php the_title(); ?></div>
				<!-- <div class="entry-summary"><?php the_excerpt(); ?></div> -->
				<div class="entry-categories">Categorías: <?php the_category(', '); ?></div>
				<div class="entry-tags">Tags: <?php the_tags(''); ?></div>
				<div class="clear"></div>
			</div>
			<div class="clear"></div>
		</div>
	
		<div class="scratch subtle-shadow"></div>
	
	<?php else :?>
	
		<div class="entry-header">

			<div class="entry-title">
				<div class="bars-post-meta">
					<?php if (count(get_the_category()) >= 1) : ?>
						<div class="bars-post-meta-content bars-post-category"><?php the_category(', '); ?></div>
					<?php endif; ?>
				</div>
				<div class="bars-post-title">
					<?php the_title(); ?>
				</div>
			</div>
			
			<?php if ( has_post_thumbnail() && ! post_password_required() ) : ?>
			<div class="entry-thumbnail">
				<?php the_post_thumbnail(); ?>
			</div>
			<?php endif; ?>
			
		</div><!-- .entry-header -->

		<div class="bars-single-post-date-container">
			<div class="bars-single-post-date">Publicado en <?php the_date(); ?></div>
			
			<div class="clear scratch"></div>
		</div>
		
		<div class="entry-content">
			<?php the_content(); ?>
		</div><!-- .entry-content -->
		
		<footer class="entry-meta bars-single-post-footer">
			<div class="clear scratch"></div>
			<?php if (count(get_the_tags()) >= 1) : ?>
				<div class="bars-single-post-tags"><?php the_tags(''); ?></div>
			<?php endif; ?>
			<div class="clear scratch"></div>
			<div class="bars-single-post-footer-container">
				<div class="bars-social"><?php do_action( 'addthis_widget' ); ?></div>
				<div class="bars-link-to-comments">
					<a class="dsq-comment-count" href="#disqus_thread"></a>
				</div>
			</div>
		</footer><!-- .entry-meta -->
			
		<div class="bars-post-navigation">
			<!--<div class="bars-related-posts-title">Entradas relacionadas</div>-->
			<div class="bars-related-posts">
				<?php echo get_related_posts_thumbnails(); ?>
			</div>
		</div>
		
		<div class="clear scratch"></div>
		<?php twentythirteen_post_nav(); ?>
		<div class="clear scratch"></div>
				
		<div class="bars-comments"><?php disqus_embed('buenosairesrojosangre'); ?></div>
		
	<?php endif;?>
	
</article><!-- #post -->