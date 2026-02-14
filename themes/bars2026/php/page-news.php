<?php
/**
 * Template Name: News
 * @package BARS2026
 */

get_header();

// Pagination
$paged = get_query_var('paged') ? get_query_var('paged') : 1;

// Query published posts, 6 per page
$news_query = new WP_Query(array(
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => 6,
    'paged'          => $paged,
));
?>

<?php get_template_part('template-parts/sections/page', 'hero', array(
    'title' => 'Noticias',
    'subtitle' => 'Últimas novedades del festival',
)); ?>

<!-- News Section -->
<section class="relative py-16 px-5 lg:px-0">
    <div class="max-w-[1000px] mx-auto">
        <?php if ($news_query->have_posts()):
            $posts_array = $news_query->posts;
            $is_first_page = ($paged === 1);
        ?>

        <!-- Desktop Grid (lg+) -->
        <div class="hidden lg:flex flex-col gap-12">
            <?php
            $chunks = array_chunk($posts_array, 3);
            foreach ($chunks as $row):
            ?>
            <div class="grid grid-cols-3 gap-8" style="height: 420px">
                <?php foreach ($row as $post): setup_postdata($post); ?>
                <article class="bg-[#0F0F0F] rounded-lg overflow-hidden flex flex-col">
                    <a href="<?php echo get_permalink($post->ID); ?>" class="flex flex-col h-full group">
                        <?php if (has_post_thumbnail($post->ID)): ?>
                        <div class="h-[200px] overflow-hidden rounded-t-lg bg-[#1A1A1A] flex-shrink-0">
                            <?php echo get_the_post_thumbnail($post->ID, 'medium_large', array(
                                'class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-300',
                            )); ?>
                        </div>
                        <?php else: ?>
                        <div class="h-[200px] rounded-t-lg bg-[#1A1A1A] flex-shrink-0"></div>
                        <?php endif; ?>

                        <div class="flex flex-col justify-between flex-1 px-5 pb-6 pt-4 gap-3">
                            <div class="flex flex-col gap-3">
                                <time class="text-xs text-bars-text-subtle tracking-[0.5px]">
                                    <?php echo esc_html(getDateInSpanish(new DateTime(get_the_date('c', $post->ID)))); ?>
                                </time>
                                <h2 class="font-heading text-2xl font-semibold leading-[1.3] text-bars-text-primary line-clamp-2 group-hover:text-bars-badge-text transition-colors duration-300">
                                    <?php echo get_the_title($post->ID); ?>
                                </h2>
                                <p class="text-sm text-white/60 leading-[1.6] line-clamp-3">
                                    <?php echo get_the_excerpt($post->ID); ?>
                                </p>
                            </div>
                            <span class="text-[13px] font-semibold text-bars-badge-text lg:hidden">
                                Seguir leyendo →
                            </span>
                        </div>
                    </a>
                </article>
                <?php endforeach; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Mobile Layout -->
        <div class="flex flex-col gap-5 lg:hidden">
            <?php foreach ($posts_array as $index => $post): setup_postdata($post); ?>
                <?php if ($index === 0 && $is_first_page): ?>
                <!-- Featured first card -->
                <article class="pb-6">
                    <a href="<?php echo get_permalink($post->ID); ?>" class="flex flex-col gap-3 group">
                        <?php if (has_post_thumbnail($post->ID)): ?>
                        <div class="h-[180px] overflow-hidden rounded-lg bg-[#1A1A1A]">
                            <?php echo get_the_post_thumbnail($post->ID, 'medium_large', array(
                                'class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-300',
                            )); ?>
                        </div>
                        <?php else: ?>
                        <div class="h-[180px] rounded-lg bg-[#1A1A1A]"></div>
                        <?php endif; ?>

                        <div class="flex flex-col gap-2">
                            <time class="text-[10px] tracking-[1px] text-white/40">
                                <?php echo esc_html(strtoupper(getDateInSpanish(new DateTime(get_the_date('c', $post->ID))))); ?>
                            </time>
                            <h2 class="text-base font-semibold leading-[1.4] text-bars-text-primary">
                                <?php echo get_the_title($post->ID); ?>
                            </h2>
                            <p class="text-[13px] text-white/60 leading-[1.6]">
                                <?php echo get_the_excerpt($post->ID); ?>
                            </p>
                            <span class="text-[13px] font-semibold text-bars-badge-text mt-1">
                                Seguir leyendo →
                            </span>
                        </div>
                    </a>
                </article>
                <?php else: ?>
                <!-- Compact card -->
                <article>
                    <a href="<?php echo get_permalink($post->ID); ?>" class="flex items-center gap-3 group">
                        <?php if (has_post_thumbnail($post->ID)): ?>
                        <div class="w-[100px] h-[80px] flex-shrink-0 overflow-hidden rounded-md bg-[#1A1A1A]">
                            <?php echo get_the_post_thumbnail($post->ID, 'thumbnail', array(
                                'class' => 'w-full h-full object-cover',
                            )); ?>
                        </div>
                        <?php else: ?>
                        <div class="w-[100px] h-[80px] flex-shrink-0 rounded-md bg-[#1A1A1A]"></div>
                        <?php endif; ?>

                        <div class="flex flex-col gap-1.5">
                            <time class="text-[10px] tracking-[1px] text-white/40">
                                <?php echo esc_html(strtoupper(getDateInSpanish(new DateTime(get_the_date('c', $post->ID))))); ?>
                            </time>
                            <h2 class="text-sm font-medium leading-[1.4] text-bars-text-primary line-clamp-2">
                                <?php echo get_the_title($post->ID); ?>
                            </h2>
                        </div>
                    </a>
                </article>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($news_query->max_num_pages > 1): ?>
        <nav class="flex items-center justify-center gap-2 mt-12">
            <?php
            $pagination = paginate_links(array(
                'total'     => $news_query->max_num_pages,
                'current'   => $paged,
                'type'      => 'array',
                'prev_text' => '<span class="hidden lg:inline">← Anterior</span><span class="lg:hidden">←</span>',
                'next_text' => '<span class="hidden lg:inline">Siguiente →</span><span class="lg:hidden">→</span>',
                'mid_size'  => 1,
            ));

            if ($pagination):
                foreach ($pagination as $page_link):
                    // Check if it's the current page
                    if (strpos($page_link, 'current') !== false):
            ?>
                        <span class="inline-flex items-center justify-center rounded text-[13px] lg:text-[13px] font-semibold text-white bg-bars-primary px-3.5 py-2.5 lg:px-3.5 lg:py-2.5">
                            <?php echo strip_tags($page_link); ?>
                        </span>
            <?php
                    else:
                        // Add styling to links (prev/next and page numbers)
                        $styled_link = str_replace(
                            '<a ',
                            '<a class="inline-flex items-center justify-center rounded text-[13px] lg:text-[13px] font-medium text-white/60 bg-[#1A1A1A] px-3 py-2 lg:px-3.5 lg:py-2.5 hover:bg-[#252525] transition-colors" ',
                            $page_link
                        );
                        echo $styled_link;
                    endif;
                endforeach;
            endif;
            ?>
        </nav>
        <?php endif; ?>

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

<?php get_footer(); ?>
