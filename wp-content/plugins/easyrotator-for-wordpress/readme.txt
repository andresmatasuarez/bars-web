=== EasyRotator for WordPress - Slider Plugin ===
Contributors: DWUser.com
Donate link: http://www.dwuser.com/easyrotator/wordpress/
Tags: rotator, slider, slide, slide show, slideshow, photos, photo, pictures, gallery, photo gallery, image gallery, images, image, media, video, audio, posts, pages, widget, plugin, seo, WordPress slider, templates, mobile, iPad, iPhone, touchscreen, jQuery, Adobe AIR, flash replacement
Requires at least: 2.8
Tested up to: 3.5.1
Stable tag: 1.0.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add beautiful, responsive EasyRotator photo rotators and sliders to your WordPress site in seconds.

== Description ==

[vimeo http://vimeo.com/40726828]

EasyRotator for WordPress helps you create beautiful, responsive photo rotators and sliders for your WordPress site in seconds.  Add rotators to posts and pages, or add rotator widgets to your theme.  Choose photos and set links, select a layout, make customizations, and everything else is handled for you.  Photos can come from your local computer, your WordPress Media Library, or Featured Images of recent posts.

* Drop-dead easy to use (no coding!)
* Over 45 flexible templates included
* No limit to the number of sliders
* Create sliders of recent or featured posts
* Optional video and audio support
* Built-in touchscreen / mobile support
* SEO friendly
* Amazingly easy to use!
* Responsive theme support - just specify an aspect ratio! 

**Requirements:** 

* PHP 5 or higher, WordPress 2.8 or higher
* All major browsers and mobile devices are supported for viewing rotators; IE6 is not.
* Wizard application (for **editing** rotators) requires Adobe AIR (auto-installer included)
* To **edit** rotators, Windows or Mac is required.  [Linux usually works](http://www.dwuser.com/support/easyrotator/kb/linux/), but is not officially supported.

**Important Links:**

* [Detailed Help](http://www.dwuser.com/support/easyrotator/wordpress/)
* [EasyRotator Homepage / Samples](http://www.dwuser.com/easyrotator/)

_EasyRotator is a registered trademark of Magnetic Marketing Corp dba DWUser.com._

== Installation ==

1. Upload the `easyrotator` folder and all of its contents into your plugins directory (`/wp-content/plugins/` by default)
1. Open the WordPress admin panel and go to the 'Plugins' page
1. Activate the new 'EasyRotator for WordPress' item in the list of plugins
1. Follow the on-screen instructions to complete setup
1. Add rotators to your posts and pages via the new Insert EasyRotator button in the post editor.  Add rotators to your theme via the EasyRotator Rotator widget in the Widgets panel.

**To add rotators directly to template files (e.g. in the site header):**

You can use the template function included with the plugin.  To do this, first create your rotator and insert it into a temporary page; this will allow you to obtain the special rotator ID code.  When you insert the rotator in your page, a shortcode will be inserted:

`[easyrotator]erc_00_xxxxxxx[/easyrotator]`

The `erc_00_xxxxxxx` value is the special rotator ID code.  To add this rotator to your template, add the following function call in your template file:

`<?php
easyrotator_display_rotator('erc_00_xxxxxxx');
?>`

Replace `erc_00_xxxxxxx`  with the real code you obtained by creating the rotator.

[More details...](http://www.dwuser.com/support/easyrotator/kb/template-function/)

**For more installation help, see the [detailed installation and usage guide](http://www.dwuser.com/support/easyrotator/wordpress/).**

== Frequently Asked Questions ==

= I'm having trouble getting started with EasyRotator for WordPress; what can I do? =

Please see the detailed installation and usage instructions [on our website](http://www.dwuser.com/support/easyrotator/wordpress/).  If you can't find the answer there, we offer responsive complimentary support.

= I want to add a rotator to my theme's header; how can I do this? =

You can use the template function included with the plugin to add rotators to the header or any other area of your template.  To do this, first create your rotator and insert it into a temporary page; this will allow you to obtain the special rotator ID code.  When you insert the rotator in your page, a shortcode will be inserted:

`[easyrotator]erc_00_xxxxxxx[/easyrotator]`

The `erc_00_xxxxxxx` value is the special rotator ID code.  To add this rotator to your template, add the following function call in your template file:

`<?php
easyrotator_display_rotator('erc_00_xxxxxxx');
?>`

Replace `erc_00_xxxxxxx`  with the real code you obtained by creating the rotator.

[More details...](http://www.dwuser.com/support/easyrotator/kb/template-function/)

= How can I customize the layout? =

You can read about making both basic and advanced customizations to layouts [in this article](http://www.dwuser.com/support/easyrotator/kb/customize-layout/).  You can customize positioning, fonts, colors, and even button images.  You'll also find instructions for modifying the transition speed.

= I'm using EasyRotator for WordPress on a RTL (right-to-left) site, and the images don't appear =

When working with an RTL site, you need to add the following CSS to your theme's stylesheet:

`div.dwuserEasyRotator {
   direction: ltr;
}`

This will ensure that the rotators appear and function properly.  (Update: This code is now automatically applied for you in most cases.)

= How can I configure the way photos are scaled and/or cropped? =

Scaling and cropping are set when you apply a layout template in the Layout/Presentation section of the editor.  To learn more about these options, see [this article](http://www.dwuser.com/support/easyrotator/kb/photo-cropping/).

= How can I change the alignment of my rotator? =

For information about modifying rotator alignment, see [this article](http://www.dwuser.com/support/easyrotator/kb/alignment/).

= I'm having trouble with an "AIR Download Error" all of a sudden =

If EasyRotator was working properly and now you're suddenly receiving installation prompts and an "AIR Download Error" message, see [this article](http://www.dwuser.com/support/easyrotator/kb/chrome-air-problem/).

= I'm having trouble with photos shifting or appearing with borders =

This is usually caused by overly-broad declarations in your stylesheet.  [Open a support ticket](http://www.dwuser.com/#bottomBoxes) and include the URL of your page; we'll help you identify the code you need to add.

= How can I create an images-only rotator? =

To learn how to create an images-only layout for your rotator, see [this article](http://www.dwuser.com/support/easyrotator/kb/images-only-layout/).

= Is it possible to add Pinterest sharing buttons to my rotators? =

We recently released an add-on that enables social sharing for rotators.  By default, options are included for Pinterest, Twitter and Facebook; these can be customized if desired.  To learn more, see [this article](http://www.dwuser.com/education/content/free-add-on-social-sharing-for-easyrotator/).

= What types of video can I use in a rotator? =

To learn more about what video types are supported and how to integrate video, see [this article](http://www.dwuser.com/support/easyrotator/kb/video-faqs/).

= How can I make links open in a new window? =

To learn about setting link targets, see [this article](http://www.dwuser.com/support/easyrotator/kb/link-target/).

= How can I transfer rotators from one site to another? =

If you need to move rotators from a development to production site, or an old site to a new site, see [this article](http://www.dwuser.com/support/easyrotator/kb/transfer-rotators/) for directions.

= How can I link slide titles to corresponding posts? =

When using dynamic data in a rotator, you may want to link the image titles to their corresponding posts.  To learn about how to enable this setting, see [this article](http://www.dwuser.com/support/easyrotator/kb/automatically-link-title/).

= How can I customize the RSS feed view? =

To learn more about the way rotators appears in RSS feed view and how to customize this view, see [this article](http://www.dwuser.com/support/easyrotator/kb/rss-compatibility/). 

= Does EasyRotator work properly on SSL sites? =

Yes!  The most recent versions of the plugin are automatically compatible with SSL sites.  If a page is being viewed over SSL, all rotators will be updated appropriately to avoid mixed-content warnings.

== Screenshots ==
1. The Insert EasyRotator button in the Post/Page editor
2. The Insert EasyRotator dialog, ready to create our first rotator
3. Creating our first rotator
4. The EasyRotator editor application
5. Rotators can dynamically display WordPress posts - either recent ones, or from specific tags/categories.
6. The finished rotator in the preview window
7. The finished rotator inserted in the WordPress editor.  Preview, edit and management options are easily accessible using the box below the editor.
8. The EasyRotator Rotator widget lets you easily add rotators to widget-compatible themes

== Changelog ==
= 1.0.7 =
* Compatibility Enhancement: Built-in warning for new Mac Chrome / AIR compatibility issue.
* Bug Fix: Don't show editor application installation prompt to non-editors.
* Compatibility Enhancement: Avoid issues with site-relative file links on unusual configurations.

= 1.0.6 =
* Compatibility Enhancement: Pre-emptively disable mod_security POST filtering when uploading rotators, via custom .htaccess file.
* Compatibility Enhancement: When API calls are made, enable error output after API call has been authenticated.  This allows for better debug messages, especially when out-of-memory errors occur.
* Bug Fix: Accidentally was logging rotator HTML to PHP log.

= 1.0.5 =
* Compatibility Enhancement: Built-in warning for new Windows Chrome / AIR compatibility issue.
* Bug Fix: Avoid 2130 errors when launching EasyRotator manager in WordPress.

= 1.0.4 =
* Enhancement: Compatibility with RSS feeds.  Instead of rendering the full rotator code, only the first photo will be shown.  A special comment is inserted, allowing for customization of this display (see FAQs for documentation).
* Enhancement: New advanced option that allows for explicitly setting the photo sizes to be loaded for Main images and Thumbnail images when using dynamic data.  Note that installation of the latest editor application update is required to access this feature.
* Enhancement: Better compatibility with shared hosting system where non-fatal errors occur or analytics/ad code is appended to each API request.
* Enhancement: Better debug messages when errors occur, including links to help content.
* Compatibility Enhancement: Enhanced compatibility with PageLines plugin; the PageLines plugin is missing some CSS, so we try to avoid undesired interactions.

= 1.0.3 =
* Internal change: Now moving uploaded rotator packages out of the temp directory before unzipping.  This improves compatibility for shared hosts where access to the temp directory is restricted.

= 1.0.2 =
* Enhancement: New automatic support for SSL viewing

= 1.0.1 =
* Enhancement: New automatic compatibility with Shortcodes Ultimate (automatically adds "raw" shortcode)
* Bug Fix: Fix bugs with background audio.
* Bug Fix: Helpful error message now displayed when uploads folder doesn't properly exist (had been empty string)

= 1.0.0 =
* First release