<div class="upsell-modal">
    <div class="slidedeck-header">
        <h1><?php _e( "Upgrade to Insert Image Slides", $this->namespace ); ?></h1>
    </div>
    <div class="background">
        <div class="inner">
            <div class="copyblock">
                <h3><?php _e( "Add images to your sliders", $this->namespace ); ?></h3>
                <p><?php _e( "The image slide type allows you to upload your own images, apply captions, and drag and drop to rearrange your slides. You can even choose from three different, customizable caption layouts, for the perfect fit.", $this->namespace ); ?></p>
                <h4>Choose from 3 different layouts</h4>
                <p class="align-center"><img src="<?php echo SLIDEDECK2_URLPATH; ?>/images/upsell-slide-type-image.jpg" alt="Caption, Body Text, None" /></p>
            </div>
            <div class="cta">
                <a class="slidedeck-noisy-button" href="<?php echo slidedeck2_action( "/upgrades&referrer=Image+Slide+Type+Handslap" ); ?>" class="button slidedeck-noisy-button"><span>Upgrade to Personal</span></a>
                <a class="features-link" href="http://demo.slidedeck.com/wp-login.php?utm_campaign=sd2_lite&utm_medium=handslap_link&utm_source=handslap_slide_type&utm_content=html_slide<?php echo self::get_cohort_query_string('&') . slidedeck2_km_link(); ?>" target="_blank">or try it out in the live demo</a>
            </div>
        </div>
    </div>
</div>