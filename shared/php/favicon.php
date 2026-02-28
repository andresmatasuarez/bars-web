<?php
/**
 * Favicon tags for wp_head
 *
 * Outputs <link> and <meta> tags for favicons, apple-touch-icons,
 * Android manifest, MS tile, and theme-color.
 *
 * @package BARS
 */

function bars_favicon_tags() {
    $base = get_template_directory_uri() . '/resources/favicon/';
    ?>
    <link rel="icon" type="image/png" sizes="192x192" href="<?php echo $base; ?>android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="96x96" href="<?php echo $base; ?>favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="48x48" href="<?php echo $base; ?>android-icon-48x48.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo $base; ?>favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $base; ?>favicon-16x16.png">
    <link rel="icon" href="<?php echo $base; ?>favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="57x57" href="<?php echo $base; ?>apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="<?php echo $base; ?>apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="<?php echo $base; ?>apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="<?php echo $base; ?>apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="<?php echo $base; ?>apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="<?php echo $base; ?>apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="<?php echo $base; ?>apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="<?php echo $base; ?>apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $base; ?>apple-icon-180x180.png">
    <link rel="manifest" href="<?php echo $base; ?>manifest.json">
    <meta name="msapplication-TileColor" content="#0A0A0A">
    <meta name="msapplication-TileImage" content="<?php echo $base; ?>ms-icon-144x144.png">
    <meta name="theme-color" content="#0A0A0A">
    <?php
}
add_action('wp_head', 'bars_favicon_tags', 0);
