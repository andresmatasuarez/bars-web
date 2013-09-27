=== Youtube Sidebar Widget ===
Contributors: srcoley, douglaskarr
Tags: YouTube, Video, Widget
Requires at least: 2.0.2
Tested up to: 3.4.1
Stable tag: 1.3.2
Version: 1.3.2

List video thumbnails from a Youtube video, account, or playlist in your WordPress theme using widgets. Play the videos right on your theme!

== Description ==

You can specify a YouTube video id, username, or playlist id to pull videos from, how many videos to pull, and a filter to pull only the videos you mean to(only if you're pulling by username).

== Installation ==

1. Upload the `youtube-sidebar-widget` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place the `Youtube Sidebar Widget` widget into a widget slot
1. Configure settings and save!

== Frequently Asked Questions ==

= Is this plugin widget ready? =

Yes!

= Where do I find the Playlist ID = 

Go to the page of one of the videos in the playlist. In the url you will see something like `&list=PL71B8152559FA2805`, remove the `&list=PL` from the string and you've found your Playlist ID Since v1.2, The "PL" does not need to be removed.

== Screenshots ==

1. YouTube Sidebar Widget settings
2. YouTube Sidebar Widget as implemented in the http://marketingtechblog.com sidebar
3. YouTube Sidebar Widget displaying video on http://marketingtechblog.com

== Changelog ==

= 1.0 =
* This is the first version

= 1.1 =
* Utilize wp_enqueue_script
* Fixed css overlay bugs
* Added screenshots

= 1.1.1 =
* Fix filter bug

= 1.1.2 =
* Added YouTube playlist support

= 1.1.3 =
* Widget will no longer display errors with the YouTube data feeds are not available

= 1.1.4 =
* Fixed playlist support bug.

= 1.1.5 =
* Now uses curl instead of file_get_contents, etc..

= 1.1.6 =
* Fixed bugged, tagging new version

= 1.1.7 =
* You can now set the video thumbnail width from within the widget options

= 1.1.8 =
* Fixes width setting bugs

= 1.2 =
* Added Video ID capabilities
* Improved getting videos via username and playlist ID
* You can leave the PL prefix on the playlist ID if you like
* Fixed various CSS issues(Chrome, FF, IE 789)
* Fixed titles option bug

= 1.2.1 =
# Fixed titles options bug

= 1.2.2 =
# Turned php all errors off

= 1.2.3 =
* Fixed Video ID label on widget settings

= 1.3 =
* Added an autoplay widget setting
* Added .ysw-youtube class that can be added to elements along with an id attribute with a value equal to the YouTube video's ID hash. Clicking these elements will cause the video to appear.

= 1.3.1 =
* Fixed major bug with both ssl and the .ysw-youtube class

= 1.3.2 =
* Added .ysw-autoplay class to autoplay videos initiated with the .ysw-youtube class.

== Upgrade Notice ==

= 1.0 =
There's no new version to upgrade to!
= 1.1 =
Urgent bug fixes! Update now!
= 1.1.1 =
Fixed a filter bug.
= 1.1.2 =
Added YouTube playlist support
= 1.1.3 =
Widget will no longer display errors with the YouTube data feeds are not available
= 1.1.4 =
Fixed a major playlist support bug
= 1.1.5 =
No longer requires allow_url_fopen to be enabled
= 1.1.6 =
Hotfix for the last release
= 1.1.7 = 
You can now set the video thumbnail width from within the widget options
= 1.1.8 =
Fixes width setting bug
= 1.2 =
This update fixes several bugs and adds the abilities to display a specific video useing only a video ID.
= 1.2.1 =
Hot fix for v1.2 - fixes titles option bug
= 1.2.2 =
This hot fix turns off a debugging mechanism that currently prints php notices and errors to the screen. If you have errors popling up when you use this plugin, download this update.
= 1.2.3 =
Fixes bug in the widget settings.
= 1.3 =
* Added an autoplay widget setting
* Added .ysw-youtube class that can be added to elements along with an id attribute with a value equal
= 1.3.1 =
* This version fixes a major bug in ssl functionality and the .yws-youtube class.
= 1.3.2 =
* This updated adds a .ysw-autoplay class that will autoplay videos initiated by the .ysw-youtube class.
