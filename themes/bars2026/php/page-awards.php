<?php
/**
 * Template Name: Awards
 * @package BARS2026
 */

// Preload the halftone banner image used by the jury modal so it's already
// in the browser cache when the modal opens — prevents decode jank during
// the opening CSS transition.
add_action('wp_head', function() {
    echo '<link rel="preload" as="image" href="' . esc_url(get_template_directory_uri() . '/resources/sala-halftone.png') . '">' . "\n";
}, 1);

get_header();

$edition = isset($_GET['edition'])
    ? Editions::getByNumber(intval($_GET['edition']))
    : Editions::current();
$edition_number = Editions::romanNumerals($edition);
$festival_dates = Editions::datesLabel($edition);

$awards = Editions::getAwards($edition);
$juries = Editions::getJuries($edition);

$isPastEdition = $edition['number'] !== Editions::current()['number'];

// Find previous edition with awards data for empty-state links
$prevEditionWithAwards = null;
if (empty($awards)) {
    $prevEdition = Editions::getByNumber($edition['number'] - 1);
    if ($prevEdition && Editions::getAwards($prevEdition)) {
        $prevEditionWithAwards = $prevEdition;
    }
}

$title = $isPastEdition
    ? 'Premios y jurados ' . $edition_number . ' (' . Editions::year($edition) . ')'
    : 'Premios y jurados';
?>

<?php get_template_part('template-parts/sections/page', 'hero', array(
    'title' => $title,
    'subtitle' => 'Edición ' . $edition_number . ' • ' . $festival_dates,
)); ?>

<!-- Awards Intro Section -->
<section class="relative min-h-96 py-8 lg:py-12">
    <div class="max-w-[1000px] mx-auto px-5 lg:px-0">

        <?php if ($isPastEdition): ?>
        <div class="pb-6">
            <a href="<?php echo esc_url(home_url('/premios')); ?>"
               class="text-sm text-bars-link-accent hover:underline">&larr; Ver premios de la edición actual</a>
        </div>
        <?php endif; ?>

        <?php if (empty($awards)): ?>
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <?php echo bars_icon('trophy', 'w-12 h-12 text-bars-icon-empty mb-4'); ?>
            <h3 class="font-heading text-2xl text-bars-text-primary mb-2">
                Premios en definición
            </h3>
            <p class="text-sm text-bars-text-subtle max-w-xs">
                Los premios y categorías de competencia de esta edición todavía se están definiendo.
            </p>
            <?php if ($prevEditionWithAwards): ?>
            <p class="text-sm text-bars-text-subtle text-center max-w-xs mx-auto mt-4">
                Mientras tanto, podés ver los
                <a href="<?php echo esc_url(home_url('/premios?edition=' . $prevEditionWithAwards['number'])); ?>"
                   class="text-bars-badge-text hover:underline">premios y jurados de la edición anterior (BARS <?php echo esc_html(Editions::romanNumerals($prevEditionWithAwards)); ?> - <?php echo esc_html(Editions::to($prevEditionWithAwards)->format('Y')); ?>)</a>.
            </p>
            <?php endif; ?>
        </div>
        <?php else: ?>

        <!-- Intro Text -->
        <p class="text-sm lg:text-base text-white/80 leading-[1.7] lg:leading-[1.8] mb-6 lg:mb-12">
            El festival otorga los siguientes premios a las realizaciones en competencia:
        </p>

        <!-- Awards List -->
        <div class="flex flex-col divide-y divide-bars-divider">
            <?php foreach ($awards as $category): ?>
            <div class="flex flex-col lg:flex-row gap-3 lg:gap-12 py-5 lg:py-6 first:pt-0 last:pb-0">
                <!-- Category Heading (left column on desktop) -->
                <h3 class="font-heading text-lg lg:text-xl font-semibold text-bars-text-primary lg:w-[220px] lg:shrink-0">
                    <?php echo esc_html($category['heading']); ?>
                </h3>
                <!-- Award Items (right column, 2-col grid on desktop) -->
                <div class="flex-1 flex flex-col gap-1">
                    <ul class="grid grid-cols-1 lg:grid-cols-2 gap-x-8 gap-y-2">
                        <?php foreach ($category['items'] as $item): ?>
                        <li class="text-[13px] lg:text-sm text-white/60 leading-[1.6] flex items-start gap-2">
                            <?php echo bars_icon('trophy', 'w-3.5 h-3.5 shrink-0 mt-[3px] opacity-60'); ?>
                            <?php echo esc_html($item); ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php if (!empty($category['note'])): ?>
                    <p class="text-[11px] lg:text-xs text-white/40 italic leading-[1.6] mt-1">
                        <?php echo esc_html($category['note']); ?>
                    </p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php endif; ?>

    </div>
</section>

<!-- Jury Section -->
<section class="relative bg-bars-bg-medium pt-8 lg:pt-12 pb-[85px] lg:pb-[140px]">
    <div class="max-w-[1000px] mx-auto px-5 lg:px-0">

        <!-- Jury Title Group -->
        <div class="flex flex-col gap-12 lg:gap-3 mb-12 lg:mb-14">
            <h2 class="font-heading text-[28px] lg:text-[36px] font-medium text-bars-text-primary">
                Jurados
            </h2>
            <p class="text-[13px] lg:text-base text-white/80 leading-[1.7] lg:leading-[1.8] max-w-[900px]">
                El jurado de cada sección está compuesto por figuras destacadas del cine, la producción y medios, con cierta predisposición al género o al ámbito cinematográfico. Son ellos quienes evalúan las películas presentadas y eligen a los ganadores de las diferentes categorías.
            </p>
        </div>

        <?php if (empty($juries)): ?>
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <?php echo bars_icon('scale', 'w-12 h-12 text-bars-icon-empty mb-4'); ?>
            <h3 class="font-heading text-2xl text-bars-text-primary mb-2">
                Jurados en definición
            </h3>
            <p class="text-sm text-bars-text-subtle max-w-xs">
                Los jurados de esta edición todavía no han sido seleccionados.
            </p>
            <?php if ($prevEditionWithAwards): ?>
            <p class="text-sm text-bars-text-subtle text-center max-w-xs mx-auto mt-4">
                Mientras tanto, podés ver los
                <a href="<?php echo esc_url(home_url('/premios?edition=' . $prevEditionWithAwards['number'])); ?>"
                   class="text-bars-badge-text hover:underline">premios y jurados de la edición anterior (BARS <?php echo esc_html(Editions::romanNumerals($prevEditionWithAwards)); ?> - <?php echo esc_html(Editions::to($prevEditionWithAwards)->format('Y')); ?>)</a>.
            </p>
            <?php endif; ?>
        </div>
        <?php else: ?>

        <div class="flex flex-col gap-12 lg:gap-14">
            <?php
            $jury_index = 0;
            $jury_count = count($juries);
            foreach ($juries as $sectionId => $juriesForSection):
                $jury_index++;
            ?>
            <div class="flex flex-col items-center gap-6 lg:gap-10">
                <!-- Section Label -->
                <h3 class="text-sm lg:text-xl font-medium text-bars-text-primary tracking-[0.5px] text-center w-full">
                    <?php echo esc_html(getJurySectionLabel($sectionId)); ?>
                </h3>

                <?php if (empty($juriesForSection)): ?>
                <p class="text-sm text-bars-text-muted italic">
                    Los jurados para esta sección todavía no han sido seleccionados
                </p>
                <?php else: ?>
                <!-- Jury Members Row -->
                <div class="flex flex-row gap-3 lg:gap-8 w-full">
                    <?php foreach ($juriesForSection as $jury):
                        // Resolve photo URL for the modal
                        $photoUrl = '';
                        if (!empty($jury['thumbnail'])) {
                            if (preg_match('/src=["\']([^"\']+)["\']/', $jury['thumbnail'], $matches)) {
                                $photoUrl = $matches[1];
                            }
                        } elseif (!empty($jury['pic']) && !empty($jury['pic']['url'])) {
                            $photoUrl = get_template_directory_uri() . '/' . $jury['pic']['url'];
                        }
                    ?>
                    <?php $has_bio = !empty($jury['description']); ?>
                    <div class="flex flex-col items-center gap-2 lg:gap-4 flex-1<?php if ($has_bio): ?> group cursor-pointer jury-card-toggle<?php endif; ?>"
                        <?php if ($has_bio): ?>
                         data-jury-id="<?php echo esc_attr($jury['postId']); ?>"
                         data-jury-name="<?php echo esc_attr($jury['name']); ?>"
                         data-jury-section="<?php echo esc_attr(getJurySectionLabel($sectionId)); ?>"
                         data-jury-photo="<?php echo esc_url($photoUrl); ?>"
                         data-jury-slug="<?php echo esc_attr(!empty($jury['postId']) ? get_post_field('post_name', $jury['postId']) : sanitize_title($jury['name'])); ?>"
                        <?php endif; ?>>
                        <!-- Jury Photo (circular) -->
                        <?php if (!empty($jury['thumbnail'])): ?>
                        <div class="w-[80px] h-[80px] lg:w-[140px] lg:h-[140px] rounded-full overflow-hidden shrink-0 [&_img]:w-full [&_img]:h-full [&_img]:object-cover<?php if ($has_bio): ?> brightness-75 grayscale-[0.6] group-hover:brightness-100 group-hover:grayscale-0 transition-[filter] duration-300<?php endif; ?>">
                            <?php echo $jury['thumbnail']; ?>
                        </div>
                        <?php elseif (!empty($jury['pic']) && !empty($jury['pic']['url'])): ?>
                        <div class="w-[80px] h-[80px] lg:w-[140px] lg:h-[140px] rounded-full overflow-hidden shrink-0<?php if ($has_bio): ?> brightness-75 grayscale-[0.6] group-hover:brightness-100 group-hover:grayscale-0 transition-[filter] duration-300<?php endif; ?>">
                            <img src="<?php echo esc_url(get_template_directory_uri() . '/' . $jury['pic']['url']); ?>"
                                 alt="<?php echo esc_attr($jury['name']); ?>"
                                 class="w-full h-full object-cover" />
                        </div>
                        <?php else: ?>
                        <!-- Placeholder when no photo -->
                        <div class="w-[80px] h-[80px] lg:w-[140px] lg:h-[140px] rounded-full bg-bars-bg-card flex items-center justify-center shrink-0">
                            <?php echo bars_icon('user', 'w-8 h-8 lg:w-14 lg:h-14 text-bars-text-subtle'); ?>
                        </div>
                        <?php endif; ?>

                        <!-- Jury Info -->
                        <div class="flex flex-col items-center gap-1 lg:gap-1">
                            <h4 class="text-xs lg:text-2xl font-medium lg:font-semibold text-bars-text-primary text-center lg:font-heading w-full lg:max-w-[240px]<?php if ($has_bio): ?> transition-colors duration-300 group-hover:text-bars-badge-text<?php endif; ?>">
                                <?php echo esc_html($jury['name']); ?>
                            </h4>
                            <?php if ($has_bio): ?>
                            <span class="text-[10px] font-medium text-bars-badge-text lg:hidden">
                                Ver bio &rarr;
                            </span>
                            <div class="hidden jury-bio-content">
                                <?php echo wp_kses_post($jury['description']); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <?php if ($jury_index < $jury_count): ?>
            <!-- Divider between jury groups -->
            <div class="h-px w-full bg-[#FFFFFF15]"></div>
            <?php endif; ?>

            <?php endforeach; ?>
        </div>

        <?php endif; ?>

    </div>
</section>

<div id="jury-modal-root"></div>

<script>
document.querySelectorAll('.jury-card-toggle').forEach(function(el) {
    el.addEventListener('click', function() {
        var id = this.getAttribute('data-jury-id');
        var bioEl = this.querySelector('.jury-bio-content');
        document.dispatchEvent(new CustomEvent('jury-modal:open', {
            detail: {
                id: parseInt(id, 10),
                name: this.getAttribute('data-jury-name'),
                section: this.getAttribute('data-jury-section'),
                photoUrl: this.getAttribute('data-jury-photo') || '',
                slug: this.getAttribute('data-jury-slug') || '',
                bio: bioEl ? bioEl.innerHTML : ''
            }
        }));
    });
});
</script>

<?php get_footer(); ?>
