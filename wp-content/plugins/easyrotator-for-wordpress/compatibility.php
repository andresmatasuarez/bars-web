<?php

/*
Copyright 2011-2012 DWUser.com.
Email contact: support {at] dwuser.com

This file is part of EasyRotator for WordPress.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


// ------------------------------
// Define classes to enable compatibility with each plugin.  See bottom for actual enabling code.
// ------------------------------

// WP Stuff
class EasyRotatorCompatibility_ShortcodesUltimate
{
    private $option_id = 'easyrotator_compatibility_shortcodesultimate_isenabled';

	function EasyRotatorCompatibility_ShortcodesUltimate()
	{
		// --- Register hooks, filters ---

        // Option setter
        add_action( 'admin_init'      , array( $this, 'hook_admin_init' ) );

        // Filter; use higher priority than shortcodes and do_shortcode (10, 11).
        add_filter( 'the_content'      , array( $this, 'filter_the_content' ), 7 );
        //add_filter( 'widget_text'      , array( $this, 'filter_the_content' ), 7 );
    }

    // --- Hooks ---

    function hook_admin_init()
    {
        // Check if Shortcodes Ultimate exists and set option
        if (function_exists('is_plugin_active'))
        {
            $isActive = is_plugin_active('shortcodes-ultimate/shortcodes-ultimate.php') ? 1 : 0;
            $savedVal = get_option($this->option_id);
            if ($savedVal === false || $isActive != $savedVal)
            {
                update_option($this->option_id, $isActive);
            }
        }
    }

    // --- Filters ---

    function filter_the_content($content)
    {
        $isActive = get_option($this->option_id);
        if ($isActive && strpos($content, '[easyrotator') !== false)
        {
            // If [raw]...[/raw] hasn't already been wrapped around [easyrotator]...[/easyrotator], add it.
            $content = preg_replace('|(\[raw\])?(\[easyrotator[^\]]*?\].*?\[/easyrotator\])(\[/raw\])?|si', '[raw]$2[/raw]', $content);
        }
        return $content;
    }

}

// ------------------------------
// Enable necessary compatibility.  If problems arise, simply comment out problematic lines.
// ------------------------------

$easyrotatorCompatibility_shortcodesUltimate = new EasyRotatorCompatibility_ShortcodesUltimate();


// ------------------------------
?>