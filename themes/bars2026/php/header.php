<?php
/**
 * Header template
 * @package BARS2026
 */

$edition_title = Editions::getTitle();

$nav_items = [
    ['slug' => 'noticias',     'label' => 'Noticias',          'mobile_label' => 'Noticias'],
    ['slug' => 'festival',     'label' => 'Festival',           'mobile_label' => 'Festival'],
    ['slug' => 'programacion', 'label' => 'Programación',      'mobile_label' => 'Programación'],
    ['slug' => 'premios',      'label' => 'Premios',            'mobile_label' => 'Premios'],
    ['slug' => 'convocatoria', 'label' => 'Convocatoria',      'mobile_label' => 'Convocatoria'],
    ['slug' => 'prensa',       'label' => 'Prensa',            'mobile_label' => 'Prensa'],
];
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php if (!current_theme_supports('title-tag')) : ?>
    <title><?php echo esc_html(bars_seo_get_title()); ?></title>
    <?php endif; ?>

    <?php wp_head(); ?>
</head>

<body <?php body_class('bg-bars-bg-dark text-bars-text-primary font-body antialiased'); ?>>
<?php if (function_exists('wp_body_open')) { wp_body_open(); } ?>

<!-- Header -->
<header class="fixed top-0 left-0 right-0 z-50 bg-bars-header backdrop-blur-sm h-16 lg:h-20 px-5 lg:px-20">
    <div class="flex items-center justify-between h-full max-w-[1280px] mx-auto">
        <!-- Logo -->
        <a href="<?php echo esc_url(home_url('/')); ?>" class="flex items-center gap-3">
            <img src="<?php echo get_template_directory_uri(); ?>/resources/bars_logo.png"
                 alt="BARS Logo"
                 class="w-8 h-8 lg:w-11 lg:h-11 object-contain">
            <span class="font-display text-xl lg:text-[28px] tracking-wider text-bars-text-primary">
                <?php echo esc_html($edition_title); ?>
            </span>
        </a>

        <!-- Desktop Navigation -->
        <nav class="hidden lg:flex items-center gap-10">
            <ul class="flex items-center gap-10">
                <?php foreach ($nav_items as $item):
                    if ($item['label'] === null) continue;
                    $is_active = is_page($item['slug']);
                ?>
                <li>
                    <?php if ($is_active): ?>
                    <a href="<?php echo home_url('/' . $item['slug']); ?>"
                       class="text-[13px] font-semibold tracking-wider uppercase text-white border-b-2 border-bars-primary pb-1 transition-colors">
                        <?php echo esc_html($item['label']); ?>
                    </a>
                    <?php else: ?>
                    <a href="<?php echo home_url('/' . $item['slug']); ?>"
                       class="text-[13px] font-medium tracking-wider uppercase text-bars-text-muted hover:text-white transition-colors">
                        <?php echo esc_html($item['label']); ?>
                    </a>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ul>
            <!-- TODO: unhide when ticket sales open -->
            <!-- <a href="<?php echo home_url('/entradas'); ?>"
               class="inline-flex items-center justify-center px-6 py-3 bg-bars-primary text-white text-[13px] font-semibold tracking-wider uppercase rounded-bars-sm hover:bg-[#A00000] transition-colors">
                Entradas
            </a> -->
        </nav>

        <!-- Mobile Menu Toggle -->
        <button id="mobile-menu-toggle"
                class="lg:hidden flex flex-col justify-center gap-[5px] p-2"
                aria-label="Menu"
                aria-expanded="false">
            <span class="block w-5 h-0.5 bg-bars-text-primary transition-transform"></span>
            <span class="block w-5 h-0.5 bg-bars-text-primary transition-opacity"></span>
            <span class="block w-5 h-0.5 bg-bars-text-primary transition-transform"></span>
        </button>
    </div>
</header>

<!-- Mobile Menu Overlay -->
<div id="mobile-menu-overlay"
     class="fixed inset-0 bg-black/80 z-40 opacity-0 pointer-events-none transition-opacity lg:hidden
            [.is-visible]:opacity-100 [.is-visible]:pointer-events-auto">
</div>

<!-- Mobile Menu -->
<nav id="mobile-menu"
     class="fixed top-16 left-0 right-0 bottom-0 z-40 bg-bars-bg-dark overflow-y-auto
            transform translate-x-full transition-transform lg:hidden
            [&.is-open]:translate-x-0">
    <div class="flex flex-col min-h-full px-5 py-8 gap-8">
        <!-- Main Navigation -->
        <div class="flex flex-col gap-6">
            <?php foreach ($nav_items as $item):
                $is_active = is_page($item['slug']);
            ?>
            <?php if ($is_active): ?>
            <a href="<?php echo home_url('/' . $item['slug']); ?>"
               class="flex items-center gap-3 font-heading text-[32px] font-medium text-white">
                <span class="block w-1 h-8 bg-bars-primary rounded-sm"></span>
                <?php echo esc_html($item['mobile_label']); ?>
            </a>
            <?php else: ?>
            <a href="<?php echo home_url('/' . $item['slug']); ?>"
               class="font-heading text-[32px] font-medium text-white/40">
                <?php echo esc_html($item['mobile_label']); ?>
            </a>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <!-- TODO: unhide when ticket sales open -->
        <!-- <a href="<?php echo home_url('/entradas'); ?>"
           class="btn-primary w-full text-center">
            Entradas
        </a> -->

        <!-- Social Links -->
        <div class="flex-1 flex items-end">
            <div class="flex gap-4">
                <a href="https://www.facebook.com/buenosairesrojosangre" target="_blank" rel="noopener noreferrer"
                   class="flex items-center justify-center w-11 h-11 rounded-full bg-white/10">
                    <?php echo bars_icon('facebook', 'w-5 h-5 text-white'); ?>
                </a>
                <a href="https://www.instagram.com/festivalrojosangre/" target="_blank" rel="noopener noreferrer"
                   class="flex items-center justify-center w-11 h-11 rounded-full bg-white/10">
                    <?php echo bars_icon('instagram', 'w-5 h-5 text-white'); ?>
                </a>
                <a href="https://www.youtube.com/user/rojosangrefestival" target="_blank" rel="noopener noreferrer"
                   class="flex items-center justify-center w-11 h-11 rounded-full bg-white/10">
                    <?php echo bars_icon('youtube', 'w-5 h-5 text-white'); ?>
                </a>
                <a href="https://x.com/rojosangre" target="_blank" rel="noopener noreferrer"
                   class="flex items-center justify-center w-11 h-11 rounded-full bg-white/10">
                    <?php echo bars_icon('x-twitter', 'w-5 h-5 text-white'); ?>
                </a>
                <a href="mailto:festivalrojosangre@gmail.com"
                   class="flex items-center justify-center w-11 h-11 rounded-full bg-white/10">
                    <?php echo bars_icon('mail', 'w-5 h-5 text-white'); ?>
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Main content wrapper - add padding-top for fixed header -->
<main id="main-content" class="pt-16 lg:pt-20">
