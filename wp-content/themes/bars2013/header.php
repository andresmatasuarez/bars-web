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
		wp_enqueue_script("jquery");
		wp_head();
	?>
	
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/style.css" type="text/css" media="screen">
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/genericons/genericons.css" type="text/css" media="screen">
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/slider.css" type="text/css" media="screen">
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/js/fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />

	
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/fancybox/source/jquery.fancybox.pack.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/jquery-form-plugin/jquery.form.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/jquery-validation/jquery.validate.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/jquery-resizecrop/jquery.resizecrop-1.0.3.min.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/imgliquid/js/imgLiquid-min.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/bars.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/contact.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/slider.js"></script>
	
	<script type="text/javascript" src="http://platform.twitter.com/widgets.js" ></script>
	<script>
		(function(d, s, id) {
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) {return;}
			js = d.createElement(s); js.id = id;
			js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
			fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));
	</script>
	
</head>

<body>
	<div id="header" >
		<div id="header-container">
			<!-- DESCRIPTION & IMAGE -->
			<div id="header-description">
				Festival internacional de cine de terror, fantástico y bizarro
			</div>
			<a href="<?php echo esc_url('/'); ?>">
				<div id="header-image"></div>
			</a>
			
			<!-- SOCIAL ICONS -->
			<div id="header-social">
				<a href="https://www.facebook.com/BuenosAiresRojoSangre" target="blank"><div id="bars-header-social-facebook"></div></a>
				<a href="http://www.youtube.com/user/RojoSangreFestival" target="blank"><div id="bars-header-social-youtube"></div></a>
				<a href="https://twitter.com/rojosangre" target="blank"><div id="bars-header-social-twitter"></div></a>
			</div>
			
			<div id="header-clouds"></div>
			
			<!-- SITE MENU -->
			<div id="header-menu" class="subtle-shadow-bottom">
				<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'nav-menu' ) ); ?>
				
				<!-- Inside #header-menu just to stick with it on scroll. -->
				<div class="scratch" ></div>
			</div>
		</div>
		
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
			
	</div>

	<div id="main" >
		<div id="main-logo-bg">
			<div id="main-container">
				<div id="content">
