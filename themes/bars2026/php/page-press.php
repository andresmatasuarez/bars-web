<?php
/**
 * Template Name: Press
 * @package BARS2026
 */

get_header();

$edition = Editions::current();
$edition_number = Editions::romanNumerals($edition);
$from = Editions::from($edition);
$to = Editions::to($edition);
$festival_dates = Editions::datesLabel($edition);

// Press passes data
$press_passes = isset($edition['pressPasses']) ? $edition['pressPasses'] : array();
$press_deadline = !empty($press_passes['deadline']) ? Editions::getPressPassesDeadline($edition) : null;
$press_pickup = Editions::getPressPassesPickupDates($edition);
$press_additional_info = Editions::getPressPassesAdditionalInfo($edition);
$press_pickup_locations = Editions::getPressPassesPickupLocations($edition);
$press_form_url = Editions::getPressPassesCredentialsFormURL($edition);

// Build venue name for intro text (first non-online venue)
$venues = Editions::venues($edition);
$venue_name = '';
if ($venues) {
    foreach ($venues as $venue) {
        if (!isset($venue['online']) || !$venue['online']) {
            $venue_name = $venue['name'];
            break;
        }
    }
}
?>

<?php
// WP 3.9 compat: get_template_part() $args (3rd param) requires WP 5.5+
$GLOBALS['page_hero_args'] = array(
    'title' => 'Prensa',
    'subtitle' => 'Edición ' . $edition_number . ' • ' . $festival_dates,
);
get_template_part('template-parts/sections/page', 'hero');
?>

<!-- Accreditation Section -->
<section class="relative min-h-96 py-8 pb-20 lg:py-16 lg:pb-36">
    <div class="max-w-[1000px] mx-auto px-5 lg:px-0">

        <!-- Intro Text -->
        <p class="text-[13px] lg:text-base text-bars-text-muted leading-[1.7] max-w-[335px] lg:max-w-[800px] mb-8 lg:mb-10">
            El festival Buenos Aires Rojo Sangre <?php echo esc_html($edition_number); ?> se llevará a cabo <?php if ($from && $to): ?>del <?php echo esc_html($from->format('j')); ?> al <?php echo esc_html($to->format('j') . ' de ' . getSpanishMonthName($to->format('F')) . ' de ' . $to->format('Y')); ?><?php if ($venue_name): ?> en <?php echo esc_html($venue_name); ?><?php endif; ?>.<?php else: ?>durante <?php echo esc_html(strtolower(Editions::datesLabel($edition))); ?>.<?php endif; ?> Los medios interesados en cubrir el evento pueden solicitar su acreditación a través del formulario online.
        </p>

        <!-- Cards Row: Deadlines + Benefits -->
        <div class="flex flex-col lg:flex-row gap-6 lg:gap-8 mb-6 lg:mb-8">

            <!-- Deadlines Card -->
            <div class="flex-1 bg-bars-bg-medium lg:bg-bars-bg-card rounded-bars-md p-5 lg:p-8">
                <h3 class="text-sm lg:text-lg font-semibold text-bars-text-primary mb-4 lg:mb-6">
                    📅 Fechas Importantes
                </h3>
                <ul class="space-y-3 lg:space-y-4 text-xs lg:text-sm text-bars-text-muted leading-[1.6]">
                    <li>• Cierre de acreditaciones: <span class="text-bars-text-primary font-medium"><?php echo $press_deadline ? esc_html(ucfirst(displayDateInSpanish($press_deadline))) : 'TBD'; ?></span></li>
                    <?php
                    $pickup_from = $press_pickup ? $press_pickup['from'] : null;
                    $pickup_to = $press_pickup ? $press_pickup['to'] : null;
                    if ($pickup_from && $pickup_to):
                        $pickup_dates = 'del ' . $pickup_from->format('j') . ' al ' . $pickup_to->format('j') . ' de ' . getSpanishMonthName($pickup_to->format('F'));
                    elseif ($pickup_from):
                        $pickup_dates = 'desde el ' . $pickup_from->format('j') . ' de ' . getSpanishMonthName($pickup_from->format('F'));
                    elseif ($pickup_to):
                        $pickup_dates = 'hasta el ' . $pickup_to->format('j') . ' de ' . getSpanishMonthName($pickup_to->format('F'));
                    else:
                        $pickup_dates = null;
                    endif;
                    ?>
                    <li>• Retiro de credenciales: <?php if ($pickup_dates): ?><span class="text-bars-text-primary font-medium"><?php echo esc_html($pickup_dates); ?><?php if ($press_additional_info): ?>, <?php echo esc_html($press_additional_info); ?><?php endif; ?></span><?php if ($press_pickup_locations): ?>, en los stands del festival en <span class="text-bars-text-primary font-medium"><?php echo esc_html(implode(', ', $press_pickup_locations)); ?></span><?php endif; ?><?php else: ?><span class="text-bars-text-primary font-medium">TBD</span><?php endif; ?></li>
                </ul>
            </div>

            <!-- Benefits Card -->
            <div class="flex-1 bg-bars-bg-medium lg:bg-bars-bg-card rounded-bars-md p-5 lg:p-8">
                <h3 class="text-sm lg:text-lg font-semibold text-bars-text-primary mb-4 lg:mb-6">
                    🎬 Beneficios
                </h3>
                <ul class="space-y-3 lg:space-y-4 text-xs lg:text-sm text-bars-text-muted leading-[1.6]">
                    <li>• Acceso a una (1) de las tres (3) funciones diarias de las 16hs</li>
                    <li>• Acceso a una (1) función extra diaria para cualquier función a elección, sujeto a disponibilidad de la sala</li>
                    <li>• Posibilidad de solicitar entrevistas</li>
                </ul>
            </div>

        </div>

        <!-- Notes Card -->
        <div class="bg-bars-primary/[0.08] rounded-bars-md p-5 lg:p-8 mb-8 lg:mb-10">
            <h3 class="text-sm lg:text-lg font-semibold text-bars-text-primary mb-4 lg:mb-6">
                ⚠️ Importante
            </h3>
            <ul class="space-y-3 lg:space-y-4 text-xs lg:text-sm text-bars-text-muted leading-[1.6]">
                <li>• No se recibirán solicitudes fuera de las fechas consignadas.</li>
                <li>• Una vez evaluada la solicitud, se responderá con un correo, donde se informará si la acreditación fue aceptada o no por el festival.</li>
                <li>• Los cupos de acreditación son limitados; solo uno por medio, debido a las capacidades de las salas en las cuales se desarrolla el festival.</li>
                <li>• Las credenciales son personales e intransferibles; únicamente la persona que acredite identidad con su credencial podrá retirar sus vouchers.</li>
                <li>• Se requiere verificación de identidad para el retiro.</li>
            </ul>
        </div>

        <!-- CTA Button -->
        <?php if ($press_form_url): ?>
        <a href="<?php echo esc_url($press_form_url); ?>"
           target="_blank"
           rel="noopener noreferrer"
           class="inline-flex items-center justify-center w-full lg:w-auto px-8 py-4 bg-bars-primary text-white text-[13px] lg:text-sm font-semibold tracking-[1px] uppercase rounded-bars-sm hover:bg-[#A00000] transition-colors">
            Solicitar Acreditación
        </a>
        <?php endif; ?>

    </div>
</section>

<?php get_footer(); ?>
