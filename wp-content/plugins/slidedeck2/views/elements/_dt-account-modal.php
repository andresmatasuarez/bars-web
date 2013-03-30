<div class="upsell-modal">
    <div class="slidedeck-header">
        <h1><?php _e( "Enjoying SlideDeck Lite?", $this->namespace ); ?></h1>
    </div>
    <div class="background">
        <div class="inner">
            <div class="copyblock">
                <h3><?php _e( "Get SlideDeck Updates &amp; More Free Products", $this->namespace ); ?></h3>
                <p><?php _e( "Sign up for a free dt labs account and never miss an update, plus get your hands on our other great products.", $this->namespace ); ?></p>
                <p class="align-center"><img src="https://s3.amazonaws.com/assets-slidedeck2/plugin_images/dt-labs-account.png" style="margin-top: 10px;" /></p>
            </div>
            <div class="cta">
                <a class="slidedeck-noisy-button" href="http://slidedeck.com/lite-plugin-learn-more?<?php echo self::get_cohort_query_string('&') ?>" class="button slidedeck-noisy-button" target="_blank" id="dt-labs-learn-more" ><span>Learn More</span></a>
                
                <a class="features-link" href="<?php echo wp_nonce_url( admin_url( 'admin-ajax.php' ) . '?action=slidedeck_dt_labs_update_modal', 'dt_labs_update_modal_remind_me_later' ); ?>">Remind Me Later</a>                
                <a class="features-link no-thanks" href="<?php echo wp_nonce_url( admin_url( 'admin-ajax.php' ) . '?action=slidedeck_dt_labs_update_modal', 'dt_labs_update_modal_no_thanks' ); ?>">No Thanks</a>
            </div>
        </div>
    </div>
</div>