<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
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
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width">
	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<!--[if lt IE 9]>
	<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js"></script>
	<![endif]-->
	<?php wp_head(); ?>
	
	<script type="text/javascript" src="<? echo get_stylesheet_directory_uri();?>/js/jquery.resizecrop-1.0.3.min.js"></script>
	<script type="text/javascript" src="<? echo get_stylesheet_directory_uri();?>/js/jquery.ThreeDots.min.js"></script>
	<script type="text/javascript" src="<? echo get_stylesheet_directory_uri();?>/js/bars.js"></script>
</head>

<body <?php body_class(); ?>>
	<div id="page" class="hfeed site">
		<header id="masthead" class="site-header" role="banner">
			<div id="header-content">
				<div id="header-textdiv">Festival internacional de cine de terror, fantástico y bizarro</div>
				<a href="<?php echo get_option('home'); ?>"><div id="header-image"></div></a>
				<div id="header-social">
					<a href="https://www.facebook.com/BuenosAiresRojoSangre" target="blank"><div id="bars-header-social-facebook"></div></a>
					<a href="http://www.youtube.com/user/RojoSangreFestival" target="blank"><div id="bars-header-social-youtube"></div></a>
					<a href="https://twitter.com/rojosangre" target="blank"><div id="bars-header-social-twitter"></div></a>
				</div>
				<div id="header-clouds"></div>
			</div>
			<div id="festival-ribbon">
				<div class="ribbon-container">
					<div class="container">
						<div class="base">
							<div class="festival-starting">Arranca el BARS 14!</div>
							<div class="festival-opening">
								<div>Jueves</br></div>
								<div>31</br></div>
								<div>Octubre</div>
							</div>
							<div class="festival-closing">
								<div>Miércoles</div>
								<div>6</div>
								<div>Noviembre</div>
							</div>
							<div class="festival-to">
								<div></br></div>
								<div>al</div>
							</div>
							<div class="festival-cinemas">
								<div><strong>Complejo Monumental</strong><br/></div>
								<div>Sala 9, 10, 11: Lavalle 836<br/></div>
								<div>Sala 3: Lavalle 780</div>
							</div>
						</div>
						<div class="left_dashes"></div>
						<div class="right_dashes"></div>
						<div class="borders"></div>
						<!-- <div class="left_corner"></div>
						<div class="right_corner"></div> -->
					</div>
				</div>
			</div>

			<div id="header-menu">
				<div id="navbar" class="navbar">
					<nav id="site-navigation" class="navigation main-navigation" role="navigation">
						<h3 class="menu-toggle"><?php _e( 'Menu', 'twentythirteen' ); ?></h3>
						<a class="screen-reader-text skip-link" href="#content" title="<?php esc_attr_e( 'Skip to content', 'twentythirteen' ); ?>"><?php _e( 'Skip to content', 'twentythirteen' ); ?></a>
						<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'nav-menu' ) ); ?>
					</nav><!-- #site-navigation -->
				</div><!-- #navbar -->
				<div class="scratch" ></div>
			</div><!-- #menu -->
			
		</header><!-- #masthead -->

		<div id="main" class="site-main">
			<div id="bars-container">
