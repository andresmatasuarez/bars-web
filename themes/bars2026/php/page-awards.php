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

<!-- Awards Section -->
<section class="bg-bars-bg-dark py-12 lg:py-16">
    <div class="max-w-[1000px] mx-auto px-5 lg:px-0">

        <?php if (empty($awards)): ?>
        <!-- Awards Not Defined Notice -->
        <div class="bg-bars-primary/20 border border-bars-primary/30 rounded-bars-md p-4 mb-8">
            <p class="text-sm text-bars-text-primary">
                ⚠️ Los premios y categorías de esta edición todavía se están definiendo
            </p>
        </div>
        <?php else: ?>

        <!-- Section Heading -->
        <h2 class="font-heading text-2xl lg:text-[36px] font-medium text-bars-text-primary mb-6 lg:mb-8">
            Premios
        </h2>

        <!-- Intro Text -->
        <p class="text-sm lg:text-base text-bars-text-secondary leading-relaxed lg:leading-loose mb-8 lg:mb-10">
            El festival otorga los siguientes premios a las realizaciones en competencia:
        </p>

        <!-- Awards Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-12 lg:mb-16">
            <?php foreach ($awards as $category): ?>
            <div>
                <h3 class="font-heading text-lg lg:text-2xl font-semibold text-bars-text-primary mb-3">
                    🏆 <?php echo esc_html($category['heading']); ?>
                </h3>
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

        <!-- Juries Section -->
        <h2 class="font-heading text-2xl lg:text-[36px] font-medium text-bars-text-primary mb-6 lg:mb-8">
            Jurados
        </h2>

        <?php if (empty($juries)): ?>
        <!-- Juries Not Defined Notice -->
        <div class="bg-bars-primary/20 border border-bars-primary/30 rounded-bars-md p-4 mb-8">
            <p class="text-sm text-bars-text-primary">
                ⚠️ Los jurados de esta edición todavía no han sido seleccionados
            </p>
        </div>
        <?php else: ?>

        <div class="space-y-12 lg:space-y-16">
            <?php foreach ($juries as $sectionId => $juriesForSection): ?>
            <div>
                <!-- Section Label -->
                <h3 class="text-lg font-semibold text-bars-text-primary mb-6 lg:mb-8">
                    <?php echo esc_html(getJurySectionLabel($sectionId)); ?>
                </h3>

                <?php if (empty($juriesForSection)): ?>
                <p class="text-sm text-bars-text-muted italic">
                    Los jurados para esta sección todavía no han sido seleccionados
                </p>
                <?php else: ?>
                <!-- Jury Members Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
                    <?php foreach ($juriesForSection as $jury): ?>
                    <div class="bg-bars-bg-medium lg:bg-bars-bg-card rounded-bars-md overflow-hidden">
                        <!-- Jury Photo -->
                        <?php if (!empty($jury['thumbnail'])): ?>
                        <div class="aspect-square overflow-hidden">
                            <?php echo $jury['thumbnail']; ?>
                        </div>
                        <?php elseif (!empty($jury['pic']) && !empty($jury['pic']['url'])): ?>
                        <div class="aspect-square overflow-hidden">
                            <img src="<?php echo esc_url(get_template_directory_uri() . '/' . $jury['pic']['url']); ?>"
                                 alt="<?php echo esc_attr($jury['name']); ?>"
                                 class="w-full h-full object-cover" />
                        </div>
                        <?php else: ?>
                        <!-- Placeholder when no photo -->
                        <div class="aspect-square bg-bars-bg-elevated flex items-center justify-center">
                            <svg class="w-16 h-16 text-bars-text-subtle" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                        </div>
                        <?php endif; ?>

                        <!-- Jury Info -->
                        <div class="p-4 lg:p-5">
                            <h4 class="text-sm lg:text-base font-semibold text-bars-text-primary mb-2">
                                <?php echo esc_html($jury['name']); ?>
                            </h4>
                            <?php if (!empty($jury['description'])): ?>
                            <div class="text-xs lg:text-sm text-bars-text-muted leading-relaxed">
                                <?php echo wp_kses_post($jury['description']); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <?php endif; ?>

    </div>
</section>

<?php get_footer(); ?>
