<?php 
if(class_exists('WP_Theater') && !class_exists('WP_Theater_Shortcodes')) {

class WP_Theater_Shortcodes  {

	/**
	 * Constructor
	 * @since WP Theater 1.0.0
	 */
	public function WP_Theater_Shortcodes()  {__construct();}

	/**
	 * Constructs
	 * @since WP Theater 1.0.0
	 */
	public function __construct() {
		add_action('init', array($this, 'init'));
	}

	/**
	 * Constructs
	 * @since WP Theater 1.0.0
	 */
	public function init() {

		$presets = WP_Theater::$presets;

		$presets->set_preset('default', array(
			'preset' => '',
			'service' => 'youtube',
			'mode' => 'embed',
			'id' => '',
			'embed_width' => 640,
			'embed_height' => 360,
			'class'=> '',
			'img_size' => 'small',
			'max' => 12,
			'autoplay_onclick' => TRUE,

			// Title settings
			'show_title' => TRUE,
			'show_video_title' => TRUE,
			'title' => '',

			// More link settings
			'show_more_link' => TRUE,
			'more_url' => FALSE,
			'more_text' => FALSE,

			// Theater options
			'show_theater' => TRUE,
			'theater_id' => FALSE,
			'show_fullwindow' => FALSE,
			'show_lowerlights' => FALSE,

			/* Modes cannot be defined except through a preset and should always:
			 * - minimally contain %id% in place of the ID or User Name -- Will eventually include %request% and %format% variable positions
			 */
			'modes' => array(),
		));

		// Add YouTube preset
		$presets->set_preset('youtube', $this->shortcode_atts($presets->get_preset('default'), array(
			'service'  => 'youtube',
			'img'      => 0,
			'img_size' => 'medium', // tests fallback -- YouTube has only small and large
			'show_fullwindow' => TRUE,
			'show_lowerlights' => TRUE,
			'modes' => array(
				'link'     => 'http://www.youtube.com/',
				'embed'    => 'http://www.youtube.com/embed/%id%?wmode=transparent&autohide=1',
				'preview'  => 'http://gdata.youtube.com/feeds/api/videos/%id%?v=2&alt=jsonc',
				'user'     => 'http://gdata.youtube.com/feeds/api/users/%id%/uploads?v=2&alt=jsonc&max-results=20',
				'playlist' => 'http://gdata.youtube.com/feeds/api/playlists/%id%?v=2&alt=jsonc&max-results=20'
			),
		)));

		// Add Vimeo preset
		$presets->set_preset('vimeo', $this->shortcode_atts($presets->get_preset('default'), array(
			'service'  => 'vimeo',
			'img'      => 0,
			'img_size' => 'medium',
			'show_fullwindow' => TRUE,
			'show_lowerlights' => TRUE,
			'modes' => array(
				'embed'    => 'http://player.vimeo.com/video/%id%?portrait=0&byline=0',
				'preview'  => 'http://vimeo.com/api/v2/video/%id%', // We add the videos.json & info.json part
				'user'     => 'http://vimeo.com/api/v2/user/%id%/',
				'channel'  => 'http://vimeo.com/api/v2/channel/%id%/',
				'group'    => 'http://vimeo.com/api/v2/group/%id%/'
			),
		)));

		// widget presets as a temp work around until I can make them proper -- cachable -- widgets

		// Add YouTube preset for sidebars
		$presets->set_preset('youtube_widget', $this->shortcode_atts($presets->get_preset('youtube'), array(
			'embed_width' => 360,
			'embed_height' => 170,
			'max' => 9,
			'img_size' => 'small',
			'show_video_title'  => FALSE,
			'show_title'  => FALSE,
			'show_more_link'  => FALSE,
			'show_fullwindow' => FALSE,
			'show_lowerlights' => FALSE,
		)));

		// Add YouTube preset for sidebars
		$presets->set_preset('vimeo_widget', $this->shortcode_atts($presets->get_preset('vimeo'), array(
			'embed_width' => 360,
			'embed_height' => 170,
			'max' => 9,
			'img_size' => 'small',
			'show_video_title'  => FALSE,
			'show_title'  => FALSE,
			'show_more_link'  => FALSE,
			'show_fullwindow' => FALSE,
			'show_lowerlights' => FALSE,
		)));

		// register the presets as shortcodes
		add_shortcode('youtube',        array($this, 'video_shortcode'));
		add_shortcode('vimeo',          array($this, 'video_shortcode'));
		add_shortcode('youtube_widget', array($this, 'video_shortcode'));
		add_shortcode('vimeo_widget',   array($this, 'video_shortcode'));

		do_action('wp_theater-add_shortcodes', $presets);
	}

	/**
	 * Creates a basic wrapper for a video to sit in.  No longer a stand alone shortcode
	 * @since WP Theater 1.0.0
	 *
	 * @param array $atts The shortcode's attributes
	 * @param string $content The shortcode's inner content
	 *
	 * @return string The string to be inserted in place of the shortcode.
	 */
	public function theater_shortcode($atts, $content = '', $tag) {

		// allow a filter to replace the output.
		$result = apply_filters('wp_theater-pre_theater', '', $atts, $content, $tag);
		if (!empty($result))
			return $result;

		// check if the content already contains html -- an easy way for people to wrap an embedded playlist
		if (!preg_match('/<.*>/', $content))
				$content = $this->get_iframe($atts);

		$theater_data = ($atts['mode'] == 'theater' && $atts['theater_id']) ? ' id="' . esc_attr($atts['theater_id']) . '"' : '';

		$result  = '<div class="wp-theater-bigscreen ' . esc_attr($atts['service']) . ' ' . esc_attr($atts['class']) . '"' . $theater_data . '>';
		$result .= '	<div class="wp-theater-bigscreen-inner">';
		$result .= 			$content;
		$result .= '		<div class="wp-theater-bigscreen-options">';

		if ($atts['show_lowerlights'])
			$result .= '		<a class="lowerlights-toggle" title="Toggle Lights" href="#"><span class="icon">Toggle Lights</span></a>';

		if ($atts['show_fullwindow'])
			$result .= '		<a class="fullwindow-toggle" title="Toggle Full Window" href="#"><span class="icon">Toggle Full Window</span></a>';

		$result .= '		</div>';
		$result .= '	</div>';
		$result .= '</div>';

		return $result;
	}

	/**
	 * Main shortcode for all YouTube integration
	 * @since WP Theater 1.0.0
	 *
	 * @param array $atts The shortcode's attributes
	 * @param string $content The shortcode's inner content
	 *
	 * @return string The string to be inserted in place of the shortcode.
	 */
	public function video_shortcode($atts, $content = '', $tag) {

		// let the plugin know that we need to load assets
		WP_Theater::enqueue_assets();

		// make sure all the atts are clean, setup correctly and extracted
		$atts = $this->format_params($atts, $content, $tag);
		if ($atts === FALSE) return '<!-- WP Theater - format_params failed -->';
		extract($atts);

		// can we just embed an iframe?
		if('embed' == $mode){
			return $this->get_iframe($atts);
		}

		// can we just embed a theater?
		if('theater' == $mode) {
			return $this->theater_shortcode($atts, $content, $tag);
		}

		// Else we need data

		// get the cache life int
		$options = get_option('wp_theater_options');
		$cache_life = isset($options['cache_life']) ? (int) $options['cache_life'] : 0;

		// set the transient data for this feed
		if($mode != 'preview' && $cache_life == 0) {
			$transient_name = 'wp_theater_transient_feed_' . $service . '_' . $mode . '_' . $id;
			if (false === ($feed = get_transient($transient_name))) {
				$feed = $this->get_api_data($atts);
				set_transient($transient_name, $feed, $cache_life);
			}
		}else{
			$feed = $this->get_api_data($atts);
		}

		if(empty($feed) || !isset($feed->videos) || !count($feed->videos))
			return '<!-- WP Theater - API request failed -->';

		// Figure out the title -- title attr first, content second, api feed third
		if(empty($title)) {
			if(!empty($content) && $id != $content) {
				$title = $content;
				$content = '';
			}elseif(isset($feed->title)) $title = $feed->title;
		}

		// make sure we only have as many as we want
		if (isset($max) && $max !== FALSE && $max > 0 && $max < count($feed->videos)) {
			$max_videos = array_slice($feed->videos, 0, $max);
			$feed->videos = $max_videos;
		}

		// Feed is formatted & ready to display

		// do we just need one video?
		if($mode == 'preview') {
			if (count($feed->videos == 1))
				return $this->video_preview($feed->videos[0], $atts);
			else return '<!-- WP Theater - Not enough data for preview -->';
		}

		// allow a filter to replace the output.
		$result = apply_filters('wp_theater-pre_video_shortcode', '', $feed, $atts, $content, $tag);
		if(!empty($result)) return $result;

		// Start formatting results

		$theater_data = !$show_theater && $theater_id ? ' data-theater-id="' . esc_attr($theater_id) . '"' : '';
		$result = '<section class="entry-section wp-theater-section ' . esc_attr($service) . ' ' . esc_attr($mode) . ' ' . esc_attr($atts['class']) . '"' .  $theater_data . '>';

		// insert the title
		if($show_title && !empty($title)) {
			$result .= '	<header>';
			$result .= '		<h3>' . apply_filters('wp_theater-section_title', $title) . '</h3>';
			$result .= '	</header>';
		}

		if($show_theater) {
			// needs to get info from the video
			$vid = $feed->videos[0];
			$tempatts = array_merge($atts, array('mode' => 'embed', 'id' => $vid->id));
			$result .= $this->theater_shortcode($tempatts, '', $tag);
		}

		// start the listing of videos
		$result .= '	<ul class="wp-theater-listing">';
		$is_first = TRUE;

		foreach($feed->videos as $video) {
			$result .= $this->video_preview($video, $atts, $is_first);
			if($is_first) $is_first = FALSE;
		}

		$result .= '	</ul>';

		// handle the more link
		if($show_more_link) {
			if($more_url)
				$feed->url = $more_url;
			if (!$more_text) $more_text = 'More ' . $title;

			$result .= '	<footer class="clear">';
			$result .= '		<a href="' . esc_url($feed->url) . '" title="' . esc_attr($more_text) . '" target="_blank" class="wp-theater-more-link">' . apply_filters('wp_theater-text', $more_text) . '</span></a>';
			$result .= '	</footer>';
		}

		$result .= '</section>';

		return $result;
	}

	/**
	 * An html string a video preview -- ONLY DOES YOUTUBE for now
	 * @since WP Theater 1.0.0
	 *
	 * @param array $video The single video's array of attributes
	 * @param array $atts The shortcodes attributes parameter
	 *
	 * @return string	A formatted html string that will display a single video.
	 */
	protected function video_preview($video, $atts, $selected = FALSE) {

		// make sure that we have an image
		if($atts['img_size'] == 'large' && (!isset($video->thumbnails['large']) || empty($video->thumbnails['large' ])))
			$atts['img_size'] = 'medium';	
		if($atts['img_size'] == 'medium' && (!isset($video->thumbnails['medium']) || empty($video->thumbnails['medium' ])))
			$atts['img_size'] = 'small';
		if($atts['img_size'] == 'small' && (!isset($video->thumbnails['small']) || empty($video->thumbnails['small' ])))
			return '';

		// allow a filter hook here in case someone wants to replace this content.
		$result = apply_filters('wp_theater-pre_video_preview', '', $video, $atts, $selected);
		if(!empty($result)) return $result;

		$wrapper_element = 'div';
		if ($atts['mode'] != 'preview') {
			$atts['class'] = '';
			$wrapper_element = 'li';
		}

		if($atts['mode'] == 'preview' && !empty($atts['title'])) {
			$video->title = $atts['title'];
		}

		$embed_atts = array('mode' => 'embed', 'modes' => array('embed' => $atts['modes']['embed']), 'id' => $video->id, 'autoplay_onclick' => $atts['autoplay_onclick']);

		$embed_url = $this->get_request_url($embed_atts);
		if ($atts['autoplay_onclick'])
			// bad idea to not parse and reformat but it's a bit too costly.
			$embed_url .= '&autoplay=1';

		$result .= '	<' . $wrapper_element . ' class="video-preview ' . esc_attr($atts['class']) . ($selected ? ' selected' : '') . '"' . 
		           ' data-id="' . esc_attr($video->id) . '"' . 
							 ' data-embed-url="' . esc_url($embed_url) . '"' .
							 ' data-embed-width="' . esc_attr($atts['embed_width']) . '"' .
							 ' data-embed-height="' . esc_attr($atts['embed_height']) . '"' .
							 '>';

		$result .= '		<a class="img-link" href="' . esc_url($video->url) . '" rel="nofollow" target="_blank" title="Watch ' . esc_attr($video->title) . '">';
		$result .= '			<img src="' . esc_url($video->thumbnails[$atts['img_size']]) . '" alt="' . esc_attr($video->title) . '" />';
		$result .= '		</a>';
		if($atts['show_video_title'])
			$result .= '		<a class="title-link" href="' . esc_url($video->url) . '" rel="nofollow" target="_blank" title="Watch ' . esc_attr($video->title) . '">' . apply_filters('wp_theater-video_title', $video->title) . '</a>';
		$result .= '	</' . $wrapper_element . '>';

		return $result;
	}

	/**
	 * Get a formatted string with the necessary html to embed an iframe
	 * @since WP Theater 1.0.0
	 *
	 * @param string $provider The name of the service provider -- youtube || vimeo
	 * @param array $atts The attributes required to fill in an iframe
	 *
	 * @return array A formatted string to display an embeded iframe
	 */
	protected function get_iframe($atts) {
		if ($atts['mode'] != 'embed') $atts['class'] = '';
		$result = '<iframe class="wp-theater-iframe ' . esc_attr($atts['service']) . ' ' . esc_attr($atts['mode']) . ' ' . esc_attr($atts['class']) . '" width="' . esc_attr($atts['embed_width']) . '" height="' . esc_attr($atts['embed_height']) . '" src="' . esc_url($this->get_request_url($atts)) . '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen></iframe>';
		return $result;
	}

	/**
	 * Formats a shortcode's parameters to maximize the chance of success
	 * @since WP Theater 1.0.0
	 *
	 * @param array $atts The shortcode's attributes
	 * @param string $content The shortcode's content
	 * @param string $tag The tag that called the shortcode
	 *
	 * @return string	A formatted array that should give the best chance of success
	 */
	protected function format_params($atts, $content, $tag) {

		// make attribues like [0] => 'hide_title' into ['show_title'] => FALSE
		$atts = $this->capture_no_value_atts($atts);

		// just in case someone tries to reset the modes array that is used for link formatting
		if (isset($atts['modes']))
			unset($atts['modes']);

		$presets = WP_Theater::$presets;

		// check for a preset -- atts' preset || shortcode tag as preset || service as preset || die, all empty
		if(!isset($atts['preset']) || empty($atts['preset'])) {
			if($tag != 'preview' && $presets->has_preset($tag))
				$atts['preset'] = $tag;
			elseif(isset($atts['service']) && $presets->has_preset($atts['service']))
				$atts['preset'] = $atts['service'];
			else return FALSE; 
		}

		// make sure we an ID to go on -- atts' id || content as ID || die, both empty
		if(empty($atts['id'])) {
			if(empty($content))
				return FALSE;
			else  {
				$atts['id'] = trim($content);
			}
		}
		
		$result = apply_filters('wp_theater-format_params', shortcode_atts($presets->get_preset($atts['preset']), $atts), $content, $tag);

		// make sure the link format is available for the requested mode
		if(!isset($result['mode']) || empty($result['mode'])) {
			return FALSE;
		}
		$mode = $result['mode'] == 'theater' ? 'embed' : $result['mode'];
		if(!isset($result['modes'][$mode]) || empty($result['modes'][$mode])) {
			return FALSE;
		}

		return $result;
	}

	/**
	 * Capture attributes without a value and convert to an extractable array --  $array[int] = value  =>  $array[string] = TRUE || value
	 * @since WP Theater 1.0.0
	 *
	 * @param array $atts The shortcodes attributes
	 *
	 * @return array The corrected array
	 */
	protected function capture_no_value_atts($atts) {
		if(!is_array($atts) || !count($atts)) return $atts;

		// needs -- compile a list of these settings based on available presets -- can't do the show/hide but can do service and mode.
		// case: $services[$value]
		// case: $modes[$value]

		foreach($atts as $key => $value) {
			if(is_int($key)) {

				switch($value) {

					case 'youtube':
					case 'vimeo':
						$atts['service'] = $value;
					 break;

					case 'embed':
					case 'theater':
					case 'preview':
					case 'user':
					case 'playlist':
					case 'channel':
					case 'group':
						$atts['mode'] = $value;
					 break;

					case 'show_video_title':
					case 'show_more_link':
					case 'show_theater':
					case 'show_fullwindow':
					case 'show_lowerlights':
						$atts[$value] = TRUE;
					 break;

					case 'show_title':
					case 'hide_title':
					case 'hide_video_title':
					case 'hide_more_link':
					case 'hide_theater':
					case 'hide_fullwindow':
					case 'hide_lowerlights':
						$atts['show' . substr($value, 4)] = FALSE;
					 break;

					default:
						// should I use any other ones as classes? $service & $mode are always used as classes.
						// ... This should probably add all of the ones provided as classes for use in JS.
						// ... ... that would provide all the detail needed to load a second page via JS + cURL.
						// ... ... ... What about the default settings -- you'd have to do this later meaning you couldn't use shortcode_atts
					 break;
				}
			}
		}
		return apply_filters('wp_theater-capture_no_value_atts', $atts);
	}

	/**
	 * Combine user attributes with known attributes and fill in defaults when needed.
	 * @since WP Theater 1.0.0
	 *
	 * @param array $pairs Entire list of supported attributes and their defaults.
	 * @param array $atts User defined attributes in shortcode tag.
	 * @param string $shortcode Optional. The name of the shortcode, provided for context to enable filtering
	 *
	 * @return array Combined and filtered attribute list.
	 */
	public function shortcode_atts($pairs, $atts) {
		$atts = (array) $atts;
		$out = array();

		foreach($pairs as $name => $default) {
			if (array_key_exists($name, $atts))
				$out[$name] = $atts[$name];
			else
				$out[$name] = $default;
		}

		return $out;
	}

	/**
	 * Retrieves and formats the data from an api request into a standard object
	 * @since WP Theater 1.0.0
	 *
	 * @param array $atts The shortcode's attributes
	 *
	 * @return stdClass An object with details about the feed and it's videos
	 */
	protected function get_api_data($atts) {

		// let people hook in here to parse their own service
		$result = apply_filters('wp_theater-pre_get_api_data', '', $atts);
		if (!empty($result)) return $result;

		$request_url = $this->get_request_url($atts);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_URL, $request_url);

		$response = curl_exec($ch);
		curl_close($ch);

		$result = new stdClass();
		$videos = array();


		// let people hook in here to parse their own service
		$result = apply_filters('wp_theater-pre_parse_feed', '', $response, $atts);
		if (!empty($result)) return $result;

		switch($atts['service']) {

			case 'youtube':

				//parsing begins here:
				if(!$response)
					return '';
				$feed = json_decode($response);
				if(!isset($feed->data))
					return '';
					
				if(isset($feed->data->items)) {
					foreach($feed->data->items as $video) {
						// user feeds don't have a sub listing
						$parsed_video = $this->parse_youtube_video($atts['mode'] == 'user' ? $video : $video->video);
						if ($parsed_video !== FALSE)
							array_push($videos, $parsed_video);
					}
				} else {
					$parsed_video = $this->parse_youtube_video($feed->data);
					if ($parsed_video !== FALSE)
						array_push($videos, $parsed_video);
				}

				if ($atts['mode'] == 'user')
					$result->title = (string) 'Uploads by ' . $atts['id'];
				else
					$result->title = (string) $feed->data->title;

				$result->url = $this->get_more_link($atts, isset($feed->data->items) ? $videos[0]->id : '');

			 break;

			case 'vimeo':

				//parsing begins here:
				if(!$response) return '';
				$feed = json_decode($response);
				if(!is_array($feed))  return '';
				foreach($feed as $video) {

					// make sure we can embed the video
					if ($video->embed_privacy  != 'anywhere')
						continue;

					$video->thumbnails = array();
			    $video->thumbnails['small']  = $video->thumbnail_small;
			    $video->thumbnails['medium'] = $video->thumbnail_medium;
			    $video->thumbnails['large']  = $video->thumbnail_large;
					// add the video
					array_push($videos,(object) $video);
				}

				// we need to request the info feed as well
				if($atts['mode'] == 'user' || $atts['mode'] == 'channel' || $atts['mode'] == 'group') {

					$request_url = $this->get_request_url($atts, 'info');

					$ch = curl_init();
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_TIMEOUT, 30);
					curl_setopt($ch, CURLOPT_URL, $request_url);

					$response = json_decode(curl_exec($ch));
					curl_close($ch);
					$result = $response;
					// just make sure we have a title named 'title'....

					if ($atts['mode'] == 'user') 
						$result->title = $result->display_name;
					else
						$result->title = $result->name;
				}

			 break;
		}

		$result->videos = $videos;

		return $result;

	}

	/**
	 * Get a formatted string with the necessary html to embed an iframe.  NOTE: $request and $output default values will change in the future so don't forget to define them when needed.
	 * @since WP Theater 1.0.0
	 *
	 * @param array $atts The attributes required to fill in an iframe
	 * @param string $request The request format (optional)
	 * @param string $output The output format (optional)
	 *
	 * @return string The complete request url
	 */
	protected function get_request_url($atts, $request = 'videos', $output = 'json')  {

		// Allow filter to override the request url
		$result = apply_filters('wp_theater-pre_get_request_url', '', $atts, $request, $output);
		if (!empty($result)) return $result;

		if ($atts['mode'] == 'theater') $atts['mode'] = 'embed';

		$result = str_replace('%id%', $atts['id'], $atts['modes'][$atts['mode']]);

		if(isset($atts['service']) && $atts['service'] == 'vimeo' && $atts['mode'] != 'embed') {
			if($atts['mode'] != 'preview')
				$result .= $request;
			$result .= '.' . $output;
		}

		return $result;
	}

	/**
	 * Takes a single video entry and returns a reformatted array
	 * @since WP Theater 1.0.0
	 *
	 * @param xml $video A single video SimpleXMLElement with the loaded data
	 *
	 * @return array An array of a single video's formatted data
	 */
	protected function parse_youtube_video($video) {

		$result = new stdClass();

		if (isset ($video->status->value) && $video->status->value == 'rejected')
			return FALSE;

		if (isset ($video->accessControl->embed) && $video->accessControl->embed == 'denied')
			return FALSE;

		$result->title = (string) $video->title;
		$result->id = (string) $video->id;
		$result->url = (string) $video->player->default;
		$result->upload_date = (string) $video->uploaded;
		$result->description = (string) $video->description;
		$result->category = (string) $video->category;
		$result->duration = (string) $video->duration;
		$result->rating = (string) $video->rating;
		$result->likeCount = (string) $video->likeCount;
		$result->viewCount = (string) $video->viewCount;

		// dimensions -- not going to bother with their aspect-ratio BS.
		$result->width = (string) '640';
		$result->height = (string) '360';

		// thumbnails
		$result->thumbnails = array();
		$result->thumbnails['small'] = (string) $video->thumbnail->sqDefault;
		// don't they have a mqDefault too?
		$result->thumbnails['large'] = (string) $video->thumbnail->hqDefault;

		return $result;
	}

	/**
	 * Get the more link to feeds that don't contain one.
	 * @since WP Theater 1.0.0
	 *
	 * @param array $atts The attributes required to fill in an iframe
	 *
	 * @return string The complete url
	 */
	protected function get_more_link($atts, $first_id = '') {
		
		// Allow filter to override more link
		$result = apply_filters('wp_theater-pre_get_more_link', '', $atts, $first_id);
		if (!empty($result)) return $result;

		$result = $atts['modes']['link'];
		if($atts['service'] == 'youtube') {
			if($atts['mode'] == 'user')
				$result .= 'user/' . $atts['id'];
			else
				$result .= 'watch?';
				if($atts['mode'] == 'embed' || $atts['mode'] == 'preview')
					$result .= 'v=' . $atts['id'];
				elseif($atts['mode'] == 'playlist')
					$result .= 'v=' . $first_id . '&list=' . $atts['id'];
		}
		return apply_filters('wp_theater-get_more_link', $result, $atts, $first_id);
	}
	/**/

} // END CLASS
} // END EXISTS CHECK