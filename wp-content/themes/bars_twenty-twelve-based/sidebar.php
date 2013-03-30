<?php
/**
 * The sidebar containing the main widget area.
 *
 * If no active widgets in sidebar, let's hide it completely.
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */
?>

	<?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
		<div id="secondary" class="widget-area" role="complementary">
			<?php dynamic_sidebar( 'sidebar-1' ); ?>
			<div id="bars-widget-featured-video">
				<div id="bars-widget-featured-video-header">
					<div class="bars-widget-featured-video-header-camera">
						<div class="rounded-box"></div>
						<div class="left-arrow"></div>
					</div>
					<div id="bars-widget-featured-video-header-title">
						Featured video
					</div>
				</div>
				<div id="bars-widget-featured-video-content">
					
				</div>
			</div>
		</div>
	<?php endif; ?>