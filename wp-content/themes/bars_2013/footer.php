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
			<?php get_sidebar( 'main' ); ?>

			<div class="site-info">
				<?php do_action( 'twentythirteen_credits' ); ?>
				<div id="footer-image"></div>
				<div id="footer-bottom-text">www.FESTIVALROJOSANGRE.com.ar</div>
				<div id="footer-text">Festival internacional de cine de terror, fantástico y bizarro</div>
				<div id="signature">cthulhu fhtagn!</div>
			</div><!-- .site-info -->
		</footer><!-- #colophon -->
	</div><!-- #page -->

	<?php wp_footer(); ?>
</body>
</html>