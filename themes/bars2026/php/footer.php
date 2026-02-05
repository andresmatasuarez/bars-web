    </div><!-- #content -->

    <footer id="colophon" class="site-footer">
        <div class="site-info">
            <span class="copyright">
                &copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>
            </span>
        </div>

        <?php
        wp_nav_menu(array(
            'theme_location' => 'footer',
            'menu_id'        => 'footer-menu',
            'fallback_cb'    => false,
        ));
        ?>
    </footer>
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
