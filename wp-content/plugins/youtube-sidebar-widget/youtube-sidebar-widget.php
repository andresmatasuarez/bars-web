<?php
	/*
	Plugin Name: Youtube Sidebar Widget
	Plugin URI: http://wordpress.org/extend/plugins/youtube-sidebar-widget/
	Description: List video thumbnails from a Youtube account or playlist in your WordPress theme using widgets. Play the videos right on your theme!
	Version: 1.3.2
	Author: Stephen Coley
	Author URI: http://coley.co

	Copyright 2010  DK New Media  (email : stephen@dknewmedia.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as 
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*/

	function curl_it($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		$curl_r = curl_exec($ch);
		curl_close($ch);
		return $curl_r;
	}

	function ssl() {
		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
			return "https";
		} else {
			return "http";
		}
	}

	class YoutubeSidebarWidget extends WP_Widget {

		function YoutubeSidebarWidget() {
			parent::WP_Widget(false, $name = 'Youtube Sidebar Widget');	
		}

		function widget($args, $instance) {

			extract( $args );
			$title = apply_filters('widget_title', $instance['title']);
			$ident = esc_attr($instance['ident']);
			$ident_type = esc_attr($instance['ident_type']);
			if($ident_type == "playlist") {
				if(substr($ident, 0, 2) == "PL") {
					$ident = substr($ident, 2);
				}
			}
			$titles = esc_attr($instance['titles']);
			$num = esc_attr($instance['num_videos']); 
			$autoplay = esc_attr($instance['autoplay']);
			$fil = esc_attr($instance['filter']);
			$width = esc_attr($instance['width']);
			$height = ($width * 3) / 4;
			$key = "AI39si4egIgiaFaxxolUNjk1Iw4ip4GHWJMt44ZpnWXidiFLjlX1_kSwUYUI-bhtK9oW3bvZ7CFe7syG__AtokRmrTMDYHdXvA";
			$vidid_url = ssl() . "://gdata.youtube.com/feeds/api/videos/$ident?v=2&key=$key";
			$username_url = ssl() . "://gdata.youtube.com/feeds/base/users/$ident/uploads?alt=rss&amp;v=2&amp;orderby=published&amp;client=ytapi-youtube-profile&key=$key";
			$playlist_url = ssl() . "://gdata.youtube.com/feeds/api/playlists/$ident?v=2&key=$key";
			echo $before_widget;
			if($title) {
				echo $before_title . $title . $after_title;
			}			

			$output = "<div id='youtube-sidebar-widget' data-url='$playlist_url'>\n\r";
			$i = 1;
			if($ident_type == "username") {

				$curl_r = curl_it($username_url);

				if($simple_xml = simplexml_load_string($curl_r)) {
					$youtube_is_up = true;
				} else {
					$youtube_is_up = false;
				}

				/*echo "<pre>";
				print_r($simple_xml);
				echo "</pre>";*/

				if($youtube_is_up) {
					$output .= "<ul>\n\r";
					foreach($simple_xml->channel->item as $item){
						if($fil) {
							if(strpos($item->description, $fil)) {
								if($i <= $num) {
									$link = explode("/watch?v=", $item->link);
									$hash = explode("&", $link[1]);
									$img = ssl() . "://i2.ytimg.com/vi/$hash[0]/0.jpg";

									if($titles == "yes") {
										$output .= "<li id='$hash[0]' data-autoplay='$autoplay'><img src='$img' alt='$item->title' desc='" . strip_tags($item->description) . "' style='width:" . $width . "px;' /><div class='play_arrow' style='width:" . $width . "px; height: " . $height . "px; margin-top: -" . $height . "px;'></div>\n\r<a href='$item->link' style='width: " . $width . "px;'>$item->title</a>";
									} else {
										$output .= "<li id='$hash[0]' data-autoplay='$autoplay'><img src='$img' alt='$item->title' desc='" . strip_tags($item->description) . "' style='width:" . $width . "px;' /><div class='play_arrow' style='width:" . $width . "px; height: " . $height . "px; margin-top: -" . $height . "px;'></div>\n\r";
									}
									
									$i++;
								} else {
									break;
								}
							}
						} else {
							if($i <= $num) {
								$link = explode("/watch?v=", $item->link);
								$hash = explode("&", $link[1]);
								$img = ssl() . "://i2.ytimg.com/vi/$hash[0]/0.jpg";

								if($titles == "yes") {
									$output .= "<li id='$hash[0]' data-autoplay='$autoplay'><img src='$img' alt='$item->title' desc='" . strip_tags($item->description) . "' style='width:" . $width . "px;' /><div class='play_arrow' style='width:" . $width . "px; height: " . $height . "px; margin-top: -" . $height . "px;'></div>\n\r<a href='$item->uri' style='width: " . $width . "px;'>$item->title</a>";
								} else {
									$output .= "<li id='$hash[0]' data-autoplay='$autoplay'><img src='$img' alt='$item->title' desc='" . strip_tags($item->description) . "' style='width:" . $width . "px;' /><div class='play_arrow' style='width:" . $width . "px; height: " . $height . "px; margin-top: -" . $height . "px;'></div>\n\r";
								}
								
								$i++;
							} else {
								break;
							}
						}
					}
					$output .= "</ul>\n\r";
				} else {
					$output .= "<p class='ysw-unavailable'>Service Unavailable.</p>";
				}
			} elseif($ident_type == "vidid") {

				$curl_r = curl_it($vidid_url);

				if($simple_xml = simplexml_load_string($curl_r)) {
					$youtube_is_up = true;
				} else {
					$youtube_is_up = false;
				}

				/*echo "<pre>";
				print_r($simple_xml);
				echo "</pre>";*/

				if($youtube_is_up) {
					$output .= "<ul>\n\r";
					$item = $simple_xml;
					if($i <= $num) {

						$link = explode("watch?v=", $item->link['href']);
						$hash = explode("&", $link[1]);
						$img = ssl() . "://i2.ytimg.com/vi/$hash[0]/0.jpg";

						if($titles == "yes") {
							$output .= "<li id='$hash[0]' data-autoplay='$autoplay'><img src='$img' alt='$item->title' desc='" . strip_tags($item->description) . "' style='width:" . $width . "px;' /><div class='play_arrow' style='width:" . $width . "px; height: " . $height . "px; margin-top: -" . $height . "px;'></div>\n\r<a href='$item->link' style='width: " . $width . "px;'>$item->title</a>";
						} else {
							$output .= "<li id='$hash[0]' data-autoplay='$autoplay'><img src='$img' alt='$item->title' desc='" . strip_tags($item->description) . "' style='width:" . $width . "px;' /><div class='play_arrow' style='width:" . $width . "px; height: " . $height . "px; margin-top: -" . $height . "px;'></div>\n\r";
						}
						
						$i++;
					} else {
						break;
					}
					$output .= "</ul>\n\r";
				} else {
					$output .= "<p class='ysw-unavailable'>Service Unavailable.</p>";
				}
			} elseif($ident_type == "playlist") {

				$curl_r = curl_it($playlist_url);

				if($simple_xml = simplexml_load_string($curl_r)) {
					$youtube_is_up = true;
				} else {
					$youtube_is_up = false;
				}
				if($youtube_is_up) {
					$output .= "<ul>\n\r";
					foreach($simple_xml->entry as $item) {
						foreach($item->link as $link_url) {
							if($link_url['type'] == "text/html" && $link_url['rel'] == "alternate") {
								if($i <= $num) {
								$link = explode("/watch?v=", $link_url['href']);
								$hash = explode("&", $link[1]);
								$img = ssl() . "://i2.ytimg.com/vi/$hash[0]/0.jpg";

								if($titles == "yes") {
									$output .= "<li id='$hash[0]' data-autoplay='$autoplay'><img src='$img' alt='$item->title' desc='$item->title' style='width:" . $width . "px;' /><div class='play_arrow' style='width:" . $width . "px; height: " . $height . "px; margin-top: -" . $height . "px;'></div>\n\r<a href='$item->link' style='width: " . $width . "px;'>$item->title</a>";
								} else {
									$output .= "<li id='$hash[0]' data-autoplay='$autoplay'><img src='$img' alt='$item->title' desc='$item->title' style='width:" . $width . "px;' /><div class='play_arrow' style='width:" . $width . "px; height: " . $height . "px; margin-top: -" . $height . "px;'></div>\n\r";
								}
								
								$i++;
							} else {
								break;
							}
							}
						}
					}
					$output .= "</ul>\n\r";
				} else {
					$output .= "<p class='ysw-unavailable'>Service Unavailable.</p>";
				}
			}
			$output .= "</div>\n\r";
			echo $output;
			echo $after_widget;
		}

		function update($new_instance, $old_instance) {				
			$instance = $old_instance;
			$instance['title'] = strip_tags($new_instance['title']);
			$instance['ident_type'] = strip_tags($new_instance['ident_type']);
			$instance['ident'] = strip_tags($new_instance['ident']);
			$instance['titles'] = strip_tags($new_instance['titles']);
			$instance['num_videos'] = strip_tags($new_instance['num_videos']);
			$instance['autoplay'] = strip_tags($new_instance['autoplay']);
			$instance['filter'] = strip_tags($new_instance['filter']);
			$instance['width'] = strip_tags($new_instance['width']);
			return $instance;
		}

		function form($instance) {				
			$instance = wp_parse_args( (array) $instance, array('title'=>'', 'ident_type'=>'username', 'ident'=>'', 'titles'=>'yes', 'num_videos'=>'2', 'autoplay'=>'0', 'filter'=>'', 'width'=>'180') );

			$title = esc_attr($instance['title']);
			$ident_type = esc_attr($instance['ident_type']);
			$ident = esc_attr($instance['ident']);
			$titles = esc_attr($instance['titles']);
			$num = esc_attr($instance['num_videos']); 
			$autoplay = esc_attr($instance['autoplay']);
			$fil = esc_attr($instance['filter']);
			$width = esc_attr($instance['width']); ?>
			

			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php if(isset($title)) { echo $title; } ?>" />
			</p>
			<p>
				<input id="<?php echo $this->get_field_id('ident_type'); ?>" name="<?php echo $this->get_field_name('ident_type'); ?>" type="radio" value="vidid" <?php if($ident_type == "vidid") { echo "checked"; } ?>/> Video ID
				<input id="<?php echo $this->get_field_id('ident_type'); ?>" name="<?php echo $this->get_field_name('ident_type'); ?>" type="radio" value="username" <?php if($ident_type == "username") { echo "checked"; } ?>/> Username
				<input id="<?php echo $this->get_field_id('ident_type'); ?>" name="<?php echo $this->get_field_name('ident_type'); ?>" type="radio" value="playlist" <?php if($ident_type == "playlist") { echo "checked"; } ?>/> Playlist ID
			</p>
			<p> 
				<input class="widefat" id="<?php echo $this->get_field_id('ident'); ?>" name="<?php echo $this->get_field_name('ident'); ?>" type="text" value="<?php if(isset($ident)) { echo $ident; } ?>" />
			</p> 
			<p>
				<label for="<?php echo $this->get_field_id('titles'); ?>"><?php _e('Display Titles:'); ?></label> 
				<select class="widefat" id="<?php echo $this->get_field_id('titles'); ?>" name="<?php echo $this->get_field_name('titles'); ?>">
					<option value='yes' <?php if($titles == "yes") { echo "selected='selected'"; } ?> >yes</option>
					<option value='no' <?php if($titles == "no") { echo "selected='selected'"; } ?> >no</option>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('num_videos'); ?>"><?php _e('Number to Display:'); ?></label> 
				<select class="widefat" id="<?php echo $this->get_field_id('num_videos'); ?>" name="<?php echo $this->get_field_name('num_videos'); ?>">
					<?php for($i = 1; $i <= 10; $i++) {
						if($num == $i) {
							echo "<option value='$i' selected='selected'>$i</option>";
						} else {
							echo "<option value='$i'>$i</option>";
						}
					} ?>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('autoplay'); ?>"><?php _e('Autoplay:'); ?></label> 
				<select class="widefat" id="<?php echo $this->get_field_id('autoplay'); ?>" name="<?php echo $this->get_field_name('autoplay'); ?>">
					<?php if($autoplay == 0) {
						echo "<option value='0' selected='selected'>No</option>";
						echo "<option value='1'>Yes</option>";
					} elseif($autoplay == 1) {
						echo "<option value='0'>No</option>";
						echo "<option value='1' selected='selected'>Yes</option>";
					} ?>
				</select>
			<p>
				<label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e('Filter:'); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="text" value="<?php if(isset($fil)) { echo $fil; } ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Thumbnail Width:'); ?></label><br />
				<input class="" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" size="3" type="text" value="<?php if(isset($width)) { echo $width; } ?>" /> px
			</p>
		 <?php }

	}

	function ysw_init() {
		if (!is_admin()) {
			wp_enqueue_script('jquery');
		}
	}	

	function ysw_head() {
		echo "<link rel='stylesheet' href='" . get_bloginfo('url') . "/wp-content/plugins/youtube-sidebar-widget/style.css' />";
		wp_enqueue_script('jquery');
	}

	function ysw_footer() {
		echo "<script type='text/javascript' src='" . get_bloginfo('url'). "/wp-content/plugins/youtube-sidebar-widget/script.js'></script>";
	}

	add_action('widgets_init', create_function('', 'return register_widget("YoutubeSidebarWidget");'));
	add_action('init', 'ysw_init');
	add_action('wp_head', 'ysw_head');
	add_action('wp_footer', 'ysw_footer');
	

?>
