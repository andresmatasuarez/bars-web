<div class="upsell-modal">
	<div class="slidedeck-header">
	    <h1><?php _e( "Upgrade to Get Support", $this->namespace ); ?></h1>
	</div>
	<div class="background">
		<div class="inner">
			<div class="copyblock">
			    <h3><?php _e( "Talk to real human beings!", $this->namespace ); ?></h3>
				<p>We're a proud team of design nerds whose passion is improving the Web. When you contact our support team, rest assured you're talking to the same folks who actually built SlideDeck.</p>
                <ul id="support-team-list">
                    <li>
                        <img src="http://0.gravatar.com/avatar/088374bf3ffe48cc14bbf3aab1cb47e8?s=64" alt="Jason">
                        <span class="name">Jason</span>
                    </li>
                    <li>
                        <img src="http://0.gravatar.com/avatar/acc09d405795fc4dfe7d0f31dd84d3e9?s=64" alt="Arnold">
                        <span class="name">Arnold</span>
                    </li>
                    <li>
                        <img src="http://0.gravatar.com/avatar/abce978306df6dfa4fd59ed4efdc706f?s=64" alt="Dave">
                        <span class="name">Dave</span>
                    </li>
                    <li>
                        <img src="http://0.gravatar.com/avatar/ee5253eb9468d6f98b1f2ea78ed8b6f7?s=64" alt="Jamie">
                        <span class="name">Jamie</span>
                    </li>
                    <li>
                        <img src="http://0.gravatar.com/avatar/9bdebe420cb1d4bd0401723d3abe1248?s=64" alt="Bradley">
                        <span class="name">Bradley</span>
                    </li>
                    <li>
                        <img src="http://0.gravatar.com/avatar/30bde62687957b255475c62e23726e70?s=64" alt="John">
                        <span class="name">John</span>
                    </li>
                </ul>
			</div>
			<div class="cta">
				<a class="slidedeck-noisy-button" href="<?php echo slidedeck2_action( "/upgrades&referrer=Need+Support+Handslap" ); ?>" class="button slidedeck-noisy-button"><span>Upgrade to Personal</span></a>
				<a class="features-link" href="http://www.slidedeck.com/features?utm_campaign=sd2_lite&utm_medium=handslap_link&utm_source=handslap_support&utm_content=support_team<?php echo self::get_cohort_query_string('&'); ?>" target="_blank">or learn more about other SlideDeck features</a>
			</div>
		</div>
	</div>
</div>