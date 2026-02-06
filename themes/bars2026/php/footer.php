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
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                    <a href="https://www.instagram.com/festivalrojosangre/"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="w-9 h-9 flex items-center justify-center rounded-full bg-white/5 text-bars-text-muted hover:text-white hover:bg-white/10 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </a>
                    <a href="https://www.youtube.com/user/rojosangrefestival"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="w-9 h-9 flex items-center justify-center rounded-full bg-white/5 text-bars-text-muted hover:text-white hover:bg-white/10 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                        </svg>
                    </a>
                    <a href="https://x.com/rojosangre"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="w-9 h-9 flex items-center justify-center rounded-full bg-white/5 text-bars-text-muted hover:text-white hover:bg-white/10 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                    </a>
                    <a href="mailto:festivalrojosangre@gmail.com"
                       class="w-9 h-9 flex items-center justify-center rounded-full bg-white/5 text-bars-text-muted hover:text-white hover:bg-white/10 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
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
                        <a href="<?php echo home_url('/festival'); ?>" class="font-body text-sm font-normal text-bars-text-muted hover:text-white transition-colors">
                            Historia
                        </a>
                        <a href="https://www.youtube.com/user/rojosangrefestival" target="_blank" rel="noopener noreferrer" class="font-body text-sm font-normal text-bars-text-muted hover:text-white transition-colors">
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
