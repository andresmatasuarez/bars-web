<?php
/**
 * Landing Page Template (Homepage)
 * @package BARS2026
 */

get_header();
$GLOBALS['bars_section_index'] = 0;
?>

<!-- Hero Section -->
<?php get_template_part('template-parts/sections/hero', 'landing'); ?>

<!-- Spot Section -->
<?php get_template_part('template-parts/sections/spot', 'section'); ?>

<!-- News Section -->
<?php $bg_class = ($GLOBALS['bars_section_index'] % 2 === 0) ? 'bg-bars-bg-dark' : 'bg-bars-bg-medium'; $GLOBALS['bars_section_index']++; ?>
<section class="<?php echo $bg_class; ?> py-16 lg:py-24 px-5 lg:px-20">
    <div class="max-w-[1280px] mx-auto">
        <!-- Section Header -->
        <div class="flex items-end justify-between mb-8 lg:mb-12">
            <div>
                <p class="text-xs font-semibold tracking-[3px] uppercase text-bars-primary mb-3">
                    NOVEDADES
                </p>
                <h2 class="font-heading text-3xl lg:text-5xl font-medium text-bars-text-primary">
                    Últimas Noticias
                </h2>
            </div>
        </div>

        <?php
        // Get recent posts
        $recent_posts = new WP_Query(array(
            'posts_per_page' => 4,
            'post_status' => 'publish',
        ));

        if ($recent_posts->have_posts()):
            $posts_array = $recent_posts->posts;
            $featured_post = $posts_array[0];
            $sidebar_posts = array_slice($posts_array, 1, 3);
        ?>
        <!-- News Grid: Main + Sidebar -->
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Main Featured News -->
            <div class="flex-1">
                <a href="<?php echo get_permalink($featured_post->ID); ?>" class="block group">
                    <?php if (has_post_thumbnail($featured_post->ID)): ?>
                        <div class="h-[300px] lg:h-[363px] overflow-hidden rounded-bars-md mb-6 bg-bars-bg-card">
                            <?php echo get_the_post_thumbnail($featured_post->ID, 'large', array(
                                'class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-300'
                            )); ?>
                        </div>
                    <?php else: ?>
                        <div class="h-[300px] lg:h-[363px] rounded-bars-md mb-6 bg-bars-bg-card"></div>
                    <?php endif; ?>

                    <time class="text-xs font-medium tracking-wider uppercase text-bars-text-subtle mb-3 block">
                        <?php echo getDateInSpanish(new DateTime(get_the_date('c', $featured_post->ID))); ?>
                    </time>
                    <h3 class="font-heading text-2xl lg:text-[32px] font-semibold leading-tight text-bars-text-primary group-hover:text-bars-badge-text transition-colors mb-4">
                        <?php echo get_the_title($featured_post->ID); ?>
                    </h3>
                    <p class="text-base text-bars-text-secondary leading-relaxed line-clamp-3">
                        <?php echo get_the_excerpt($featured_post->ID); ?>
                    </p>
                    <span class="text-[13px] font-semibold text-bars-badge-text lg:hidden mt-3 inline-block">
                        Seguir leyendo →
                    </span>
                </a>
            </div>

            <!-- Sidebar News -->
            <div class="w-full lg:w-[400px] flex flex-col gap-0">
                <?php foreach ($sidebar_posts as $post): ?>
                <a href="<?php echo get_permalink($post->ID); ?>" class="flex gap-4 border-b border-white/10 pb-6 pt-6 first:pt-0 group transition-colors">
                    <?php if (has_post_thumbnail($post->ID)): ?>
                        <div class="w-20 h-20 flex-shrink-0 overflow-hidden rounded-bars-sm bg-bars-bg-medium">
                            <?php echo get_the_post_thumbnail($post->ID, 'thumbnail', array(
                                'class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-300'
                            )); ?>
                        </div>
                    <?php else: ?>
                        <div class="w-20 h-20 flex-shrink-0 rounded-bars-sm bg-bars-bg-medium"></div>
                    <?php endif; ?>
                    <div class="flex flex-col gap-2">
                        <time class="text-[11px] font-medium tracking-wider uppercase text-bars-text-subtle">
                            <?php echo getDateInSpanish(new DateTime(get_the_date('c', $post->ID))); ?>
                        </time>
                        <h4 class="font-heading text-lg font-medium leading-tight text-bars-text-primary group-hover:text-bars-badge-text transition-colors line-clamp-2">
                            <?php echo get_the_title($post->ID); ?>
                        </h4>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
            wp_reset_postdata();
        else:
        ?>
            <p class="text-bars-text-muted text-center py-12">
                No hay noticias disponibles.
            </p>
        <?php endif; ?>
    </div>
</section>

<?php $metrics = getFestivalMetrics(); ?>
<!-- About Section -->
<?php $bg_class = ($GLOBALS['bars_section_index'] % 2 === 0) ? 'bg-bars-bg-dark' : 'bg-bars-bg-medium'; $GLOBALS['bars_section_index']++; ?>
<section class="<?php echo $bg_class; ?> pt-16 lg:pt-24 <?php echo $metrics['fallback'] ? 'pb-6 lg:pb-8' : 'pb-16 lg:pb-24'; ?> px-5 lg:px-20">
    <div class="max-w-[1280px] mx-auto text-center">
        <p class="text-xs font-semibold tracking-widest uppercase text-bars-primary mb-4">
            Sobre el Festival
        </p>
        <h2 class="font-heading text-3xl lg:text-5xl font-medium text-bars-text-primary mb-6">
            La cita anual con el cine de género
        </h2>
        <p class="text-base lg:text-lg text-bars-text-secondary leading-relaxed max-w-[800px] mx-auto mb-10 lg:mb-16">
            Buenos Aires Rojo Sangre es el festival más importante de cine de terror, fantasía y bizarro de Argentina y Latinoamérica. Desde 1999, celebramos el cine de género con la mejor selección de películas nacionales e internacionales.
        </p>

        <!-- Stats -->
        <div class="grid grid-cols-2 min-[360px]:flex min-[360px]:flex-wrap min-[360px]:justify-center gap-8 lg:gap-16 mb-10 lg:mb-16">
            <div class="text-center">
                <p class="font-display text-4xl lg:text-6xl text-bars-primary"><?php echo esc_html($metrics['editions']); ?><span class="text-xl lg:text-3xl relative -top-2.5 lg:-top-4">ª</span></p>
                <p class="text-sm text-bars-text-muted mt-1">Edición</p>
            </div>
            <div class="text-center">
                <p class="font-display text-4xl lg:text-6xl text-bars-primary"><?php echo esc_html($metrics['movies']); ?><span class="text-xl lg:text-3xl relative -top-2.5 lg:-top-4">+</span></p>
                <p class="text-sm text-bars-text-muted mt-1">Películas<?php if ($metrics['fallback']): ?><a href="#metrics-footnote" class="text-bars-text-muted">*</a><?php endif; ?></p>
            </div>
            <div class="text-center">
                <p class="font-display text-4xl lg:text-6xl text-bars-primary"><?php echo esc_html($metrics['short_films']); ?><span class="text-xl lg:text-3xl relative -top-2.5 lg:-top-4">+</span></p>
                <p class="text-sm text-bars-text-muted mt-1">Cortos<?php if ($metrics['fallback']): ?><a href="#metrics-footnote" class="text-bars-text-muted">*</a><?php endif; ?></p>
            </div>
            <div class="text-center">
                <p class="font-display text-4xl lg:text-6xl text-bars-primary"><?php echo esc_html($metrics['countries']); ?><span class="text-xl lg:text-3xl relative -top-2.5 lg:-top-4">+</span></p>
                <p class="text-sm text-bars-text-muted mt-1">Países<?php if ($metrics['fallback']): ?><a href="#metrics-footnote" class="text-bars-text-muted">*</a><?php endif; ?></p>
            </div>
        </div>

        <a href="<?php echo home_url('/festival'); ?>"
           class="text-base text-bars-link-accent hover:text-white transition-colors">
            Ver más →
        </a>
        <?php if ($metrics['fallback']): ?>
            <p id="metrics-footnote" class="text-xs text-bars-text-muted mt-20 lg:mt-24 italic opacity-50">
                <?php $fallback_edition = Editions::getByNumber($metrics['fallback_number']); ?>
                * Datos correspondientes a la <?php echo esc_html($metrics['fallback_number']); ?>ª edición (<?php echo esc_html(Editions::getTitle($fallback_edition)); ?> · <?php echo esc_html(Editions::year($fallback_edition)); ?>) - La programación <?php echo esc_html(Editions::current()['year']); ?> estará disponible próximamente
            </p>
        <?php endif; ?>
    </div>
</section>

<!-- Previous Edition Showcase -->
<?php get_template_part('template-parts/sections/previous-edition', 'showcase'); ?>

<?php get_footer(); ?>
