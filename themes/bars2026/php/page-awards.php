<?php
/**
 * Template Name: Awards
 * @package BARS2026
 */

get_header();

$edition = Editions::current();
$edition_number = Editions::romanNumerals($edition);
$from = Editions::from($edition);
$to = Editions::to($edition);
if ($from && $to) {
    $festival_dates = $from->format('j') . ' - ' . $to->format('j') . ' ' .
        ucfirst(getSpanishMonthName($to->format('F'))) . ' ' . $to->format('Y');
} else {
    $festival_dates = 'Fechas por confirmar';
}

$awards = Editions::getAwards($edition);
$juries = Editions::getJuries($edition);
?>

<?php get_template_part('template-parts/sections/page', 'hero', array(
    'title' => 'Premios y jurados',
    'subtitle' => 'Edición ' . $edition_number . ' • ' . $festival_dates,
)); ?>

<!-- Awards Intro Section -->
<section class="relative py-8 lg:py-12">
    <div class="max-w-[1000px] mx-auto px-5 lg:px-0">

        <?php if (empty($awards)): ?>
        <!-- Awards Not Defined Notice -->
        <div class="bg-bars-primary/20 border border-bars-primary/30 rounded-bars-md p-4 mb-8">
            <p class="text-sm text-bars-text-primary">
                Los premios y categorías de esta edición todavía se están definiendo
            </p>
        </div>
        <?php else: ?>

        <!-- Intro Text -->
        <p class="text-sm lg:text-base text-white/80 leading-[1.7] lg:leading-[1.8] mb-6 lg:mb-12">
            El festival otorga los siguientes premios a las realizaciones en competencia:
        </p>

        <!-- Awards Grid -->
        <div class="flex flex-col lg:flex-row gap-4 lg:gap-16">
            <?php foreach ($awards as $category): ?>
            <div class="flex-1 flex flex-col gap-3 lg:gap-6">
                <h3 class="font-heading text-lg lg:text-2xl font-semibold text-bars-text-primary">
                    <?php echo esc_html('🏆 ' . $category['heading']); ?>
                </h3>
                <ul class="flex flex-col gap-2 lg:gap-3">
                    <?php foreach ($category['items'] as $item): ?>
                    <li class="text-[13px] lg:text-sm text-white/60 leading-[1.6]"><?php echo esc_html('• ' . $item); ?></li>
                    <?php endforeach; ?>
                    <?php if (!empty($category['note'])): ?>
                    <li class="text-[11px] lg:text-xs text-white/40 italic leading-[1.6]"><?php echo esc_html($category['note']); ?></li>
                    <?php endif; ?>
                </ul>
            </div>
            <?php endforeach; ?>
        </div>

        <?php endif; ?>

    </div>
</section>

<!-- Jury Section -->
<section class="bg-bars-bg-medium pt-8 lg:pt-12 pb-[85px] lg:pb-[140px]">
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
        <!-- Juries Not Defined Notice -->
        <div class="bg-bars-primary/20 border border-bars-primary/30 rounded-bars-md p-4 mb-8">
            <p class="text-sm text-bars-text-primary">
                Los jurados de esta edición todavía no han sido seleccionados
            </p>
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
                            <svg class="w-8 h-8 lg:w-14 lg:h-14 text-bars-text-subtle" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
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
                bio: bioEl ? bioEl.innerHTML : ''
            }
        }));
    });
});
</script>

<?php get_footer(); ?>
