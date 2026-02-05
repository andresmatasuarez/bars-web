<?php
/**
 * Page Hero Section
 *
 * Shared heading section used by all inner pages (not the landing).
 * Ensures consistent height across Convocatoria, Prensa, and future pages.
 *
 * @param array $args {
 *     @type string $title    Page title (required)
 *     @type string $subtitle Subtitle line (optional)
 * }
 * @package BARS2026
 */

$title = isset($args['title']) ? $args['title'] : '';
$subtitle = isset($args['subtitle']) ? $args['subtitle'] : '';
?>

<section class="bg-bars-bg-medium py-10 lg:py-16">
    <div class="max-w-[1000px] mx-auto px-5 lg:px-0">
        <h1 class="font-display text-[40px] lg:text-[64px] tracking-[2px] text-bars-text-primary">
            <?php echo esc_html($title); ?>
        </h1>
        <?php if ($subtitle): ?>
        <p class="text-[13px] lg:text-base text-bars-text-muted mt-3 lg:mt-4">
            <?php echo esc_html($subtitle); ?>
        </p>
        <?php endif; ?>
    </div>
</section>
