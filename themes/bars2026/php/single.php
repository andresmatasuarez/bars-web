<?php
/**
 * Single Post Template
 * @package BARS2026
 */

get_header();

$post_date = new DateTime(get_the_date('c'));
$date_spanish = getDateInSpanish($post_date);
$thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'large');
$excerpt = $post->post_excerpt;
?>

<?php while (have_posts()): the_post(); ?>

<!-- Article Hero - Desktop -->
<section class="hidden lg:flex relative justify-center items-end bg-bars-bg-dark overflow-hidden min-h-[320px]"
         style="padding: 40px 0 48px 0;">
    <?php if ($thumbnail_url): ?>
    <div class="absolute inset-0">
        <img src="<?php echo esc_url($thumbnail_url); ?>"
             alt=""
             class="w-full h-full object-cover opacity-45">
    </div>
    <div class="absolute inset-0 bg-bars-bg-dark/25"></div>
    <div class="absolute inset-0" style="background: linear-gradient(to top, #0A0A0A 18%, transparent 85%);"></div>
    <?php endif; ?>

    <div class="relative flex flex-col justify-between w-full max-w-[1000px] px-5 lg:px-0 min-h-[220px]">
        <!-- Back link -->
        <a href="<?php echo esc_url(home_url('/noticias')); ?>"
           onclick="if(document.referrer&&document.referrer.indexOf('/noticias')!==-1){history.back();return false;}"
           class="inline-flex items-center gap-2 py-2 text-sm font-medium text-white/60 hover:text-white transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver a noticias
        </a>

        <div class="flex flex-col gap-4 mt-auto">
            <!-- Date -->
            <time class="text-[13px] font-normal text-bars-badge-text tracking-[1px]">
                <?php echo esc_html($date_spanish); ?>
            </time>

            <!-- Title -->
            <h1 class="font-display text-[64px] tracking-[2px] text-white leading-none">
                <?php the_title(); ?>
            </h1>

            <?php if ($excerpt): ?>
            <!-- Subtitle (excerpt) -->
            <p class="text-lg text-white/60">
                <?php echo esc_html($excerpt); ?>
            </p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Article Hero - Mobile -->
<section class="lg:hidden relative h-[220px]">
    <?php if ($thumbnail_url): ?>
    <img src="<?php echo esc_url($thumbnail_url); ?>"
         alt=""
         class="absolute inset-0 w-full h-full object-cover">
    <div class="absolute inset-0" style="background: linear-gradient(to bottom, #0A0A0A 0%, transparent 50%);"></div>
    <?php else: ?>
    <div class="absolute inset-0 bg-bars-bg-dark"></div>
    <?php endif; ?>

    <!-- Back link -->
    <a href="<?php echo esc_url(home_url('/noticias')); ?>"
       onclick="if(document.referrer&&document.referrer.indexOf('/noticias')!==-1){history.back();return false;}"
       class="absolute top-5 left-5 inline-flex items-center gap-1.5 text-sm font-medium text-white hover:text-white/80 transition-colors z-10">
        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
        Noticias
    </a>
</section>

<!-- Article Content -->
<section class="bg-bars-bg-dark">
    <div class="max-w-[1000px] mx-auto px-5 lg:px-0 pt-6 pb-20 lg:pt-2 lg:pb-16">

        <!-- Mobile: Date + Title (shown below hero on mobile) -->
        <div class="lg:hidden flex flex-col gap-5 mb-5">
            <time class="text-[11px] font-semibold text-bars-badge-text tracking-[1px] uppercase">
                <?php echo esc_html(strtoupper($date_spanish)); ?>
            </time>
            <h1 class="font-heading text-[26px] font-semibold leading-[1.3] text-white">
                <?php the_title(); ?>
            </h1>
        </div>

        <!-- Featured Image (desktop only) -->
        <?php if ($thumbnail_url): ?>
        <div class="hidden lg:block mb-10">
            <img src="<?php echo esc_url($thumbnail_url); ?>"
                 alt="<?php echo esc_attr(get_the_title()); ?>"
                 class="w-full h-auto rounded-bars-md">
        </div>
        <?php endif; ?>

        <!-- Article Body -->
        <div class="article-content">
            <?php the_content(); ?>
        </div>

        <!-- Prev/Next Navigation -->
        <?php
        $prev_post = get_previous_post();
        $next_post = get_next_post();

        if ($prev_post || $next_post):
        ?>
        <nav class="mt-10 lg:mt-16">
            <!-- Desktop Nav -->
            <div class="hidden lg:block">
                <div class="h-px bg-bars-divider mb-6"></div>
                <div class="flex justify-between items-start gap-8">
                    <!-- Previous Article -->
                    <div class="flex-1">
                        <?php if ($prev_post):
                            $prev_date = new DateTime(get_the_date('c', $prev_post->ID));
                        ?>
                        <a href="<?php echo esc_url(get_permalink($prev_post->ID)); ?>"
                           class="flex items-center gap-5 group">
                            <?php if (has_post_thumbnail($prev_post->ID)): ?>
                            <div class="w-[160px] h-[100px] flex-shrink-0 overflow-hidden rounded-bars-sm bg-bars-bg-card">
                                <?php echo get_the_post_thumbnail($prev_post->ID, 'thumbnail', array(
                                    'class' => 'w-full h-full object-cover',
                                )); ?>
                            </div>
                            <?php endif; ?>
                            <div class="flex flex-col gap-1.5">
                                <span class="text-sm font-medium text-white/40">
                                    &larr; Artículo anterior
                                </span>
                                <span class="font-heading text-xl font-medium text-white group-hover:text-bars-badge-text transition-colors">
                                    <?php echo esc_html(get_the_title($prev_post->ID)); ?>
                                </span>
                                <time class="text-sm text-white/25">
                                    <?php echo esc_html(getDateInSpanish($prev_date)); ?>
                                </time>
                            </div>
                        </a>
                        <?php endif; ?>
                    </div>

                    <!-- Next Article -->
                    <div class="flex-1 flex justify-end">
                        <?php if ($next_post):
                            $next_date = new DateTime(get_the_date('c', $next_post->ID));
                        ?>
                        <a href="<?php echo esc_url(get_permalink($next_post->ID)); ?>"
                           class="flex items-center gap-5 group">
                            <div class="flex flex-col gap-1.5 items-end text-right">
                                <span class="text-sm font-medium text-white/40">
                                    Artículo siguiente &rarr;
                                </span>
                                <span class="font-heading text-xl font-medium text-white group-hover:text-bars-badge-text transition-colors">
                                    <?php echo esc_html(get_the_title($next_post->ID)); ?>
                                </span>
                                <time class="text-sm text-white/25">
                                    <?php echo esc_html(getDateInSpanish($next_date)); ?>
                                </time>
                            </div>
                            <?php if (has_post_thumbnail($next_post->ID)): ?>
                            <div class="w-[160px] h-[100px] flex-shrink-0 overflow-hidden rounded-bars-sm bg-bars-bg-card">
                                <?php echo get_the_post_thumbnail($next_post->ID, 'thumbnail', array(
                                    'class' => 'w-full h-full object-cover',
                                )); ?>
                            </div>
                            <?php endif; ?>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Mobile Nav -->
            <div class="flex flex-col gap-4 lg:hidden">
                <?php if ($prev_post):
                    $prev_date = new DateTime(get_the_date('c', $prev_post->ID));
                ?>
                <a href="<?php echo esc_url(get_permalink($prev_post->ID)); ?>"
                   class="flex items-center gap-3 bg-bars-bg-medium rounded-bars-md p-3">
                    <?php if (has_post_thumbnail($prev_post->ID)): ?>
                    <div class="w-[60px] h-[60px] flex-shrink-0 overflow-hidden rounded-md bg-bars-bg-card">
                        <?php echo get_the_post_thumbnail($prev_post->ID, 'thumbnail', array(
                            'class' => 'w-full h-full object-cover',
                        )); ?>
                    </div>
                    <?php endif; ?>
                    <div class="flex flex-col gap-1 min-w-0">
                        <span class="text-[11px] font-medium text-white/40">
                            &larr; Artículo anterior
                        </span>
                        <span class="font-heading text-base font-medium text-white truncate">
                            <?php echo esc_html(get_the_title($prev_post->ID)); ?>
                        </span>
                        <time class="text-[10px] text-white/25">
                            <?php echo esc_html(getDateInSpanish($prev_date)); ?>
                        </time>
                    </div>
                </a>
                <?php endif; ?>

                <?php if ($next_post):
                    $next_date = new DateTime(get_the_date('c', $next_post->ID));
                ?>
                <a href="<?php echo esc_url(get_permalink($next_post->ID)); ?>"
                   class="flex items-center gap-3 bg-bars-bg-medium rounded-bars-md p-3">
                    <div class="flex flex-col gap-1 min-w-0 flex-1 items-end text-right">
                        <span class="text-[11px] font-medium text-white/40">
                            Artículo siguiente &rarr;
                        </span>
                        <span class="font-heading text-base font-medium text-white truncate max-w-full">
                            <?php echo esc_html(get_the_title($next_post->ID)); ?>
                        </span>
                        <time class="text-[10px] text-white/25">
                            <?php echo esc_html(getDateInSpanish($next_date)); ?>
                        </time>
                    </div>
                    <?php if (has_post_thumbnail($next_post->ID)): ?>
                    <div class="w-[60px] h-[60px] flex-shrink-0 overflow-hidden rounded-md bg-bars-bg-card">
                        <?php echo get_the_post_thumbnail($next_post->ID, 'thumbnail', array(
                            'class' => 'w-full h-full object-cover',
                        )); ?>
                    </div>
                    <?php endif; ?>
                </a>
                <?php endif; ?>
            </div>
        </nav>
        <?php endif; ?>
    </div>
</section>

<?php endwhile; ?>

<?php get_footer(); ?>
