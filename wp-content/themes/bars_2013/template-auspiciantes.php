<?php
/**
 * Template Name: 'Auspiciantes' template
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">

			<?php /* The loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<header class="entry-header">
						<?php if ( has_post_thumbnail() && ! post_password_required() ) : ?>
						<div class="entry-thumbnail">
							<?php the_post_thumbnail(); ?>
						</div>
						<?php endif; ?>
						<h1 class="entry-title"><?php the_title(); ?></h1>
						<div class="scratch subtle-shadow">
						</div>
					</header><!-- .entry-header -->

					<div class="entry-content">
						<div id="sponsors">
							<div class="auspiciantes auspiciantes-quintadimension">
								<a href="http://www.quintadimension.com/" target="_blank" alt="Quinta Dimensión" ></a>
							</div>
							<div class="auspiciantes auspiciantes-incaa">
								<a href="http://www.incaa.gov.ar/" target="_blank" alt="INCAA" ></a>
							</div>
							<div class="auspiciantes auspiciantes-videoflims">
								<a href="http://www.videoflims.com.ar/" target="_blank" alt="Video Flims" ></a>
							</div>
							<div class="auspiciantes auspiciantes-findlingfilms">
								<a href="http://findlingfilms.com/" target="_blank" alt="Findling Films" ></a>
							</div>
							<div class="auspiciantes auspiciantes-monumental">
								<a href="http://www.cinesmultiplex.com.ar/" target="_blank" alt="Monumental Lavalle" ></a>
							</div>
							<div class="auspiciantes auspiciantes-cinesargentinos">
								<a href="http://www.cinesargentinos.com.ar/" target="_blank" alt="Cines Argentinos" ></a>
							</div>
							<div class="auspiciantes auspiciantes-mostaza">
								<a href="http://www.mostazaweb.com.ar/" target="_blank" alt="Mostaza Hamburguesas" ></a>
							</div>
							<div class="auspiciantes auspiciantes-cunnington">
								<a href="http://www.cunnington.com.ar/" target="_blank" alt="Cunnington" ></a>
							</div>
							<div class="auspiciantes auspiciantes-campari">
								<a href="http://www.campari.com/ar/es/" target="_blank" alt="Campari Argentina" ></a>
							</div>
							<div class="auspiciantes auspiciantes-sony">
								<a href="http://www.sonypictures.com.ar" target="_blank" alt="Sony Pictures" ></a>
							</div>
							<div class="auspiciantes auspiciantes-syfy">
								<a href="http://www.syfy.com" target="_blank" alt="SyFy" ></a>
							</div>
							<div class="auspiciantes auspiciantes-nacionalrock">
								<a href="http://nacionalrock.com/‎" target="_blank" alt="Nacional Rock" ></a>
							</div>
							<div class="auspiciantes auspiciantes-filmoteca">
								<a href="http://filmotecabuenosaires.wordpress.com/" target="_blank" alt="Filmoteca Buenos Aires" ></a>
							</div>
							<div class="auspiciantes auspiciantes-rarovhs">
								<a href="http://www.rarovhs.com.ar/" target="_blank" alt="Raro VHS" ></a>
							</div>
							<div class="auspiciantes auspiciantes-planetarycomics">
								<a href="http://planetary-comics.blogspot.com.ar/" target="_blank" alt="Planetary Comics" ></a>
							</div>
							<div class="auspiciantes auspiciantes-piromaniafx">
								<a href="http://www.piromaniafx.com.ar/‎" target="_blank" alt="Piromania FX" ></a>
							</div>
							<div class="auspiciantes auspiciantes-rentalbsas">
								<a href="http://www.rental-bsas.com.ar/" target="_blank" alt="Rental Bs. As." ></a>
							</div>
							<div class="auspiciantes auspiciantes-ticoral">
								<a href="http://www.ticoral.com" target="_blank" alt="Ticoral" ></a>
							</div>
							<div class="auspiciantes auspiciantes-eda">
								<a href="http://www.edaeditores.com.ar/" target="_blank" alt="EDA" ></a>
							</div>
							<div class="auspiciantes auspiciantes-ab">
								<a href="http://arianabouzon.wix.com/arianabouzon#!" target="_blank" alt="AB Producciones" ></a>
							</div>
							<div class="auspiciantes auspiciantes-vacomoloco">
								<a href="https://www.facebook.com/vacomoloco" target="_blank" alt="Vacomoloco" ></a>
							</div>
							<div class="auspiciantes auspiciantes-toronja">
								<a href="http://www.toronjaproducciones.com.ar/" target="_blank" alt="Toronja Producciones" ></a>
							</div>
							<div class="auspiciantes auspiciantes-leotripi">
								<!-- <a href="" target="_blank" alt="Toronja Producciones" ></a> -->
							</div>
							<div class="auspiciantes auspiciantes-festeringslime">
								<a href="mailto:amatasuarez@gmail.com" target="_blank" alt="Festering Slime Web Design" ></a>
							</div>
							<div class="auspiciantes auspiciantes-metamorfosisfx">
								<a href="http://www.metamorfosisfx.com.ar/" target="_blank" alt="Metamorfosis FX" ></a>
							</div>
							<div class="auspiciantes auspiciantes-bsasciudad">
								<a href="http://www.buenosaires.gob.ar/" target="_blank" alt="Buenos Aires Ciudad" ></a>
							</div>
							<div class="auspiciantes auspiciantes-rafma">
								<a href="https://www.facebook.com/pages/RAFMA-Red-Argentina-de-Festivales-y-Muestras-Audiovisuales/151718504869922" target="_blank" alt="RAFMA Red Argentina de Festivales y Muestras Audiovisuales" ></a>
							</div>
							<div class="auspiciantes auspiciantes-fantafestivales">
								<a href="http://www.fantafestivales.org/" target="_blank" alt="Fanta Festivales" ></a>
							</div>
							<div class="auspiciantes auspiciantes-cinefania">
								<a href="http://www.cinefania.com/" target="_blank" alt="Cinefanía" ></a>
							</div>
							<div class="auspiciantes auspiciantes-requiem">
								<a href="https://www.facebook.com/pages/Requiem/56147413104" target="_blank" alt="Requiem club" ></a>
							</div>
							<div class="auspiciantes auspiciantes-lacripta">
								<!-- <a href="http://reservas.salaslacripta.com.ar/" target="_blank" alt="Salas La Cripta" ></a> -->
							</div>
						</div>
					</div><!-- .entry-content -->
					
				</article><!-- #post -->

			<?php endwhile; ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>