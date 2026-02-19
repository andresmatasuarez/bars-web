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
$poster = !empty($edition['poster']) ? $edition['poster'] : '';

// State detection for CTAs
$movieCount = countMovieEntriesForEdition($edition);
$callClosed = Editions::isCallClosed($edition);
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
        <div class="max-w-[1280px] w-full mx-auto <?php if ($poster): ?>lg:flex lg:items-center lg:gap-12<?php endif; ?>">
            <?php if ($poster): ?>
            <!-- Poster (desktop only) -->
            <div class="hidden lg:block lg:flex-shrink-0">
                <div class="relative">
                    <div class="absolute -inset-12 bg-bars-primary/15 rounded-full blur-3xl"></div>
                    <a href="<?php echo get_template_directory_uri() . '/' . esc_attr($poster); ?>" target="_blank" rel="noopener">
                        <img src="<?php echo get_template_directory_uri() . '/' . esc_attr($poster); ?>"
                             alt="Afiche BARS <?php echo esc_attr($edition_number); ?>"
                             class="relative w-[300px] rounded-bars-md border border-white/10 shadow-2xl cursor-pointer hover:scale-[1.02] transition-transform duration-300">
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <div class="<?php echo $poster ? 'lg:flex-1 lg:min-w-0' : 'max-w-[700px]'; ?>">
                <!-- Edition Badge -->
                <div class="inline-flex items-center gap-2 px-4 py-2 border border-bars-primary rounded-bars-sm mb-6 lg:mb-8">
                    <span class="text-[10px] lg:text-xs font-semibold tracking-[2px] text-bars-primary">
                        EDICIÓN <?php echo esc_html($edition_number); ?> • <?php echo esc_html(Editions::year($edition)); ?>
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
                        <?php echo bars_icon('ticket', 'w-5 h-5 text-bars-primary'); ?>
                        <span class="font-body text-sm lg:text-base font-medium tracking-wider text-bars-text-primary">
                            EDICIÓN <?php echo esc_html($edition_number); ?>
                        </span>
                    </div>

                    <!-- Dates - Calendar icon -->
                    <div class="flex items-center gap-2 whitespace-nowrap shrink-0">
                        <?php echo bars_icon('calendar', 'w-5 h-5 text-bars-primary'); ?>
                        <span class="font-body text-sm lg:text-base font-medium tracking-wider text-bars-text-primary">
                            <?php echo esc_html(strtoupper(Editions::datesLabel($edition))); ?>
                        </span>
                    </div>

                    <!-- Venues -->
                    <?php
                    $named_venues = array_filter($venues ?? [], function($v) { return isset($v['name']); });
                    ?>
                    <?php if (!empty($named_venues)): ?>
                        <?php foreach ($named_venues as $venueKey => $venue): ?>
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
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="flex items-center gap-2 whitespace-nowrap shrink-0">
                            <?php echo bars_icon('map-pin', 'w-5 h-5 text-bars-primary'); ?>
                            <span class="font-body text-sm lg:text-base font-medium tracking-wider text-bars-text-primary">
                                LUGAR A CONFIRMAR
                            </span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- CTAs -->
                <div class="flex flex-col sm:flex-row gap-3 lg:gap-4">
                    <?php if ($movieCount === "0" && !$callClosed): ?>
                        <!-- Call open: submission is the primary action -->
                        <a href="<?php echo home_url('/convocatoria'); ?>"
                           class="btn-primary text-center">
                            <?php echo bars_icon('megaphone', 'w-5 h-5'); ?>
                            Convocatoria abierta
                        </a>
                    <?php elseif ($movieCount === "0" && $callClosed): ?>
                        <!-- Transitional: selection in progress -->
                        <a href="<?php echo home_url('/programacion'); ?>"
                           class="btn-primary text-center">
                            <?php echo bars_icon('hourglass', 'w-5 h-5'); ?>
                            Selección en proceso
                        </a>
                        <span class="btn-ghost text-center pointer-events-none opacity-50">
                            <?php echo bars_icon('party-popper', 'w-5 h-5'); ?>
                            Convocatoria cerrada
                        </span>
                    <?php else: ?>
                        <!-- Programming live -->
                        <a href="<?php echo home_url('/programacion'); ?>"
                           class="btn-primary text-center">
                            <?php echo bars_icon('clapperboard', 'w-5 h-5'); ?>
                            Ver programación
                        </a>
                    <?php endif; ?>
                </div>

                <?php if ($poster): ?>
                <!-- Poster (mobile only) -->
                <div class="lg:hidden mt-8 flex justify-center">
                    <div class="relative">
                        <div class="absolute -inset-8 bg-bars-primary/15 rounded-full blur-2xl"></div>
                        <a href="<?php echo get_template_directory_uri() . '/' . esc_attr($poster); ?>" target="_blank" rel="noopener">
                            <img src="<?php echo get_template_directory_uri() . '/' . esc_attr($poster); ?>"
                                 alt="Afiche BARS <?php echo esc_attr($edition_number); ?>"
                                 class="relative w-[220px] rounded-bars-md border border-white/10 shadow-2xl cursor-pointer hover:scale-[1.02] transition-transform duration-300">
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</section>
