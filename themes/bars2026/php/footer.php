</main><!-- #main-content -->

<!-- Sponsors Section -->
<?php get_template_part('template-parts/sections/sponsors', 'section'); ?>

<!-- Footer -->
<footer class="bg-bars-footer">
    <div class="max-w-[1280px] mx-auto px-5 lg:px-20 py-16 lg:py-20">
        <!-- Footer Main -->
        <div class="flex flex-col lg:flex-row lg:justify-between gap-12 lg:gap-20">
            <!-- Brand Column -->
            <div class="flex flex-col gap-6 lg:w-[350px]">
                <!-- Logo -->
                <a href="<?php echo esc_url(home_url('/')); ?>" class="flex items-center gap-3">
                    <img src="<?php echo get_template_directory_uri(); ?>/resources/bars_logo.png"
                         alt="BARS Logo"
                         class="w-10 h-10 object-contain">
                    <span class="footer-brand-name font-display text-2xl tracking-[2px] text-bars-text-primary">
                        Buenos Aires Rojo Sangre
                    </span>
                </a>

                <!-- Tagline -->
                <p class="text-sm text-bars-text-subtle leading-relaxed">
                    Festival Internacional de Cine de Terror, Fantasía y Bizarro. Desde 1999 celebrando el cine de género en Argentina.
                </p>

                <!-- Social Links -->
                <div class="flex items-center gap-4">
                    <a href="https://www.facebook.com/buenosairesrojosangre"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="w-9 h-9 flex items-center justify-center rounded-full bg-white/5 text-bars-text-muted hover:text-white hover:bg-white/10 transition-colors">
                        <?php echo bars_icon('facebook', 'w-5 h-5'); ?>
                    </a>
                    <a href="https://www.instagram.com/festivalrojosangre/"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="w-9 h-9 flex items-center justify-center rounded-full bg-white/5 text-bars-text-muted hover:text-white hover:bg-white/10 transition-colors">
                        <?php echo bars_icon('instagram', 'w-5 h-5'); ?>
                    </a>
                    <a href="https://www.youtube.com/user/rojosangrefestival"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="w-9 h-9 flex items-center justify-center rounded-full bg-white/5 text-bars-text-muted hover:text-white hover:bg-white/10 transition-colors">
                        <?php echo bars_icon('youtube', 'w-5 h-5'); ?>
                    </a>
                    <a href="https://x.com/rojosangre"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="w-9 h-9 flex items-center justify-center rounded-full bg-white/5 text-bars-text-muted hover:text-white hover:bg-white/10 transition-colors">
                        <?php echo bars_icon('x-twitter', 'w-5 h-5'); ?>
                    </a>
                    <a href="mailto:festivalrojosangre@gmail.com"
                       class="w-9 h-9 flex items-center justify-center rounded-full bg-white/5 text-bars-text-muted hover:text-white hover:bg-white/10 transition-colors">
                        <?php echo bars_icon('mail', 'w-5 h-5'); ?>
                    </a>
                </div>
            </div>

            <!-- Navigation Columns -->
            <div class="flex flex-col sm:flex-row gap-8 sm:gap-20">
                <!-- Festival Column -->
                <div class="flex flex-col gap-5">
                    <h4 class="font-body text-xs font-semibold tracking-[2px] uppercase text-bars-text-primary">
                        Festival
                    </h4>
                    <nav class="flex flex-col gap-3">
                        <a href="<?php echo home_url('/festival#historia'); ?>" class="font-body text-sm font-normal text-bars-text-muted hover:text-white transition-colors">
                            Historia
                        </a>
                        <a href="<?php echo home_url('/festival#rojosangretv'); ?>" class="font-body text-sm font-normal text-bars-text-muted hover:text-white transition-colors">
                            #RojoSangreTV
                        </a>
                        <a href="<?php echo home_url('/noticias'); ?>" class="font-body text-sm font-normal text-bars-text-muted hover:text-white transition-colors">
                            Noticias
                        </a>
                        <a href="<?php echo home_url('/programacion'); ?>" class="font-body text-sm font-normal text-bars-text-muted hover:text-white transition-colors">
                            Programación
                        </a>
                        <a href="<?php echo home_url('/premios'); ?>" class="font-body text-sm font-normal text-bars-text-muted hover:text-white transition-colors">
                            Premios
                        </a>
                    </nav>
                </div>

                <!-- Participate Column -->
                <div class="flex flex-col gap-5">
                    <h4 class="font-body text-xs font-semibold tracking-[2px] uppercase text-bars-text-primary">
                        Participar
                    </h4>
                    <nav class="flex flex-col gap-3">
                        <a href="<?php echo home_url('/convocatoria'); ?>" class="font-body text-sm font-normal text-bars-text-muted hover:text-white transition-colors">
                            Convocatoria
                        </a>
                        <a href="<?php echo home_url('/prensa'); ?>" class="font-body text-sm font-normal text-bars-text-muted hover:text-white transition-colors">
                            Prensa
                        </a>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div class="h-px bg-bars-divider my-10"></div>

        <!-- Footer Bottom -->
        <div class="flex justify-start">
            <p class="font-body text-[13px] text-bars-text-subtle">
                Ciudad de Buenos Aires, Argentina
            </p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
