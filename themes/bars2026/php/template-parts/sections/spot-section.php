<?php
/**
 * Spot Section - YouTube video embed with click-to-play
 * @package BARS2026
 */

$edition = Editions::current();
$edition_number = Editions::romanNumerals($edition);
$spot_url = !empty($edition['spot']) ? $edition['spot'] : '';
if (!$spot_url) return;

// Extract YouTube video ID from URL
$video_id = '';
if (preg_match('/[?&]v=([^&]+)/', $spot_url, $matches)) {
    $video_id = $matches[1];
} elseif (preg_match('/youtu\.be\/([^?]+)/', $spot_url, $matches)) {
    $video_id = $matches[1];
}
if (!$video_id) return;

$thumbnail_url = 'https://img.youtube.com/vi/' . $video_id . '/maxresdefault.jpg';

$bg_class = ($GLOBALS['bars_section_index'] % 2 === 0) ? 'bg-bars-bg-dark' : 'bg-bars-bg-medium';
$GLOBALS['bars_section_index']++;
?>

<section class="<?php echo $bg_class; ?> py-16 lg:py-24 px-5 lg:px-20">
    <div class="max-w-[900px] mx-auto">
        <!-- Section Header -->
        <div class="text-center mb-8 lg:mb-12">
            <p class="text-xs font-semibold tracking-[3px] uppercase text-bars-primary mb-3">
                EDICI&Oacute;N <?php echo esc_html($edition_number); ?>
            </p>
            <h2 class="font-heading text-3xl lg:text-5xl font-medium text-bars-text-primary">
                Spot Oficial
            </h2>
        </div>

        <!-- Video Container -->
        <div class="spot-video-container aspect-video rounded-bars-lg overflow-hidden relative cursor-pointer group"
             data-video-id="<?php echo esc_attr($video_id); ?>">
            <!-- Thumbnail -->
            <img src="<?php echo esc_url($thumbnail_url); ?>"
                 alt="Spot oficial BARS <?php echo esc_attr($edition_number); ?>"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">

            <!-- Dark Overlay -->
            <div class="absolute inset-0 bg-black/40 group-hover:bg-black/30 transition-colors duration-300"></div>

            <!-- Play Button -->
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
</section>
