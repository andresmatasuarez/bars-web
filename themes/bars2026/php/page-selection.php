<?php
/**
 * Template Name: Selection
 * @package BARS2026
 */

get_header();

$currentEdition = isset($_GET['edition'])
    ? Editions::getByNumber(intval($_GET['edition']))
    : Editions::current();
$days = Editions::days($currentEdition);
$from = $days[0];
$to = $days[count($days) - 1];
$fromDay = $from->format('d');
$toDay = $to->format('d');
$month = ucfirst(getSpanishMonthName($from->format('F')));
$year = $from->format('Y');

$subtitle = 'Edición ' . Editions::romanNumerals($currentEdition)
    . ' • ' . $fromDay . ' - ' . $toDay . ' ' . $month . ' ' . $year;
?>

<?php get_template_part('template-parts/sections/page', 'hero', array(
    'title' => 'Programación',
    'subtitle' => $subtitle,
)); ?>

<section class="relative min-h-[60vh] pb-16">
    <div class="max-w-[1000px] mx-auto px-5 lg:px-0">
        <?php
        if (countMovieEntriesForEdition($currentEdition) === "0") {
            if (Editions::isCallClosed($currentEdition)) {
                ?>
                <div class="text-center py-20">
                    <h2 class="font-heading text-2xl lg:text-3xl text-bars-text-primary mb-4">
                        La programación se está definiendo
                    </h2>
                    <p class="text-sm lg:text-base text-bars-text-muted leading-relaxed max-w-md mx-auto">
                        La grilla para este año ya se encuentra en proceso de selección.
                        Estará disponible en los próximos días.
                    </p>
                </div>
                <?php
            } else {
                $linkToCall = get_permalink(get_page_by_path('convocatoria'));
                ?>
                <div class="text-center py-20">
                    <h2 class="font-heading text-2xl lg:text-3xl text-bars-text-primary mb-4">
                        La convocatoria aún está abierta
                    </h2>
                    <p class="text-sm lg:text-base text-bars-text-muted leading-relaxed max-w-md mx-auto">
                        No pierdas la oportunidad de proyectar tu película o corto en el festival.
                        Revisá los <a href="<?php echo esc_url($linkToCall); ?>" class="text-bars-primary hover:underline">términos y condiciones</a>.
                    </p>
                </div>
                <?php
            }
        } else {
            ?>
            <div id="react-root-selection"></div>
            <script>
                <?php
                    $movieSections = array();
                    foreach (getMovieSectionsForEdition($currentEdition) as $sectionId) {
                        $movieSections[$sectionId] = getMovieSectionLabel($sectionId);
                    }

                    $movies = getMoviesAndMovieBlocks($currentEdition);

                    $prepareMovie = function($movie) {
                        setup_postdata($movie);
                        return prepareMovieForDisplaying($movie);
                    };

                    echo 'window.MOVIES = ' . json_encode(array_map($prepareMovie, $movies)) . ';';
                    echo 'window.MOVIE_SECTIONS = ' . json_encode($movieSections) . ';';
                    echo 'window.CURRENT_EDITION = ' . $currentEdition['number'] . ';';
                    echo 'window.LATEST_EDITION = ' . Editions::current()['number'] . ';';
                ?>
            </script>
            <script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/selection.iife.js"></script>
            <?php
        }
        ?>
    </div>
</section>

<?php get_footer(); ?>
