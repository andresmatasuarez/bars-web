<?php
/**
 * Sponsors Section
 * @package BARS2026
 */

$edition = Editions::current();
$sponsors = isset($edition['sponsors']) ? $edition['sponsors'] : array();
if (empty($sponsors)) return;

// Helper function to get size classes based on sponsor group
if (!function_exists('get_sponsor_size_classes')):
function get_sponsor_size_classes($title) {
    $title_lower = strtolower($title);
    if (strpos($title_lower, 'organiz') !== false) {
        return 'h-16 lg:h-20';
    } elseif (strpos($title_lower, 'auspic') !== false) {
        return 'h-[52px] lg:h-[65px]';
    } else {
        return 'h-10 lg:h-[50px]';
    }
}
endif;

// Helper function to get max-width class for the logos container
if (!function_exists('get_sponsor_container_classes')):
function get_sponsor_container_classes($title) {
    $title_lower = strtolower($title);
    if (strpos($title_lower, 'organiz') !== false) {
        return 'max-w-[600px]';
    } elseif (strpos($title_lower, 'auspic') !== false) {
        return 'max-w-[700px]';
    }
    return '';
}
endif;
?>

<section class="bg-gradient-to-b from-[#121212] to-bars-bg-elevated py-16 lg:py-20">
    <div class="max-w-[1280px] mx-auto px-5 lg:px-20">
        <!-- Header -->
        <div class="text-center mb-12 lg:mb-16">
            <h2 class="font-heading text-2xl lg:text-5xl font-medium text-bars-text-primary">
                El festival se realiza con el apoyo de
            </h2>
        </div>

        <!-- Sponsor Groups -->
        <div class="flex flex-col gap-12 lg:gap-16">
            <?php foreach ($sponsors as $group): ?>
                <?php if (!empty($group['items'])):
                    $size_classes = get_sponsor_size_classes($group['title']);
                    $container_classes = get_sponsor_container_classes($group['title']);
                ?>
                <div class="flex flex-col items-center gap-6">
                    <!-- Group Title -->
                    <h3 class="text-xs font-medium tracking-[3px] uppercase text-bars-text-muted">
                        <?php echo esc_html(strtoupper($group['title'])); ?>
                    </h3>

                    <!-- Logos -->
                    <div class="flex flex-wrap justify-center items-center gap-6 lg:gap-12 <?php echo $container_classes; ?>">
                        <?php foreach ($group['items'] as $sponsor): ?>
                            <?php if (!empty($sponsor['logo'])): ?>
                                <?php
                                $logo_url = $sponsor['logo'];
                                // Handle relative paths
                                if (strpos($logo_url, '/') === 0 || strpos($logo_url, 'resources/') === 0) {
                                    $logo_url = get_template_directory_uri() . '/' . ltrim($logo_url, '/');
                                }
                                ?>
                                <?php if (!empty($sponsor['href'])): ?>
                                    <a href="<?php echo esc_url($sponsor['href']); ?>"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="opacity-70 hover:opacity-100 transition-opacity">
                                        <img src="<?php echo esc_url($logo_url); ?>"
                                             alt="<?php echo esc_attr($sponsor['alt'] ?? ''); ?>"
                                             class="<?php echo $size_classes; ?> w-auto object-contain grayscale hover:grayscale-0 transition-all">
                                    </a>
                                <?php else: ?>
                                    <img src="<?php echo esc_url($logo_url); ?>"
                                         alt="<?php echo esc_attr($sponsor['alt'] ?? ''); ?>"
                                         class="<?php echo $size_classes; ?> w-auto object-contain opacity-70 grayscale">
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
