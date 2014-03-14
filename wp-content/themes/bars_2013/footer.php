<?php
/**
 * The template for displaying the footer.
 *
 * Contains footer content and the closing of the
 * #main and #page div elements.
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */
?>

		<div class="clear"></div>
		</div>
		</div><!-- #main -->
		<footer id="colophon" class="site-footer" role="contentinfo">
			<div class="footer-color-overlay">
				<?php get_sidebar( 'main' ); ?>

				<div class="site-info">
				
					<div class="scratch"></div>
					<div class="footer-container">
						<div class="footer-menu">
							<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'nav-menu' ) ); ?>
							<?php get_search_form(); ?>
						</div>
						<div class="footer-logos">
							<div class="footer-image"></div>
							<a href="mailto:amatasuarez@gmail.com"><div id="festeringslime" class="footer-mylogo"></div></a>
						</div>
					</div>
				</div><!-- .site-info -->
			</div>
		</footer><!-- #colophon -->
	</div><!-- #page -->

	<?php wp_footer(); ?>
</body>
</html>