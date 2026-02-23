<?php
/**
 * Template Name: Call
 * @package BARS2026
 */

get_header();

$edition = Editions::current();
$edition_number = Editions::romanNumerals($edition);
$festival_dates = Editions::datesLabel($edition);
$call = Editions::call($edition);
$call_deadline = Editions::callDeadline($edition);
$call_deadline_extended = Editions::callDeadlineExtended($edition);
$call_is_closed = Editions::isCallClosed($edition);

// Get authorization links if available
$authorization = isset($call['authorization']) ? $call['authorization'] : array();
$awards = Editions::getAwards($edition);
?>

<?php
// WP 3.9 compat: get_template_part() $args (3rd param) requires WP 5.5+
$GLOBALS['page_hero_args'] = array(
    'title' => 'Convocatoria',
    'subtitle' => 'Edición ' . $edition_number . ' • ' . $festival_dates,
);
get_template_part('template-parts/sections/page', 'hero');
?>

<!-- Content Section -->
<section class="relative min-h-96 py-12 lg:py-16 <?php echo $call_is_closed ? 'call-closed' : ''; ?>">
    <div class="max-w-[1000px] mx-auto px-5 lg:px-0">

        <?php if ($call_is_closed): ?>
        <div class="flex flex-col items-center justify-center py-12 text-center">
            <?php echo bars_icon('alert-triangle', 'w-12 h-12 text-bars-icon-empty mb-4'); ?>
            <h3 class="font-heading text-2xl text-bars-text-primary mb-2">
                La convocatoria está cerrada
            </h3>
            <p class="text-sm text-bars-text-subtle max-w-xs">
                Las bases se mantienen visibles para referencia.
            </p>
        </div>
        <div class="h-px bg-bars-divider mb-8"></div>
        <?php endif; ?>

        <!-- English Note -->
        <?php if (isset($call['termsEN'])): ?>
        <div class="flex items-center gap-2 bg-white/5 rounded-bars-md px-4 py-3 mb-8">
            <?php echo bars_icon('globe', 'w-5 h-5 shrink-0'); ?>
            <span class="text-sm text-bars-text-muted italic">For the English version of these terms,
            <a href="<?php echo esc_url(get_template_directory_uri() . '/' . $call['termsEN']); ?>"
               target="_blank"
               class="text-sm text-bars-link-accent font-semibold italic hover:underline">
                download them here</a>.</span>
        </div>
        <?php endif; ?>

        <!-- Intro Section -->
        <div class="space-y-6 mb-10">
            <p class="text-sm lg:text-base text-bars-text-secondary leading-relaxed lg:leading-loose">
                Para presentar películas a la consideración del Comité de Programación del Festival el postulante debe enviar un screener online a través de las plataformas:
            </p>

            <!-- Platform Links -->
            <div class="flex flex-wrap justify-center gap-4">
                <a href="https://filmfreeway.com/BuenosAiresRojoSangre"
                   target="_blank"
                   class="inline-flex items-center justify-center px-4 py-2 bg-bars-primary text-white text-sm font-semibold rounded-bars-sm hover:bg-[#A00000] transition-colors">
                    FilmFreeway
                </a>
                <a href="https://festhome.com/festival/buenos-aires-rojo-sangre"
                   target="_blank"
                   class="inline-flex items-center justify-center px-4 py-2 bg-bars-primary text-white text-sm font-semibold rounded-bars-sm hover:bg-[#A00000] transition-colors">
                    Festhome
                </a>
            </div>

            <!-- Deadline -->
            <?php if ($call_deadline): ?>
            <div class="flex items-center gap-2 bg-bars-primary/10 rounded-bars-md px-4 py-3">
                <?php echo bars_icon('calendar', 'w-5 h-5 shrink-0'); ?>
                <span class="text-sm lg:text-base text-bars-text-primary">
                    La fecha tope para la recepción del material es el
                    <?php if (isset($call_deadline_extended)): ?>
                        <del class="opacity-50 font-medium"><?php echo esc_html(getDateInSpanish($call_deadline)); ?></del>
                        — extendida hasta el <strong class="font-medium underline"><?php echo esc_html(getDateInSpanish($call_deadline_extended)); ?></strong>.
                    <?php else: ?>
                        <strong class="font-medium underline"><?php echo esc_html(getDateInSpanish($call_deadline)); ?></strong>.
                    <?php endif; ?>
                </span>
            </div>
            <?php endif; ?>

            <!-- Fees Description -->
            <p class="text-sm lg:text-base text-bars-text-secondary leading-relaxed lg:leading-loose">
                La inscripción al festival dentro de los plazos establecidos no tiene costo para el material producido en Argentina, mientras que tiene un valor de USD 6 para el material producido en el resto de Latinoamérica y de USD 12 para el material producido en el resto del mundo.
            </p>

            <p class="text-sm lg:text-base text-bars-text-secondary leading-relaxed lg:leading-loose">
                Los eventuales gastos de envío corren por cuenta de los participantes, como así también cualquier impuesto, tasa, gravamen, gastos de aduana, etc., que surja del envío. El Festival no pagará tasa alguna ocasionada por el envío de las copias.
            </p>

            <!-- Fee Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 lg:gap-6">
                <div class="bg-white/5 rounded-bars-md p-4 lg:p-5 text-center">
                    <p class="text-xs lg:text-sm font-medium text-bars-text-muted mb-1">Argentina</p>
                    <p class="text-xl lg:text-2xl font-semibold text-bars-text-primary">Gratis</p>
                </div>
                <div class="bg-white/5 rounded-bars-md p-4 lg:p-5 text-center">
                    <p class="text-xs lg:text-sm font-medium text-bars-text-muted mb-1">Resto de Latinoamérica</p>
                    <p class="text-xl lg:text-2xl font-semibold text-bars-text-primary">USD 6</p>
                </div>
                <div class="bg-white/5 rounded-bars-md p-4 lg:p-5 text-center">
                    <p class="text-xs lg:text-sm font-medium text-bars-text-muted mb-1">Resto del Mundo</p>
                    <p class="text-xl lg:text-2xl font-semibold text-bars-text-primary">USD 12</p>
                </div>
            </div>

            <p class="text-xs lg:text-sm text-bars-text-subtle italic">
                Las inscripciones tardías tienen recargo en todas las categorías, incluida la producción nacional.
            </p>
        </div>

        <!-- Participation Conditions -->
        <div class="mb-10">
            <h3 class="text-lg font-semibold text-bars-text-primary mb-4">Condiciones de Participación</h3>
            <div class="text-sm lg:text-base text-bars-text-secondary leading-relaxed lg:leading-loose space-y-4">
                <p>
                    Solo podrán participar en las secciones competitivas las películas que no hayan tenido ningún tipo de difusión en medios electrónicos, estreno comercial en cines en cualquier sala del país, o que no hayan tenido ninguna clase de exhibición pública en la Ciudad de Buenos Aires y alrededores dentro de los 180 días previos al inicio del festival. Siempre se privilegiará en la selección a los films que no hayan tenido ninguna clase de exhibición en la Ciudad de Buenos Aires y alrededores.
                </p>
                <p>
                    Los filmes no podrán ser retirados del festival una vez que se haga conocer la selección. La presentación de material a esta convocatoria supone conocimiento de las presentes condiciones.
                </p>
                <p>
                    El festival podrá eliminar de su selección, incluso una vez presentada la programación, toda vez que identifique puntos del presente reglamento siendo infringidos.
                </p>
            </div>
        </div>

        <!-- Genres Section -->
        <div class="mb-10">
            <h3 class="text-lg font-semibold text-bars-text-primary mb-4">Géneros</h3>
            <p class="text-sm lg:text-base text-bars-text-secondary leading-relaxed lg:leading-loose">
                El Comité de Programación tendrá en consideración largometrajes encuadrables dentro de los géneros fantástico, bizarro, ciencia–ficción y terror.
            </p>
        </div>

        <!-- Sections -->
        <div class="mb-10">
            <h3 class="text-lg font-semibold text-bars-text-primary mb-4">Secciones</h3>
            <p class="text-sm lg:text-base text-bars-text-secondary leading-relaxed lg:leading-loose mb-4">
                El festival estará dividido en diferentes secciones:
            </p>
            <ul class="space-y-3 pl-4 text-sm lg:text-base text-bars-text-secondary leading-relaxed lg:leading-loose">
                <li>• Sección competitiva de largometrajes de producción posterior al 1 de enero de 2024.</li>
                <li>• Secciones internacional competitiva de cortometrajes para películas de cualquier país de origen, con una duración máxima de 25 minutos, con producción posterior al 1 de enero de 2024.</li>
                <li>• Secciones informativas de largometrajes, no competitivas, para películas de cualquier año de producción.</li>
                <li>• Secciones informativas de cortometrajes y mediometrajes, no competitivas, sin limitación de fechas o duración.</li>
            </ul>
            <p class="text-sm lg:text-base text-bars-text-secondary leading-relaxed lg:leading-loose mt-4">
                El festival se reserva la posibilidad de organizar nuevas secciones no comprendidas en estas bases.
            </p>
            <p class="text-xs lg:text-sm text-bars-text-muted italic mt-3">
                Se considera largometrajes a producciones de una duración superior a los 60 minutos.
            </p>
        </div>

        <!-- Awards and Jury -->
        <div class="mb-10">
            <h3 class="text-lg font-semibold text-bars-text-primary mb-4">Premios y Jurados</h3>
            <p class="text-sm lg:text-base text-bars-text-secondary leading-relaxed lg:leading-loose mb-6">
                La Dirección del Festival nombrará los miembros de los jurados para cada sección. No podrán formar parte del Jurado aquellas personas que tengan intereses en la producción y/o explotación de las películas presentadas a competición. Se otorgarán los siguientes premios:
            </p>

            <?php if (!empty($awards)): ?>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <?php foreach ($awards as $category): ?>
                <div>
                    <h4 class="font-heading text-lg font-semibold text-bars-text-primary mb-3"><?php echo esc_html($category['heading']); ?></h4>
                    <ul class="text-sm text-bars-text-secondary space-y-1">
                        <?php foreach ($category['items'] as $item): ?>
                        <li><?php echo esc_html($item); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php if (!empty($category['note'])): ?>
                    <p class="text-xs text-bars-text-muted italic mt-3"><?php echo esc_html($category['note']); ?></p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <p class="text-sm lg:text-base text-bars-text-secondary leading-relaxed lg:leading-loose mt-6">
                Ninguna película podrá recibir más de dos premios. El Jurado podrá proponer menciones especiales y ninguna película podrá recibir más de dos menciones.
            </p>
        </div>

        <!-- General Provisions -->
        <div class="mb-10">
            <h3 class="text-lg font-semibold text-bars-text-primary mb-4">Disposiciones Generales</h3>
            <div class="text-sm lg:text-base text-bars-text-secondary leading-relaxed lg:leading-loose space-y-4">
                <p>
                    El Festival se reserva el derecho de seleccionar las obras participantes, determinar en qué sección irá cada una y establecer el orden y la fecha de exhibición.
                </p>
                <p>
                    El Festival se compromete a no efectuar más de tres (3) proyecciones del film. En caso de una eventual realización de una edición online, el festival requerirá una autorización explícita por parte de los realizadores.
                </p>
                <p>
                    Las películas cuya lengua no sea el castellano deberán presentarse subtituladas (al español) o, en su defecto, deberán adjuntar una guía de diálogos (Dialogue List o Subtitle List) en español o inglés para facilitar la traducción.
                </p>
                <p>
                    La participación en el Festival implica la aceptación del presente Reglamento. Cualquier cuestión que surja a lo largo del Festival, no contemplada en el presente Reglamento, será decidida por la organización del certamen.
                </p>
            </div>
        </div>

        <!-- Formats -->
        <div class="mb-10">
            <h3 class="text-lg font-semibold text-bars-text-primary mb-4">Formatos</h3>
            <p class="text-sm lg:text-base text-bars-text-secondary leading-relaxed lg:leading-loose mb-6">
                En caso de que la película sea seleccionada para participar, la organización del festival se pondrá en contacto con los realizadores para analizar el formato en que se realizará la proyección.
            </p>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-heading text-lg font-semibold text-bars-text-primary mb-3">Largometrajes</h4>
                    <p class="text-sm lg:text-base text-bars-text-secondary leading-relaxed">
                        Los largometrajes se proyectarán en DCP, Blu-Ray o archivo digital.
                    </p>
                </div>
                <div>
                    <h4 class="font-heading text-lg font-semibold text-bars-text-primary mb-3">Cortometrajes</h4>
                    <p class="text-sm lg:text-base text-bars-text-secondary leading-relaxed mb-3">
                        Los cortometrajes se proyectarán sólo en archivo digital en HD 1080p sólo con la siguiente exportación (enviar sin barras de ajuste):
                    </p>
                    <ul class="text-sm text-bars-text-muted space-y-1">
                        <li>Códec H.264</li>
                        <li>Resolución: 1080p</li>
                        <li>Canales de Audio: 2 (estéreo)</li>
                        <li>Audio Sample Rate: 48kHz</li>
                        <li>Audio Bit Depth: 16 ó 24 bits</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Authorization Section -->
        <?php if (!empty($authorization)): ?>
        <div class="mb-10">
            <h3 class="text-lg font-semibold text-bars-text-primary mb-4">Hoja de Autorización</h3>
            <p class="text-sm lg:text-base text-bars-text-secondary leading-relaxed mb-6">
                Descargue el formulario de autorización en el formato de su preferencia:
            </p>
            <div class="flex flex-wrap gap-4">
                <?php if (!empty($authorization['es'])): ?>
                <a href="<?php echo esc_url(get_template_directory_uri() . '/' . $authorization['es']); ?>"
                   target="_blank"
                   class="inline-flex items-center justify-center px-4 py-2 bg-bars-primary text-white text-sm font-semibold rounded-bars-sm hover:bg-[#A00000] transition-colors">
                    PDF Español
                </a>
                <?php endif; ?>
                <?php if (!empty($authorization['docxES'])): ?>
                <a href="<?php echo esc_url(get_template_directory_uri() . '/' . $authorization['docxES']); ?>"
                   target="_blank"
                   class="inline-flex items-center justify-center px-4 py-2 bg-bars-primary text-white text-sm font-semibold rounded-bars-sm hover:bg-[#A00000] transition-colors">
                    DOCX Español
                </a>
                <?php endif; ?>
                <?php if (!empty($authorization['en'])): ?>
                <a href="<?php echo esc_url(get_template_directory_uri() . '/' . $authorization['en']); ?>"
                   target="_blank"
                   class="inline-flex items-center justify-center px-4 py-2 bg-bars-primary text-white text-sm font-semibold rounded-bars-sm hover:bg-[#A00000] transition-colors">
                    PDF English
                </a>
                <?php endif; ?>
                <?php if (!empty($authorization['docxEN'])): ?>
                <a href="<?php echo esc_url(get_template_directory_uri() . '/' . $authorization['docxEN']); ?>"
                   target="_blank"
                   class="inline-flex items-center justify-center px-4 py-2 bg-bars-primary text-white text-sm font-semibold rounded-bars-sm hover:bg-[#A00000] transition-colors">
                    DOCX English
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</section>

<?php get_footer(); ?>
