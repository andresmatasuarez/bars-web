<?php
/**
 * Template Name: About
 * @package BARS2026
 */

get_header();
?>

<?php
// WP 3.9 compat: get_template_part() $args (3rd param) requires WP 5.5+
$GLOBALS['page_hero_args'] = array(
    'title' => 'Acerca del BARS',
    'subtitle' => 'Más de dos décadas de cine fantástico en Argentina',
);
get_template_part('template-parts/sections/page', 'hero');
?>

<!-- Content Section -->
<section class="relative min-h-96 py-12 lg:py-16">
    <div class="max-w-[1000px] mx-auto px-5 lg:px-0">

        <!-- Tab Navigation -->
        <div class="bg-bars-bg-medium rounded-bars-md flex mb-12 lg:mb-16">
            <button data-tab="historia"
                    class="about-tab flex-1 py-3 px-4 text-sm font-semibold text-center rounded-l-bars-md bg-bars-primary text-white cursor-pointer transition-colors hover:bg-[#A00000]"
                    aria-selected="true">
                Historia
            </button>
            <button data-tab="rojosangretv"
                    class="about-tab flex-1 py-3 px-4 text-sm font-medium text-center rounded-r-bars-md bg-bars-bg-medium text-bars-text-muted cursor-pointer transition-colors hover:text-white"
                    aria-selected="false">
                #RojoSangreTV
            </button>
        </div>

        <!-- Historia Tab Panel -->
        <div id="tab-historia" class="about-tab-panel space-y-8 lg:space-y-12">

            <!-- El Festival -->
            <div>
                <h2 class="font-heading text-[28px] lg:text-4xl font-semibold text-bars-text-primary mb-4 lg:mb-6">El Festival</h2>
                <p class="text-sm lg:text-base text-bars-text-secondary leading-relaxed lg:leading-loose"><strong>Buenos Aires Rojo Sangre</strong> es el más antiguo festival de cine fantástico de Latinoamérica. Con un recorrido de dos décadas, el <strong>BARS</strong> es un espacio de encuentro y exhibición de cine fantástico de todo el mundo y un punto de resistencia para los creadores argentinos de cine de género. Nacido en el año 2000 como una muestra de cine independiente y, desde 2004, con el formato de festival competitivo, el <strong>BARS</strong> es una cita imprescindible para los amantes del género y para el público que quiere entrar en contacto con un cine que no se ve en otros festivales.</p>
            </div>

            <!-- Buenos Aires Rojo Sangre 20 años -->
            <div>
                <h2 class="font-heading text-[28px] lg:text-4xl font-semibold text-bars-text-primary mb-4 lg:mb-6">Buenos Aires Rojo Sangre 20 años</h2>
                <div class="space-y-4 lg:space-y-6">
                    <p class="text-sm lg:text-base text-bars-text-secondary leading-relaxed lg:leading-loose">En sus 20 años de historia, el festival <strong>Buenos Aires Rojo Sangre</strong> acompañó y vio crecer a una generación de cineastas que, como nunca antes en la historia del cine argentino, impulsaron con fuerza una producción nacional de cine de terror, ciencia ficción y fantasía. En el festival se pudieron ver –muchas veces como estreno– obras de los referentes actuales de nuestro cine de género como <strong>Daniel de la Vega</strong>, <strong>Demian Rugna</strong>, <strong>Pablo Pares</strong>, <strong>Fabián Forte</strong>, <strong>Gabriel Grieco</strong>, <strong>Nicolás</strong> y <strong>Luciano Onetti</strong>, <strong>Gonzalo Calzada</strong>, <strong>Nicanor Loreti</strong> y muchísimos más.</p>
                    <p class="text-sm lg:text-base text-bars-text-secondary leading-relaxed lg:leading-loose">Pero tampoco el festival dejó de lado la historia del fantástico nacional, recuperando films olvidados como <em>El Inquisidor</em> (<strong>Bernardo Arias</strong>) y <em>Seis pasajes al infierno</em> (<strong>Fernando Siro</strong>), estrenando en Buenos Aires films históricos como <em>El hombre bestia</em> (<strong>Camilo Zacaría Soprani</strong>), haciendo proyecciones en fílmico de clásicos como <em>Lo que vendrá</em> (<strong>Gustavo Mosquera R.</strong>), <em>Nazareno Cruz y el lobo</em> (<strong>Leonardo Favio</strong>) o <em>La venganza del sexo</em> (<strong>Emilio Vieyra</strong>) y hasta reestrenando clásicas obras de la TV argentina como <em>Mañana puede ser verdad</em> de <strong>Narciso Ibáñez Menta</strong>.</p>
                </div>
            </div>

            <!-- Nuestras Sedes -->
            <div>
                <h2 class="font-heading text-[28px] lg:text-4xl font-semibold text-bars-text-primary mb-4 lg:mb-6">Nuestras Sedes</h2>
                <p class="text-sm lg:text-base text-bars-text-secondary leading-relaxed lg:leading-loose">Iniciado como una pequeña muestra en el año 2000 para 50 personas en el microcine de la Facultad de Ciencias Sociales de la UBA posteriormente fue pasando por el Centro Cultural General San Martín, el Complejo Tita Merello, el Multiplex Lavalle hasta su última sede, el Multiplex Belgrano donde más de 10000 espectadores se acercaron a las diversas actividades, que incluyen proyecciones, charlas y talleres.</p>
            </div>

            <!-- Cine Internacional -->
            <div>
                <h2 class="font-heading text-[28px] lg:text-4xl font-semibold text-bars-text-primary mb-4 lg:mb-6">Cine Internacional</h2>
                <p class="text-sm lg:text-base text-bars-text-secondary leading-relaxed lg:leading-loose">Además de ser un espacio para difusión del cine local, también es un espacio para conocer el cine de género todo el mundo y a sus realizadores, trayendo figuras como <strong>Fede Álvarez</strong> (<em>Evil Dead</em>, <em>Don't breathe</em>), <strong>Mick Garris</strong> (<em>Masters of Horror</em>, <em>Sleepwalkers</em>) o <strong>Ruggero Deodato</strong> (<em>Cannibal Holocaust</em>, <em>The House on the Edge of the Park</em>).</p>
            </div>

            <!-- Actividades Todo el Año -->
            <div>
                <h2 class="font-heading text-[28px] lg:text-4xl font-semibold text-bars-text-primary mb-4 lg:mb-6">Actividades Todo el Año</h2>
                <p class="text-sm lg:text-base text-bars-text-secondary leading-relaxed lg:leading-loose">El <strong>BARS</strong> es también, durante todo el año, un espacio de difusión permanente del cine de género, organizando talleres para jóvenes de efectos especiales y realización de cine (Feria del libro infantil y juvenil), concursos especiales (Fin de semana sangriento), ciclos de proyecciones (Centro Cultural Recoleta), programas de tv (Ciudad Abierta, INCAATV). También hace 10 años el BARS organiza el <strong>Mendoza Rojo Sangre</strong>, una muestra anual con lo más destacado de cada edición del festival.</p>
            </div>

        </div>

        <!-- RojoSangreTV Tab Panel -->
        <div id="tab-rojosangretv" class="about-tab-panel hidden">

            <!-- Heading & Description -->
            <div class="mb-8 lg:mb-10">
                <h2 class="font-heading text-[28px] lg:text-4xl font-semibold text-bars-text-primary mb-4 lg:mb-6">#RojoSangreTV</h2>
                <p class="text-sm lg:text-base text-bars-text-secondary leading-relaxed lg:leading-loose">Rojo Sangre TV salió al aire los viernes a la medianoche de septiembre a noviembre de 2018, a través de Comarca SI, canal 32.3, de TDA. Los mejores cortos del Festival Buenos Aires Rojo Sangre en la televisión. Con la conducción estelar de <strong>Ariel Toronja</strong> y gran elenco!</p>
            </div>

            <!-- Video Grid -->
            <?php
            $chapterIds = [
                'U2S3LVH57zo',
                'd58qkkjxXC8',
                'DLaJ0JvUq8c',
                'JFsCyfADn2Q',
                'HFI3KDezfbU',
                'f9oEjfTDdLw',
                'c54LZ5NP3xc',
            ];
            ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
                <?php foreach ($chapterIds as $index => $id): ?>
                <div>
                    <div class="aspect-video rounded-bars-md overflow-hidden bg-bars-bg-card">
                        <iframe class="w-full h-full"
                                src="https://www.youtube.com/embed/<?php echo esc_attr($id); ?>"
                                title="Episodio <?php echo $index + 1; ?>"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen
                                loading="lazy"></iframe>
                    </div>
                    <p class="text-xs lg:text-sm text-bars-text-muted font-medium mt-2">Episodio <?php echo $index + 1; ?></p>
                </div>
                <?php endforeach; ?>
            </div>

        </div>

    </div>
</section>

<?php get_footer(); ?>
