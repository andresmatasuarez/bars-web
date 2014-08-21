<?php
/**
 * @package WordPress
 * @subpackage bars2013
 */
 
?>
<!DOCTYPE html>
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
	<script src="<?php echo bloginfo('template_directory'); ?>/js/html5.js"></script>
	<![endif]-->
	
	<?php
		wp_enqueue_script('jquery');
		wp_head();
	?>

	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/style.css" type="text/css" media="screen"></link>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/fancybox/jquery.fancybox.css" type="text/css" media="screen"></link>

	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/lib.min.js"></script>
	
	<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
	
</head>

<body>

		
	<div id="header" >
		<div id="header-info-container">
			<div id="header-info">
				<div class="left">
					<span class="size-highlight">30 </span>
					de
					<span class="size-highlight"> octubre </span>
					al
					<span class="size-highlight"> 5 </span>
					de
					<span class="size-highlight"> noviembre</span>
				</div>
				<div class="center">
					2014
				</div>
				<div class="right size-highlight">
					Complejo Monumental Lavalle
				</div>
			</div>
		</div>

		<div class="scratch" ></div>

		<div id="header-container">
			<!-- DESCRIPTION & IMAGE -->
			<div id="header-description">
				Festival internacional de cine de terror, fantástico y bizarro
			</div>
			<a href="<?php echo esc_url('/'); ?>">
				<div id="header-image">
				</div>
			</a>
			
			<!-- SOCIAL ICONS -->
			<div id="header-social">
				<a href="https://www.facebook.com/BuenosAiresRojoSangre" target="blank"><div id="bars-header-social-facebook"></div></a>
				<a href="http://www.youtube.com/user/RojoSangreFestival" target="blank"><div id="bars-header-social-youtube"></div></a>
				<a href="https://twitter.com/rojosangre" target="blank"><div id="bars-header-social-twitter"></div></a>
			</div>
			
			<!-- <div id="header-clouds"></div> -->
		</div>
			
		<!-- SITE MENU -->
		<div id="header-menu" class="subtle-shadow-bottom">
			<div class="scratch" ></div>
			<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'nav-menu', 'sort_column' => 'menu_order' ) ); ?>
			
			<!-- Inside #header-menu just to stick with it on scroll. -->
			<div class="scratch" ></div>
		</div>

<!--		
		<div id="festival-ribbon" style="display: none;" >
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
				</div>
			</div>
		</div>
-->
			
	</div>

	<div id="main" >
		<div id="main-logo-bg">
		<table id="main-table">
			<tr id="main-container">
				<td id="content">
