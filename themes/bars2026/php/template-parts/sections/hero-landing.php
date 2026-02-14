<?php
/**
 * Hero Section - Landing Page
 * @package BARS2026
 */

$edition = Editions::current();
$edition_number = Editions::romanNumerals($edition);
$from = Editions::from($edition);
$to = Editions::to($edition);
$venues = Editions::venues($edition);
?>

<section class="relative min-h-[600px] lg:h-[800px] overflow-hidden">
    <!-- Background Image -->
    <div class="absolute inset-0 bg-black">
        <img src="<?php echo get_template_directory_uri(); ?>/resources/sala-halftone.png"
             alt=""
             class="w-full h-full object-cover grayscale-[0.65]">
        <div class="absolute inset-0 bg-gradient-to-b from-[#150808]/50 via-transparent to-[#150808]/50"></div>
    </div>

    <!-- Overlay Gradient -->
    <div class="absolute inset-0 bg-gradient-to-b from-bars-bg-dark via-transparent via-40% to-bars-bg-dark"></div>

    <!-- Content -->
    <div class="relative z-10 h-full flex flex-col justify-center px-5 lg:px-20 py-16 lg:py-20">
        <div class="max-w-[1280px] w-full mx-auto">
            <div class="max-w-[700px]">
                <!-- Edition Badge -->
                <div class="inline-flex items-center gap-2 px-4 py-2 border border-bars-primary rounded-bars-sm mb-6 lg:mb-8">
                    <span class="text-[10px] lg:text-xs font-semibold tracking-[2px] text-bars-primary">
                        EDICIÓN <?php echo esc_html($edition_number); ?> • <?php echo $to ? $to->format('Y') : date('Y'); ?>
                    </span>
                </div>

                <!-- Title -->
                <h1 class="font-display text-[48px] lg:text-[120px] leading-[0.9] tracking-wider text-bars-text-primary mb-4 lg:mb-6">
                    BUENOS AIRES<br><span class="hero-title-highlight text-bars-primary">ROJO SANGRE</span>
                </h1>

                <!-- Subtitle -->
                <p class="font-heading text-lg lg:text-[28px] leading-relaxed text-bars-text-secondary mb-6 lg:mb-8 lg:max-w-[520px]">
                    Festival Internacional de Cine de Terror, Fantasía y Bizarro
                </p>

                <!-- Info Rows -->
                <div class="flex flex-col lg:flex-row lg:flex-nowrap gap-4 lg:gap-8 mb-8 lg:mb-10">
                    <!-- Edition - Clapperboard icon -->
                    <div class="flex items-center gap-2 whitespace-nowrap shrink-0">
                        <?php echo bars_icon('clapperboard', 'w-5 h-5 text-bars-primary'); ?>
                        <span class="font-body text-sm lg:text-base font-medium tracking-wider text-bars-text-primary">
                            EDICIÓN <?php echo esc_html($edition_number); ?>
                        </span>
                    </div>

                    <!-- Dates - Calendar icon -->
                    <?php if ($from && $to): ?>
                    <div class="flex items-center gap-2 whitespace-nowrap shrink-0">
                        <?php echo bars_icon('calendar', 'w-5 h-5 text-bars-primary'); ?>
                        <span class="font-body text-sm lg:text-base font-medium tracking-wider text-bars-text-primary">
                            <?php echo $from->format('j'); ?> - <?php echo $to->format('j'); ?> <?php echo strtoupper(getSpanishMonthName($to->format('F'))); ?> <?php echo $to->format('Y'); ?>
                        </span>
                    </div>
                    <?php endif; ?>

                    <!-- Venues -->
                    <?php if (!empty($venues)): ?>
                        <?php foreach ($venues as $venueKey => $venue): ?>
                            <?php if (isset($venue['name'])): ?>
                            <div class="flex items-center gap-2 whitespace-nowrap shrink-0">
                                <?php if (!empty($venue['online'])): ?>
                                <!-- Online venue - Monitor Play icon -->
                                <?php echo bars_icon('monitor-play', 'w-5 h-5 text-bars-primary'); ?>
                                <?php else: ?>
                                <!-- Physical venue - Map Pin icon -->
                                <?php echo bars_icon('map-pin', 'w-5 h-5 text-bars-primary'); ?>
                                <?php endif; ?>
                                <span class="font-body text-sm lg:text-base font-medium tracking-wider text-bars-text-primary">
                                    <?php echo esc_html(strtoupper($venue['name'])); ?>
                                </span>
                            </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- CTAs -->
                <div class="flex flex-col sm:flex-row gap-3 lg:gap-4">
                    <a href="<?php echo home_url('/programacion'); ?>"
                       class="btn-primary text-center">
                        Ver programación
                    </a>
                    <a href="<?php echo home_url('/convocatoria'); ?>"
                       class="btn-ghost text-center">
                        Convocatoria abierta
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
