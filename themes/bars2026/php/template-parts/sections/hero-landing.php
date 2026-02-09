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
                <p class="font-heading text-lg lg:text-[28px] leading-relaxed text-bars-text-secondary mb-6 lg:mb-8">
                    Festival Internacional de Cine de Terror,<br>
                    Fantasía y Bizarro
                </p>

                <!-- Info Rows -->
                <div class="flex flex-col lg:flex-row lg:flex-nowrap gap-4 lg:gap-8 mb-8 lg:mb-10">
                    <!-- Edition - Clapperboard icon -->
                    <div class="flex items-center gap-2 whitespace-nowrap shrink-0">
                        <svg class="w-5 h-5 text-bars-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path d="M4 11v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8H4Z"/>
                            <path d="m4 11-.88-2.87a2 2 0 0 1 1.33-2.5l11.48-3.5a2 2 0 0 1 2.5 1.32l.87 2.87L4 11.01Z"/>
                            <path d="m6.6 4.99 3.38 4.2"/>
                            <path d="m11.86 3.38 3.38 4.2"/>
                        </svg>
                        <span class="font-body text-sm lg:text-base font-medium tracking-wider text-bars-text-primary">
                            EDICIÓN <?php echo esc_html($edition_number); ?>
                        </span>
                    </div>

                    <!-- Dates - Calendar icon -->
                    <?php if ($from && $to): ?>
                    <div class="flex items-center gap-2 whitespace-nowrap shrink-0">
                        <svg class="w-5 h-5 text-bars-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path d="M8 2v4"/>
                            <path d="M16 2v4"/>
                            <rect width="18" height="18" x="3" y="4" rx="2"/>
                            <path d="M3 10h18"/>
                        </svg>
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
                                <svg class="w-5 h-5 text-bars-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path d="M10 10V8a1 1 0 0 1 1.447-.894l4 2a1 1 0 0 1 0 1.788l-4 2A1 1 0 0 1 10 12V10z"/>
                                    <rect x="2" y="3" width="20" height="14" rx="2"/>
                                    <path d="M12 17v4"/>
                                    <path d="M8 21h8"/>
                                </svg>
                                <?php else: ?>
                                <!-- Physical venue - Map Pin icon -->
                                <svg class="w-5 h-5 text-bars-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
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
