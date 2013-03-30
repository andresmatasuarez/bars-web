<?php
    $premium_lenses = array(
        'fashion' => array(
            'thumbnail' => "https://s3.amazonaws.com/slidedeck-pro/upsell_assets/images/lenses/fashion/thumbnail.jpg",
            'name' => "Fashion",
            'utm_content' => "SD2LENSFASHION"
        ),
        'classic' => array(
            'thumbnail' => "https://s3.amazonaws.com/slidedeck-pro/upsell_assets/images/lenses/classic/thumbnail.jpg",
            'name' => "Classic",
            'utm_content' => "SD2LENSCLASSIC"
        ),
        'half-moon' => array(
            'thumbnail' => "https://s3.amazonaws.com/slidedeck-pro/upsell_assets/images/lenses/half-moon/thumbnail.jpg",
            'name' => "Half Moon",
            'utm_content' => "SD2LENSHALFMOON"
        )
    );
?>
<?php foreach( $premium_lenses as $slug => $lens_meta ): ?>

    <?php if( !in_array( 'fashion', $lens_slugs ) ) : ?>
    
        <a href="http://www.slidedeck.com/lenses-ae69de/?lens=<?php echo $slug; ?>&utm_source=premium_lenses_tab&utm_medium=link&utm_content=<?php echo $lens_meta['utm_content']; ?>&utm_campaign=sd2_lite<?php echo self::get_cohort_query_string('&') . slidedeck2_km_link( 'Browse Premium Lens', array( 'name' => $lens_meta['name'], 'location' => 'Lens Choices Tab' ) ); ?>" target="_blank" class="lens placeholder" rel="lenses">
            <span class="thumbnail"><img src="<?php echo $lens_meta['thumbnail']; ?>" /></span>
            <span class="shadow">&nbsp;</span>
            <span class="title"><?php echo $lens_meta['name']; ?></span>
        </a>
    
    <?php endif; ?>

<?php endforeach; ?>

<div class="upgrade-license-lenses">
    <span class="upgrade">
        <img src="https://s3.amazonaws.com/slidedeck-pro/lite_upsell_assets/images/need-more-lenses.png" />
        <div class="upgrade-button-cta">
            <a href="<?php echo slidedeck2_action( "/upgrades&referrer=Lens+Choices+Tab" ); ?>" class="upgrade-button green">
                <span class="button-noise">
                    <span>Upgrade</span>
                </span>
            </a>
        </div>
    </span>
    <span class="shadow">&nbsp;</span>
</div>