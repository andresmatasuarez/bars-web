<?php
/**
 * 
 * @package WordPress
 * @subpackage bars2013
 */
?>

				</div><!-- #main-container -->
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
					<div class="bars-image"></div>
					<a href="mailto:amatasuarez@gmail.com">
						<div class="fs-image"></div>
					</a>
				</div>
			</div>
		</div>
		
		
	<?php wp_footer(); ?>
	
	<!-- FACEBOOK SOCIAL PLUGIN -->
	<div id="fb-root"></div>
	<script>(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/es_LA/all.js#xfbml=1";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>

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