<?php
    $premium_lenses = array(
        'fashion' => array(
            'thumbnail' => "https://s3.amazonaws.com/slidedeck-pro/upsell_assets/images/lenses/fashion/thumbnail-large.jpg",
            'name' => "Fashion",
            'description' => "Ideal for sites with big bold images and a large number of slides.",
            'utm_content' => "SD2LENSFASHION"
        ),
        'classic' => array(
            'thumbnail' => "https://s3.amazonaws.com/slidedeck-pro/upsell_assets/images/lenses/classic/thumbnail-large.jpg",
            'name' => "Classic",
            'description' => "The classic SlideDeck form-factor. Configurable vertical spines.",
            'utm_content' => "SD2LENSCLASSIC"
        ),
        'half-moon' => array(
            'thumbnail' => "https://s3.amazonaws.com/slidedeck-pro/upsell_assets/images/lenses/half-moon/thumbnail-large.jpg",
            'name' => "Half Moon",
            'description' => "A great lens for showcasing your images and titles in a clean, blanaced, format.",
            'utm_content' => "SD2LENSHALFMOON"
        )
    );
?>
<?php foreach( $premium_lenses as $slug => $lens_meta ): ?>
    
    <?php if( !in_array( $slug, $lens_slugs ) ) : ?>
            
        <div class="lens add-lens">
            <div class="inner">
                <img src="<?php echo $lens_meta['thumbnail']; ?>" />
                <h4><?php echo $lens_meta['name']; ?></h4>
                <p><?php echo $lens_meta['description']; ?></p>
                <div class="upgrade-button-cta">
                    <a href="http://www.slidedeck.com/lenses-ae69de/?lens=<?php echo $slug; ?>&utm_source=premium_lenses_page&utm_medium=link&utm_content=<?php echo $lens_meta['utm_content']; ?>&utm_campaign=sd2_lite<?php echo self::get_cohort_query_string('&') . slidedeck2_km_link( 'Browse Premium Lens', array( 'name' => $lens_meta['name'], 'location' => 'Lens Management' ) ); ?>" target="_blank" class="upgrade-button green">
                        <span class="button-noise">
                            <span>Add <?php echo $lens_meta['name']; ?> Lens</span>
                        </span>
                    </a>
                </div>
            </div>
            <div class="actions"></div>
        </div>
        
    <?php endif; ?>
    
<?php endforeach; ?>