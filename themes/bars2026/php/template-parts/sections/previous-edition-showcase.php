<?php
/**
 * Previous Edition Showcase - Shows previous edition assets during transitional states
 * Renders when current edition is missing poster and/or spot
 * @package BARS2026
 */

$current = Editions::current();
$spot_url = !empty($current['spot']) ? $current['spot'] : '';

// When current has a spot, spot-section.php handles it; this template handles "no current spot" scenarios
if ($spot_url) return;

$poster = !empty($current['poster']) ? $current['poster'] : '';

$prev = Editions::getByNumber($current['number'] - 1);
if (!$prev) return;

$prev_poster = !empty($prev['poster']) ? $prev['poster'] : '';
$prev_spot = !empty($prev['spot']) ? $prev['spot'] : '';

// Determine what to display
$display_poster = (!$poster && $prev_poster) ? $prev_poster : null;
$display_spot_url = $prev_spot;

// Nothing to display at all
if (!$display_poster && !$display_spot_url) return;

// Extract YouTube video ID
$video_id = '';
if ($display_spot_url) {
    if (preg_match('/[?&]v=([^&]+)/', $display_spot_url, $matches)) {
        $video_id = $matches[1];
    } elseif (preg_match('/youtu\.be\/([^?]+)/', $display_spot_url, $matches)) {
        $video_id = $matches[1];
    }
}

$edition_label = 'EDICI&Oacute;N ' . esc_html(Editions::romanNumerals($prev)) . ' &middot; ' . esc_html(Editions::year($prev));

// Determine heading and caption
if ($display_poster && $video_id) {
    // Case 1: prev poster + prev spot
    $heading = 'Última edición';
    $caption = 'Afiche y spot correspondientes a la ' . $prev['number'] . 'ª edición (BARS ' . Editions::romanNumerals($prev) . ' · ' . Editions::year($prev) . ')';
} elseif (!$display_poster && $video_id) {
    // Prev spot only (current poster exists in hero)
    $heading = 'Spot última edición';
    $caption = 'Spot correspondiente a la ' . $prev['number'] . 'ª edición (BARS ' . Editions::romanNumerals($prev) . ' · ' . Editions::year($prev) . ')';
} else {
    // Poster only, no spot
    $heading = 'Última edición';
    $caption = 'Afiche de la última edición';
}

$thumbnail_url = $video_id ? 'https://img.youtube.com/vi/' . $video_id . '/maxresdefault.jpg' : '';
$has_both = $display_poster && $video_id;

$bg_class = ($GLOBALS['bars_section_index'] % 2 === 0) ? 'bg-bars-bg-dark' : 'bg-bars-bg-medium';
$GLOBALS['bars_section_index']++;
?>

<section class="<?php echo $bg_class; ?> py-16 lg:py-24 px-5 lg:px-20">
    <div class="<?php echo $has_both ? 'max-w-[1080px]' : 'max-w-[900px]'; ?> mx-auto">
        <!-- Section Header -->
        <div class="text-center mb-8 lg:mb-12">
            <p class="text-xs font-semibold tracking-[3px] uppercase text-bars-primary mb-3">
                <?php echo $edition_label; ?>
            </p>
            <h2 class="font-heading text-3xl lg:text-5xl font-medium text-bars-text-primary">
                <?php echo esc_html($heading); ?>
            </h2>
        </div>

        <?php if ($has_both): ?>
        <!-- Side-by-side layout: poster + spot -->
        <div class="flex flex-col items-center lg:grid lg:grid-cols-[auto_1fr] lg:items-stretch gap-8 lg:gap-12">
            <!-- Poster -->
            <div class="flex-shrink-0">
                <div class="relative">
                    <div class="absolute -inset-12 bg-bars-primary/15 rounded-full blur-3xl"></div>
                    <a href="<?php echo get_template_directory_uri() . '/' . esc_attr($display_poster); ?>" target="_blank" rel="noopener">
                        <img src="<?php echo get_template_directory_uri() . '/' . esc_attr($display_poster); ?>"
                             alt="Afiche BARS <?php echo esc_attr(Editions::romanNumerals($prev)); ?>"
                             class="relative w-[220px] lg:w-[280px] rounded-bars-md border border-white/10 shadow-2xl cursor-pointer hover:scale-[1.02] transition-transform duration-300">
                    </a>
                </div>
            </div>

            <!-- Spot -->
            <div class="w-full max-w-lg lg:max-w-none lg:relative lg:overflow-hidden">
                <div class="spot-video-container aspect-video lg:absolute lg:inset-y-0 lg:right-0 lg:w-auto rounded-bars-lg overflow-hidden relative cursor-pointer group"
                     data-video-id="<?php echo esc_attr($video_id); ?>">
                    <img src="<?php echo esc_url($thumbnail_url); ?>"
                         alt="Spot BARS <?php echo esc_attr(Editions::romanNumerals($prev)); ?>"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-black/40 group-hover:bg-black/30 transition-colors duration-300"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="relative">
                            <div class="absolute -inset-4 bg-bars-primary/30 rounded-full blur-xl group-hover:bg-bars-primary/50 transition-colors duration-300"></div>
                            <div class="relative w-16 h-16 lg:w-20 lg:h-20 bg-bars-primary rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                <svg class="w-7 h-7 lg:w-8 lg:h-8 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php elseif ($video_id): ?>
        <!-- Spot only (centered) -->
        <div class="spot-video-container aspect-video rounded-bars-lg overflow-hidden relative cursor-pointer group"
             data-video-id="<?php echo esc_attr($video_id); ?>">
            <img src="<?php echo esc_url($thumbnail_url); ?>"
                 alt="Spot BARS <?php echo esc_attr(Editions::romanNumerals($prev)); ?>"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
            <div class="absolute inset-0 bg-black/40 group-hover:bg-black/30 transition-colors duration-300"></div>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="relative">
                    <div class="absolute -inset-4 bg-bars-primary/30 rounded-full blur-xl group-hover:bg-bars-primary/50 transition-colors duration-300"></div>
                    <div class="relative w-16 h-16 lg:w-20 lg:h-20 bg-bars-primary rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg">
                        <svg class="w-7 h-7 lg:w-8 lg:h-8 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <?php else: ?>
        <!-- Poster only (centered) -->
        <div class="flex justify-center">
            <div class="relative">
                <div class="absolute -inset-12 bg-bars-primary/15 rounded-full blur-3xl"></div>
                <a href="<?php echo get_template_directory_uri() . '/' . esc_attr($display_poster); ?>" target="_blank" rel="noopener">
                    <img src="<?php echo get_template_directory_uri() . '/' . esc_attr($display_poster); ?>"
                         alt="Afiche BARS <?php echo esc_attr(Editions::romanNumerals($prev)); ?>"
                         class="relative w-[280px] lg:w-[340px] rounded-bars-md border border-white/10 shadow-2xl cursor-pointer hover:scale-[1.02] transition-transform duration-300">
                </a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Caption -->
        <p class="text-center text-sm text-bars-text-muted opacity-50 mt-4 lg:mt-6">
            <?php echo esc_html($caption); ?>
        </p>
        <p class="text-center text-sm text-bars-text-muted opacity-50 mt-1">
            Afiche y spot <?php echo esc_html(Editions::year($current)); ?> próximamente disponibles
        </p>
    </div>
</section>
