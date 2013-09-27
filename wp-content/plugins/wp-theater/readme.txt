=== WP Theater ===
Contributors: kentfarst
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=X3FWTE2FBBTJU
Tags: video, shortcode, embed, channel, playlist, group, user, youtube, vimeo, lower lights, full window, preset
Requires at least: 3.6
Tested up to: 3.6.1
Stable tag: 1.0.8
License: GPLv3

Shortcodes for YouTube and Vimeo. Includes embeds, "Theater" embed, thumbed previews, playlist, channel, user uploads and groups.

== Description ==
Shortcodes for integrating **YouTube** and **Vimeo** videos and feeds. Numerous options that include traditional embedding, single video previews, a wrapped "Theater" embed, and video listings from playlists, channels, user uploads and groups.

For a better looking explanation and parameter usage please visit:

http://redshiftstudio.com/wp-theater/

= Usage =
**Boring Embed** - The classic
`[youtube]VideoID[/youtube]
[vimeo]VideoID[/vimeo]`

**Preview** - Thumbnail and title of a single video
`[youtube preview]VideoID[/youtube]
[vimeo preview]VideoID[/vimeo]`

**Theater** - Traditional embed that's wrapped for styling and has optional Lower Lights and Full Window buttons.
`[youtube theater]VideoID[/youtube]
[vimeo theater]VideoID[/vimeo]`

*The following contain a "theater" by default*

**User** - Listing of a user's videos
`[youtube user]UserName[/youtube]
[vimeo user]UserID[/vimeo]`

**Channel** - Listing of videos from a specific channel
`[vimeo channel]ChannelID[/vimeo]`

**Playlist** - Listing of videos from a user's playlist
`[youtube playlist]PlaylistID[/youtube]`

**Group** - Listing of vidoes from a specific group
`[vimeo group]GroupID[/vimeo]`


= Presets =
Presets fill parameters with an appropriate default, including the service.  Built in presets are also set as their own shortcode; as such presets can be used as the shortcode's tag. e.g. *[youtube_widget ... ]*

Existing presets are:

* youtube
* vimeo
* youtube_widget
* vimeo_widget


= Requirements =

1. Tested on WordPress version 3.6 and later.  Does not support Multisite, yet
1. PHP 5 with curl


== Installation ==

1. Unpack the download-package
1. Upload all files to the `/wp-content/plugins/` directory, include folders
1. Activate the plugin through the 'Plugins' menu in WordPress


== Frequently Asked Questions ==

= What settings can be changed =
Outside of the shortcode's parameters we enable you to disable the loaded assets as well as the cache life or expiration.

* *Use Default CSS* - You can choose to disable the built in CSS file so that you can write your own.
* *Use Default Genericons* - You can choose to disable the Genericons fallback CSS file.  You should only disable the Genericons if you've also disabled the CSS file as the characters/icons are currently hardcoded.  WP Theater's genericons will not load if genericons are already enqueued.
* *Use Default JS* - You can choose to disable the built in JS file so that you can write your own.
* *Cache Expiration* - Feeds are cached using the Transient API and this setting will set the expiration.  A value of 0 (zero) will bypass caching.


== Developer FAQ ==

= How can I customize the output =
Filters exist that can handle complete customization of the output.

Display -- override built in output

* wp_theater-pre_video_shortcode ( '', $feed, $atts )
* wp_theater-pre_theater ( '', $atts, $content, $tag )
* wp_theater-pre_video_preview ( '', $video, $atts, $selected )

Attributes

* wp_theater-capture_no_value_atts ( $atts )
* wp_theater-format_params ( $atts, $content, $tag )

API Feeds -- Override built in api request and parsing

* wp_theater-pre_get_api_data ( '', $atts )
* wp_theater-pre_get_request_url ( '', $atts, $request, $output )
* wp_theater-pre_parse_feed ( '', $response, $atts )

Content

* wp_theater-section_title ( $title )
* wp_theater-video_title ( $title )
* wp_theater-pre_get_more_link ( '', $atts, $first_id )
* wp_theater-get_more_link ( $more_link, $atts, $first_id )

Presets

* wp_theater-get_preset ( $name )
* wp_theater-set_preset ( $arr, $name )

= How do I add my own preset? =
The following code will create a preset named "my_preset".  We do not currently, but are planning to, offer a method of saving presets to the database so that they stick around between theme's.

`
function my_preset_init ($presets) {
	$presets->set_preset( 'my_preset', shortcode_atts( $presets->get_preset( 'youtube' ), array(
		'embed_width' => 342,
		'embed_height' => 192,
		'max' => 9,
	) ) );
	add_shortcode( 'my_preset', array( WP_Theater::$shortcodes, 'video_shortcode' ) );
}
add_action('wp_theater-add_shortcodes', 'my_preset_init');
`

= How can I modify the embed url? =
Each preset requires a modes array to store the different link formats used.  You can directly access and modify these yourself through a theme's functions.php.
e.g.
`
// make youtube embed with https and youtube-nocookie.com
function my_preset_init ($presets) {
	$youtube_preset = $presets->get_preset( 'youtube' );
	$youtube_preset['modes']['embed'] = 'https://www.youtube-nocookie.com/embed/%id%?wmode=transparent&autohide=1';
	$presets->set_preset( 'youtube', $youtube_preset );
}
add_action('wp_theater-add_shortcodes', 'my_preset_init');
`

= What do the reformatted feeds look like? =
Vimeo's feed will return exactly what their API states except we merge their info and video requests into one and clone values to help normalize the feeds.  Youtube on the other hand is almost completely reformatted into a format based on Vimeo's

As of v1.0.0 you can count on the full feeds returning the following content with an exception being that single preview feeds do not have the feed title or url:
`
object
	'title' => string
	'url' => string
	'videos' => array
		0 => object
			'title' => string
			'id' => string
			'url' => string
			'upload_date' => string
			'description' => string
			'category' => string
			'duration' => string
			'rating' => string
			'likeCount' => string
			'viewCount' => string
			'width' => string
			'height' => string
			'thumbnails' => array
				'small' => string
				'medium' => string
				'large' => string
`


== Screenshots ==

1. Sample screen shot of how a Vimeo group will look like.  Image shows the title, theater, lower-lights & full window buttons, videos listing with thumb & title and a link to more content.


== Changelog ==
= 1.0.8 (09/17/2013) =

* Fixed validation of *modes'* link formats against requested mode
* Fixed instance where embeds would get query params added which are only meant for API requests
* Fixed WP Theater's version of shortcode_atts, which removes the filter hook, to only be used when setting up presets
* Removed plugin related constants in favor of class variables
* More readme fixes with screenshot

= 1.0.7 (09/16/2013) =

* Added autoplay_onclick parameter (default: TRUE)
* Fixed CSS & JS assets so they load only when the shortcode is in use
* Fixed videos so they get trimmed to $max after caching instead of before. 
* Fixed auto scrolling to only happen if the theater is not in view
* Fixed lower lights so they don't keep adding an element on IE8
* Removed the useless [video] shortcode as it conflicts with WP's built in shortcode by the same name
* Removed admin files from being included when on the front-end and vice versa
* Removed style coloring on links so a theme's styles take priority
* Fixed WP Theater's version of shortcode_atts to only be used when setting up presets
* More readme fixes

= 1.0.6 (09/15/2013) =

* Removed timing code left in by mistake

= 1.0.5 (09/14/2013) =

* Enabled setting for transient cache expiration (default is 4 hrs. -- 14400 seconds)

= 1.0.2 (09/13/2013) =

* Stupid typos

= 1.0 (09/13/2013) =

* Initial Release

== Upgrade Notice ==

= 1.0.7 =
More changes you'd expect from a quality plugin developer.