<?php
/**
 * Template Name: Selection
 * @package BARS2026
 */

get_header();

$currentEdition = isset($_GET['edition'])
    ? Editions::getByNumber(intval($_GET['edition']))
    : Editions::current();
$isPastEdition = $currentEdition['number'] !== Editions::current()['number'];
$festival_dates = Editions::datesLabel($currentEdition);
$year = Editions::year($currentEdition);

$subtitle = 'Edición ' . Editions::romanNumerals($currentEdition)
    . ' • ' . $festival_dates;
?>

<?php
$title = $isPastEdition
    ? 'Programación ' . Editions::romanNumerals($currentEdition) . ' (' . $year . ')'
    : 'Programación';
get_template_part('template-parts/sections/page', 'hero', array(
    'title' => $title,
    'subtitle' => $subtitle,
)); ?>

<section class="relative min-h-[60vh] pb-16">
    <div class="max-w-[1000px] mx-auto px-5 lg:px-0">
        <?php if ($isPastEdition): ?>
        <div class="pt-6">
            <a href="<?php echo esc_url(home_url('/programacion')); ?>"
               class="text-sm text-bars-link-accent hover:underline">&larr; Ver programación actual</a>
        </div>
        <?php endif; ?>
        <?php
        if (countMovieEntriesForEdition($currentEdition) === "0") {
            if (Editions::isCallClosed($currentEdition)) {
                ?>
                <div class="flex flex-col items-center justify-center py-20 text-center">
                    <?php echo bars_icon('clapperboard', 'w-12 h-12 text-bars-icon-empty mb-4'); ?>
                    <h3 class="font-heading text-2xl text-bars-text-primary mb-2">
                        La programación se está definiendo
                    </h3>
                    <p class="text-sm text-bars-text-subtle max-w-xs">
                        La grilla para este año ya se encuentra en proceso de selección.
                        Estará disponible en los próximos días.
                    </p>
                    <?php
                    $prevEdition = Editions::getByNumber($currentEdition['number'] - 1);
                    if ($prevEdition && countMovieEntriesForEdition($prevEdition) !== "0"):
                        $prevRoman = Editions::romanNumerals($prevEdition);
                        $prevYear = Editions::from($prevEdition)->format('Y');
                    ?>
                    <p class="text-sm text-bars-text-subtle text-center max-w-xs mx-auto mt-4">
                        Mientras tanto, podés ver la
                        <a href="<?php echo esc_url(home_url('/programacion?edition=' . $prevEdition['number'])); ?>"
                           class="text-bars-badge-text hover:underline">programación de la edición anterior (BARS <?php echo esc_html($prevRoman); ?> - <?php echo esc_html($prevYear); ?>)</a>.
                    </p>
                    <?php endif; ?>
                </div>
                <?php
            } else {
                $linkToCall = get_permalink(get_page_by_path('convocatoria'));
                ?>
                <div class="flex flex-col items-center justify-center py-20 text-center">
                    <?php echo bars_icon('megaphone', 'w-12 h-12 text-bars-icon-empty mb-4'); ?>
                    <h3 class="font-heading text-2xl text-bars-text-primary mb-2">
                        La convocatoria aún está abierta
                    </h3>
                    <p class="text-sm text-bars-text-subtle max-w-xs">
                        No pierdas la oportunidad de proyectar tu película o corto en el festival.
                        Revisá los <a href="<?php echo esc_url($linkToCall); ?>" class="text-bars-badge-text hover:underline">términos de la convocatoria</a>.
                    </p>
                    <?php
                    $prevEdition = Editions::getByNumber($currentEdition['number'] - 1);
                    if ($prevEdition && countMovieEntriesForEdition($prevEdition) !== "0"):
                        $prevRoman = Editions::romanNumerals($prevEdition);
                        $prevYear = Editions::from($prevEdition)->format('Y');
                    ?>
                    <p class="text-sm text-bars-text-subtle text-center max-w-xs mx-auto mt-4">
                        Mientras tanto, podés ver la
                        <a href="<?php echo esc_url(home_url('/programacion?edition=' . $prevEdition['number'])); ?>"
                           class="text-bars-badge-text hover:underline">programación de la edición anterior (BARS <?php echo esc_html($prevRoman); ?> - <?php echo esc_html($prevYear); ?>)</a>.
                    </p>
                    <?php endif; ?>
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
