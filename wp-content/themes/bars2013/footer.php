<?php
/**
 * The template for displaying the footer.
 *
 * Contains footer content and the closing of the
 * #main and #page div elements.
 *
 * @package WordPress
 * @subpackage bars2013
 */
?>



		<div class="clear"></div>
		
		<div id="footer">
			<div class="scratch"></div>
			<div id="footer-container">
				<div class="footer-nav">
					<?php get_search_form(); ?>
				</div>
				<div class="footer-logos">
					<div class="bars-image"></div>
					<a href="mailto:amatasuarez@gmail.com">
						<div class="fs-image"></div>
					</a>
				</div>
			</div>
		</div>
		
		
	<?php wp_footer(); ?>
</body>
</html>