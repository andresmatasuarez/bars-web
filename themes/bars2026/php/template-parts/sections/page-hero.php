<?php
/**
 * Page Hero Section
 *
 * Shared heading section used by all inner pages (not the landing).
 * Dark background with photo overlay and gradient fade.
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

<section class="relative bg-bars-bg-dark overflow-hidden">
    <!-- Background Image + Gradient -->
    <div class="absolute inset-0">
        <img src="<?php echo get_template_directory_uri(); ?>/resources/sala2-halftone.png"
             alt=""
             class="w-full h-full object-cover opacity-90">
        <div class="absolute inset-0" style="background: linear-gradient(to bottom, transparent 0%, var(--color-bars-bg-dark) 95%)"></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 pt-8 pb-4">
        <div class="max-w-[1000px] mx-auto px-5 lg:px-0 flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
            <h1 class="font-display text-[40px] tracking-[2px] leading-[0.95] text-bars-text-primary">
                <?php echo esc_html($title); ?>
            </h1>
            <?php if ($subtitle): ?>
            <span class="hidden lg:block flex-1 border-b border-bars-text-muted/20 mb-1.5" aria-hidden="true"></span>
            <p class="text-sm text-bars-text-muted lg:self-end">
                <?php echo esc_html($subtitle); ?>
            </p>
            <?php endif; ?>
        </div>
    </div>
</section>
