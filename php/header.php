<?php
/**
 * @package WordPress
 * @subpackage bars2013
 */

	require_once 'editions.php';

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

	<!--
		Added to avoid screen being "trimmed" on mobile displays
		more info: http://stackoverflow.com/questions/22732475/div-width-100-percent-not-working-in-mobile-browser
	-->
	<meta name="viewport" content="width=1020">

	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<!--[if lt IE 9]>
	<script src="<?php echo bloginfo('template_directory'); ?>/js/html5.js"></script>
	<![endif]-->

	<?php
		wp_head();
	?>

	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/style.css" type="text/css"></link>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/main.js"></script>

	<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>

</head>

<body>
	<!-- FACEBOOK SOCIAL PLUGIN -->
	<script>
	  window.fbAsyncInit = function() {
	    FB.init({
	      appId      : '1439679999607579',
	      xfbml      : true,
	      version    : 'v2.4'
	    });
	  };

	  (function(d, s, id){
	     var js, fjs = d.getElementsByTagName(s)[0];
	     if (d.getElementById(id)) {return;}
	     js = d.createElement(s); js.id = id;
	     js.src = "//connect.facebook.net/en_US/sdk.js";
	     fjs.parentNode.insertBefore(js, fjs);
	   }(document, 'script', 'facebook-jssdk'));
	</script>

	<?php
		$edition = Editions::current();
	?>

	<div id="current-edition-year" style="display: none;">
		<?php echo Editions::from($edition)->format('Y'); ?>
	</div>

	<div id="header" >
		<div id="header-info-container">
			<div id="header-info">
				<div class="left">
					<?php
						$from = Editions::from($edition);
						$to = Editions::to($edition);
						$year = $from->format('Y');

						$sameMonth = $from->format('F') == $to->format('F');
						if (Editions::shouldDisplayOnlyMonths($edition)) {
							if ($sameMonth){
					?>
								<span class="size-highlight"><?php echo getSpanishMonthName($to->format('F')); ?></span>
					<?php
							} else {
					?>
								<span class="size-highlight"><?php echo getSpanishMonthName($from->format('F')); ?></span>
								/
								<span class="size-highlight"><?php echo getSpanishMonthName($to->format('F')); ?></span>
						<?php
							}
						?>
					<?php
						} else {
							if ($sameMonth){
					?>
								<span class="size-highlight"><?php echo $from->format('j'); ?></span>
								al
								<span class="size-highlight"><?php echo $to->format('j'); ?></span>
								de
								<span class="size-highlight"><?php echo getSpanishMonthName($to->format('F')); ?></span>
					<?php
							} else {
					?>
								<span class="size-highlight"><?php echo $from->format('j'); ?></span>
								de
								<span class="size-highlight"><?php echo getSpanishMonthName($from->format('F')); ?></span>
								al
								<span class="size-highlight"><?php echo $to->format('j'); ?></span>
								de
								<span class="size-highlight"><?php echo getSpanishMonthName($to->format('F')); ?></span>
						<?php
							}
						?>
					<?php
						}
					?>
				</div>
				<div class="center"><?php echo $year; ?></div>
				<div class="right size-highlight">
					<?php
						$venues = Editions::venues($edition);
					?>
					<table>
						<thead>
							<tr>
								<?php
									foreach ($venues as $key => $value) {
									 	echo '<th>' . $value['name'] . '</th>';
									}
								?>
							</tr>
						</thead>
						<tbody>
							<tr>
								<?php
									foreach ($venues as $key => $value) {
									 	echo '<td>' . $value['address'] . '</td>';
									}
								?>
							</tr>
						</tbody>
					</table>
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
				<div class="header-image">
				</div>
			</a>

			<!-- SOCIAL ICONS -->
			<div id="header-social">
				<a href="https://www.facebook.com/BuenosAiresRojoSangre" target="blank">
					<span class="fa fa-facebook-square"></span>
				</a>
				<a href="http://www.youtube.com/user/RojoSangreFestival" target="blank">
					<span class="fa fa-youtube-square"></span>
				</a>
				<a href="https://twitter.com/rojosangre" target="blank">
					<span class="fa fa-twitter-square"></span>
				</a>
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
							<div><strong>Multiplex</strong><br/></div>
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
