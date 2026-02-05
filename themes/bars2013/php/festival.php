<?php
/*
 * Template Name: tmpl_festival
 *
 * @package WordPress
 * @subpackage bars2013
 */
	get_header();

	// Get paragraph separator from SVG file
	$paragraphSeparator = file_get_contents(dirname(__FILE__) . '/paragraph-separator.svg');
	$position = strpos($paragraphSeparator, '<svg');
	$paragraphSeparator = substr($paragraphSeparator, $position);
?>
				<div id="page-festival" class="page">
					<div class="page-header">
						Acerca del festival
					</div>

					<div class="scratch"></div>

					<div class="festival-history text-opensans">
						<p><strong>Buenos Aires Rojo Sangre</strong> es el más antiguo festival de cine fantástico de Latinoamérica. Con un recorrido de dos décadas, el <strong>BARS</strong> es un espacio de encuentro y exhibición de cine fantástico de todo el mundo y un punto de resistencia para los creadores argentinos de cine de género. Nacido en el año 2000 como una muestra de cine independiente y, desde 2004, con el formato de festival competitivo, el <strong>BARS</strong> es una cita imprescindible para los amantes del género y para el público que quiere entrar en contacto con un cine que no se ve en otros festivales.</p>

						<h3 style="margin-top: 2.5em;">Buenos Aires Rojo Sangre 20 años</h3>
						<p>
							En sus 20 años de historia, el festival <strong>Buenos Aires Rojo Sangre</strong> acompañó y vio crecer a una generación de cineastas que, como nunca antes en la historia del cine argentino, impulsaron con fuerza una producción nacional de cine de terror, ciencia ficción y fantasía. En el festival se pudieron ver –muchas veces como estreno– obras de los referentes actuales de nuestro cine de género como <strong>Daniel de la Vega</strong>, <strong>Demian Rugna</strong>, <strong>Pablo Pares</strong>, <strong>Fabián Forte</strong>, <strong>Gabriel Grieco</strong>, <strong>Nicolás</strong> y <strong>Luciano Onetti</strong>, <strong>Gonzalo Calzada</strong>, <strong>Nicanor Loreti</strong> y muchísimos más.
						</p>

						<p>Pero tampoco el festival dejó de lado la historia del fantástico nacional, recuperando films olvidados como <i>El Inquisidor</i> (Bernardo Arias) y <i>Seis pasajes al infierno</i> (Fernando Siro), estrenando en Buenos Aires films históricos como <i>El hombre bestia</i> (Camilo Zacaría Soprani), haciendo proyecciones en fílmico de clásicos como <i>Lo que vendrá</i> (Gustavo Mosquera R.), <i>Nazareno Cruz y el lobo</i> (Leonardo Favio) o <i>La venganza del sexo</i> (Emilio Vieyra) y hasta reestrenando clásicas obras de la TV argentina como <i>Mañana puede ser verdad</i> de Narciso Ibáñez Menta.</p>

						<div class="paragraph-separator">
							<?php echo $paragraphSeparator; ?>
						</div>

						<p>Iniciado como una pequeña muestra en 2000 para 50 personas en el microcine de la Facultad de Ciencias Sociales de la UBA posteriormente fue pasando por el Centro Cultural General San Martín, el Complejo Tita Merello, el Multiplex Lavalle hasta su última sede, el Multiplex Belgrano donde más de 10000 espectadores se acercaron a las diversas actividades, que incluyen proyecciones, charlas y talleres.</p>

						<p>Además de ser un espacio para difusión del cine local, también es un espacio para conocer el cine de género todo el mundo y a sus realizadores, trayendo figuras como <strong>Fede Álvarez</strong> (<i>Evil Dead</i>, <i>Don't breathe</i>), <strong>Mick Garris</strong> (<i>Masters of Horror</i>, <i>Sleepwalkers</i>) o <strong>Ruggero Deodato</strong> (<i>Cannibal Holocaust</i>, <i>The House on the Edge of the Park</i>).</p>

						<p>El <strong>BARS</strong> es también, durante todo el año, un espacio de difusión permanente del cine de género, organizando talleres para jóvenes de efectos especiales y realización de cine (Feria del libro infantil y juvenil), concursos especiales (Fin de semana sangriento), ciclos de proyecciones (Centro Cultural Recoleta), programas de tv (Ciudad Abierta, INCAATV). También hace 10 años el <strong>BARS</strong> organiza el <strong>Mendoza Rojo Sangre</strong>, una muestra anual con lo más destacado de cada edición del festival.</p>

						<br />

						<a href="http://www.festivalrojosangre.com.ar">www.festivalrojosangre.com.ar</a>
						<br />
						Facebook: <a href="https://www.facebook.com/BuenosAiresRojoSangre" target="_blank" rel="noopener">BuenosAiresRojoSangre</a>
						<br />
						Twitter: <a href="https://twitter.com/rojosangre">@rojosangre</a>
						<br />
						Instagram: <a href="https://www.instagram.com/festivalrojosangre">@festivalrojosangre</a>
					<div>
				</div>

	<?php
		get_sidebar();
		get_footer();
	?>
