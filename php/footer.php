<?php
/**
 *
 * @package WordPress
 * @subpackage bars2013
 */
?>

				</tr>
			</table>
		</div><!-- #main-logo-bg -->
	</div><!-- #main -->

	<div class="clear"></div>

	<div id="footer" class="subtle-shadow-top">
		<div class="scratch"></div>
		<div id="footer-container">
			<div class="footer-nav">
				<?php get_search_form(); ?>
			</div>
			<div class="footer-logos">
				<div class="footer-image"></div>
				<div class="footer-festering-slime"></div>
			</div>
		</div>
	</div>

	<?php wp_footer(); ?>

	<!-- DISQUS COMMENT COUNT -->
	<script type="text/javascript">
		var disqus_shortname = 'buenosairesrojosangre';

		/* * * DON'T EDIT BELOW THIS LINE * * */
		(function () {
			var s = document.createElement('script');
			s.async = true;
			s.type = 'text/javascript';
			s.src = 'http://' + disqus_shortname + '.disqus.com/count.js';
			(document.getElementsByTagName('HEAD')[0] || document.getElementsByTagName('BODY')[0]).appendChild(s);
		}());
	</script>
</body>
</html>
