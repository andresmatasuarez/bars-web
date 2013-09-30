<?php
/**
 * Template Name: 'Auspiciantes' template
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">

			<?php /* The loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<header class="entry-header">
						<?php if ( has_post_thumbnail() && ! post_password_required() ) : ?>
						<div class="entry-thumbnail">
							<?php the_post_thumbnail(); ?>
						</div>
						<?php endif; ?>
						<h1 class="entry-title"><?php the_title(); ?></h1>
						<div class="scratch subtle-shadow">
						</div>
					</header><!-- .entry-header -->

					<div class="entry-content">
						<div id="sponsors">
							<div class="auspiciantes auspiciantes-multiplex">
								<a href="http://www.cinesmultiplex.com.ar/" target="_blank" alt="Cines Multiplex" ></a>
							</div>
							<div class="auspiciantes auspiciantes-metamorfosisfx">
								<a href="http://www.metamorfosisfx.com.ar/" target="_blank" alt="Metamorfosis FX" ></a>
							</div>
							<div class="auspiciantes auspiciantes-quintadimension">
								<a href="http://www.quintadimension.com/" target="_blank" alt="Quinta Dimensión" ></a>
							</div>
							<div class="auspiciantes auspiciantes-findlingfilms">
								<a href="http://findlingfilms.com/" target="_blank" alt="Findling Films" ></a>
							</div>
							<div class="auspiciantes auspiciantes-videoflims">
								<a href="http://www.videoflims.com.ar/" target="_blank" alt="Video Flims" ></a>
							</div>
							<div class="auspiciantes auspiciantes-incaa">
								<a href="http://www.incaa.gov.ar/" target="_blank" alt="INCAA" ></a>
							</div>
							<div class="auspiciantes auspiciantes-fantafestivales">
								<a href="http://www.fantafestivales.org/" target="_blank" alt="Fanta Festivales" ></a>
							</div>
							<div class="auspiciantes auspiciantes-designia">
								<a href="http://www.designiaestudio.com.ar/" target="_blank" alt="Designia" ></a>
							</div>
						</div>
					</div><!-- .entry-content -->
					
				</article><!-- #post -->

			<?php endwhile; ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>