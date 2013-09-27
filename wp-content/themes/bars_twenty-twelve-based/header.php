<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */
?><!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<title><?php wp_title( '|', true, 'right' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php // Loads HTML5 JavaScript file to add support for HTML5 elements in older IE versions. ?>
<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
<![endif]-->
<?php wp_head(); ?>

<script type="text/javascript" src="/js/main.js"></script>

</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed site">

	<div class="scratch left"></div>
	<div class="bars-main-container">
	<header id="masthead" class="site-header" role="banner">
		
		<div id="bars-header-social">
			<div class="column">
				<div id="bars-header-social-facebook"></div>
				<!-- <div id="bars-header-social-youtube"></div> -->
			</div>
			<div class="column">
				<div id="bars-header-social-twitter"></div>
				<!-- <div id="bars-header-social-tumblr"></div> -->
				<!-- <div id="bars-header-social-flickr"></div> -->
			</div>
		</div>
		
		<div id="bars-header-logo"></div>
		
		<!-- <div id="bars-header-caption" style="margin-bottom: 20px;">
			14º festival Internacional de cine fantástico, bizarro y terrorífico
		</div>

		 <div class="horizontal-scratch top"></div> -->
		
		<nav id="site-navigation" class="main-navigation bars-header-menu" role="navigation">
			<h3 class="menu-toggle"><?php _e( 'Menu', 'twentytwelve' ); ?></h3>
			<a class="assistive-text" href="#content" title="<?php esc_attr_e( 'Skip to content', 'twentytwelve' ); ?>"><?php _e( 'Skip to content', 'twentytwelve' ); ?></a>
			<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'nav-menu' ) ); ?>
		</nav><!-- #site-navigation -->
		
		<div class="horizontal-scratch bottom"></div>

		<?php $header_image = get_header_image();
		if ( ! empty( $header_image ) ) : ?>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_url( $header_image ); ?>" class="header-image" width="<?php echo get_custom_header()->width; ?>" height="<?php echo get_custom_header()->height; ?>" alt="" /></a>
		<?php endif; ?>
	</header><!-- #masthead -->
	
	<div id="main" class="wrapper">