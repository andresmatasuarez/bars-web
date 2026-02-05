<?php
/**
 * Main template file (fallback)
 *
 * @package BARS2026
 */

get_header();
?>

<div class="bg-bars-bg-dark min-h-screen">
    <!-- Page Hero -->
    <section class="bg-bars-bg-medium py-10 lg:py-16">
        <div class="max-w-[1000px] mx-auto px-5 lg:px-0">
            <?php if (is_home() && !is_front_page()): ?>
                <h1 class="font-display text-4xl lg:text-6xl tracking-wider text-bars-text-primary">
                    <?php single_post_title(); ?>
                </h1>
            <?php elseif (is_archive()): ?>
                <h1 class="font-display text-4xl lg:text-6xl tracking-wider text-bars-text-primary">
                    <?php the_archive_title(); ?>
                </h1>
            <?php elseif (is_search()): ?>
                <h1 class="font-display text-4xl lg:text-6xl tracking-wider text-bars-text-primary">
                    Búsqueda: <?php echo get_search_query(); ?>
                </h1>
            <?php else: ?>
                <h1 class="font-display text-4xl lg:text-6xl tracking-wider text-bars-text-primary">
                    Noticias
                </h1>
            <?php endif; ?>
        </div>
    </section>

    <!-- Content -->
    <section class="py-12 lg:py-16">
        <div class="max-w-[1000px] mx-auto px-5 lg:px-0">
            <?php if (have_posts()): ?>
                <div class="space-y-8">
                    <?php while (have_posts()): the_post(); ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('group'); ?>>
                            <a href="<?php the_permalink(); ?>" class="block">
                                <?php if (has_post_thumbnail()): ?>
                                    <div class="aspect-video overflow-hidden rounded-bars-md mb-4 bg-bars-bg-card">
                                        <?php the_post_thumbnail('news-featured', array(
                                            'class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-300'
                                        )); ?>
                                    </div>
                                <?php endif; ?>

                                <time class="text-xs text-bars-text-muted mb-2 block">
                                    <?php echo get_the_date('j M Y'); ?>
                                </time>
                                <h2 class="font-heading text-xl lg:text-2xl font-medium text-bars-text-primary group-hover:text-white transition-colors mb-2">
                                    <?php the_title(); ?>
                                </h2>
                                <div class="text-sm text-bars-text-secondary line-clamp-3">
                                    <?php the_excerpt(); ?>
                                </div>
                            </a>
                        </article>
                    <?php endwhile; ?>
                </div>

                <!-- Pagination -->
                <div class="mt-12 flex justify-center">
                    <?php
                    the_posts_pagination(array(
                        'prev_text' => '← Anterior',
                        'next_text' => 'Siguiente →',
                        'class' => 'flex gap-2',
                    ));
                    ?>
                </div>
            <?php else: ?>
                <p class="text-bars-text-muted text-center py-12">
                    No se encontró contenido.
                </p>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php get_footer(); ?>
