<?php
/*
Plugin Name: EasyRotator for WordPress
Plugin URI: http://www.dwuser.com/easyrotator/wordpress/
Description: Add professional, customizable photo sliders to your site in seconds.  Powered by the EasyRotator application from DWUser.com.
Version: 1.0.7
Author: DWUser.com
Author URI: http://www.dwuser.com/
License: GPL v2 or later
*/

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


// --------------------------------------------------
// Check wordpress version
global $wp_version;
if (version_compare($wp_version, '2.8', '<'))
{
	// Try to display a warning.  This will only work on 2.0.11+, but hey... you've got some serious gall if you're using <2.
	function easyrotator_incompatibility_admin_notice()
	{
		?>
		<div class="error">
			<p>
				<span style="font-weight: bold;">Error!</span> 
				The EasyRotator plugin requires WordPress 2.8 or higher to function properly.  Please head over to <a href="http://wordpress.org/download/" target="_blank">wordpress.org</a> and update your installation.
			</p>
		</div>
		<?php
	}
	add_action( 'admin_notices', 'easyrotator_incompatibility_admin_notice' );
}
else
{
// --------------------------------------------------


// Start up the engine
require_once(dirname(__FILE__) . '/engine/main.php');

// WP Stuff
class EasyRotator
{
	function EasyRotator()
	{
		// --- Initialization ---
		
		// note that this automatically matches http/https.
		$this->url         = dirname(plugins_url('easyrotator.php', __FILE__)) . '/';
		
		
		// --- Register hooks ---
		
		// Shortcode
		add_shortcode( 'easyrotator', array( $this, 'hook_shortcode' ) );
		
		// Editor
		add_action( 'init'            , array( $this, 'hook_init_load_scripts' ) );
		add_action( 'init'            , array( $this, 'hook_init_configure_editor' ) ); // update post editor
		add_action( 'admin_init'	  , array( $this, 'hook_admin_init_add_meta_boxes' ) ); // append post editor meta box; WP3.0+ can use add_action( 'add_meta_boxes', ... )
		add_action( 'admin_init'      , array( $this, 'hook_admin_init' ) );
		add_action( 'admin_menu'      , array( $this, 'hook_admin_menu' ) );
		add_action( 'admin_head'      , array( $this, 'hook_admin_head' ) );
		add_action( 'admin_print_styles' , array( $this, 'hook_admin_print_styles' ) );
		add_action( 'admin_notices'   , array( $this, 'hook_admin_notices') );
		add_action( 'admin_footer'    , array( $this, 'hook_admin_footer' ) );
		add_action( 'admin_footer'    , array( $this, 'editor_easyrotator_manage_dialog' ) );
		add_action( 'admin_footer'    , array( $this, 'admin_inline_help_content') );
		
		add_action( 'edit_form_advanced'   , array( $this, 'editor_quicktags' ) );
		add_action( 'edit_page_form'       , array( $this, 'editor_quicktags' ) );
		
		add_action( 'edit_form_advanced'   , array( $this, 'editor_edit_monitor' ) );
		add_action( 'edit_page_form'   	   , array( $this, 'editor_edit_monitor' ) );
		
		add_action( 'wp_dashboard_setup'   , array( $this, 'hook_admin_dashboard_setup' ) );
		
		// Admin AJAX
		add_action( 'wp_ajax_easyrotator_welcome_notice_hide'    , array( $this, 'ajax_easyrotator_welcome_notice_hide' ) );
		add_action( 'wp_ajax_easyrotator_hide_tooltip'    , array( $this, 'ajax_easyrotator_hide_tooltip' ) );
		add_action( 'wp_ajax_easyrotator_get_rotator_names'    , array( $this, 'ajax_easyrotator_get_rotator_names' ) );
		add_action( 'wp_ajax_easyrotator_dashboard_widget_content'	, array( $this, 'ajax_easyrotator_dashboard_widget_content' ) );
		add_action( 'wp_ajax_easyrotator_help_content'	, array( $this, 'ajax_easyrotator_help_content' ) );
		add_action( 'wp_ajax_easyrotator_get_lovebar_config', array( $this, 'ajax_easyrotator_get_lovebar_config' ) );
		add_action( 'wp_ajax_easyrotator_lovebar_actions', array( $this, 'ajax_easyrotator_lovebar_actions' ) );
	}
	
	function hook_init_load_scripts()
	{
		// Load any needed scripts
	}
	
	function hook_admin_init()
	{
        $includeJQUICSS = true;
        if (stripos(@$_GET['page'], 'pagelines') === 0)
            $includeJQUICSS = false;

        if ($includeJQUICSS)
		    wp_enqueue_style(  'jquery-ui-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
            // to investigate: wp_enqueue_style( 'wp-jquery-ui-dialog' );
		        
		// All er-specific admin css
        wp_register_style( 'easyrotator-plugin-admin-css', $this->url . 'css/easyrotator_admin.css', array(), '1.0.0', 'all' );
		wp_enqueue_style( 'easyrotator-plugin-admin-css' );
		
		// JS
		wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script( 'jquery-ui-dialog' );
        wp_enqueue_script( 'swfobject' );
        
	}
	
	function hook_admin_menu()
	{
		// Setup primary menu
		$primary_slug = 'easyrotator_admin_overview';
		$primary_title = __('EasyRotator Administration / Options Page');
		$hookname = add_menu_page( $primary_title, 'EasyRotator', 'edit_posts', $primary_slug, array( $this, 'admin_page_overview' ), 'div' );

		// Add submenu pages - first is just mirror of main.
		add_submenu_page( $primary_slug, $primary_title, 'Overview', 'edit_posts', $primary_slug, array( $this, 'admin_page_overview' ) );
		$hookname_sub1 = add_submenu_page( $primary_slug, 'EasyRotator - Help', 'Help', 'edit_posts', 'easyrotator_admin_help', array( $this, 'admin_page_help' ) );
		
		// Allow for admin-page-specific CSS
		add_action( 'admin_print_styles-' . $hookname, array( $this, 'hook_admin_add_custom_styles' ) );
		add_action( 'admin_print_styles-' . $hookname_sub1, array( $this, 'hook_admin_add_custom_styles' ) );
		
		// Allow for admin-page-specific footer js
		add_action( 'admin_footer-' . $hookname, array( $this, 'hook_admin_add_custom_footer_scripts' ) );
		add_action( 'admin_footer-' . $hookname_sub1, array( $this, 'hook_admin_add_custom_footer_scripts' ) );
	}
	
	function hook_admin_add_custom_styles()
	{
		// [Now, just always adding the er css in hook_admin_init above]
	}
	
	function admin_page_overview()
	{
		global $erwp;
		
		$actionAPI = 'easyrotator_admin_general_apiupdate';
		
		// Get current API Key
		$curKey = $erwp->getAPIKey();
		
		if ( !empty($_POST) && check_admin_referer( $actionAPI, '_wpnonce_' . $actionAPI ) )
		{
            if (current_user_can('manage_options'))
            {
                // Try to reset the apikey option...
                $curKey = $erwp->resetAPIKey();

                // Display confirmation message
                ?>
                <div class="updated fade">
                    <p><strong>
                        <?php _e('API Key has been reset.&nbsp; New value may be found below.'); ?>
                    </strong></p>
                </div>
                <?php
            }
            else
            {
                // Display error message
                ?>
                <div class="error">
                    <p><strong>
                        <?php _e('You do not have permission to reset the API Key.&nbsp; Please contact a site administrator for assistance.'); ?>
                    </strong></p>
                </div>
            <?php
            }
		} // end $actionAPI processing
		
		// Write out the form
		?>

			<div class="wrap easyrotator_admin_wrap">

				<div id="icon-easyrotator-adminheader" class="icon32 icon32-easyrotator-admin"><br /></div>
				<h2>EasyRotator Administration / Options</h2>
				<div class="easyrotator_admin_subheader">Add professional, customizable photo sliders to your site in seconds with <a href="http://www.dwuser.com/easyrotator/" target="_blank">EasyRotator</a> from <a href="http://www.dwuser.com/" target="_blank">DWUser.com</a>.</div>
				
				<form name="<?php echo($actionAPI); ?>" action="<?php echo( esc_attr( str_replace( '%7E', '~', $_SERVER['REQUEST_URI']) ) ); ?>" method="POST" id="<?php echo($actionAPI); ?>" onsubmit="return confirm('Are you sure you wish to reset the API Key?  You may need to update any saved values within the EasyRotator desktop application.');">
					
					<?php wp_nonce_field( $actionAPI, '_wpnonce_' . $actionAPI ); ?>
					
					<h3>Getting Started</h3>
					<p>
						To create your first rotator, use the Insert EasyRotator button on the Post/Page editor. It's super-simple from there!
					</p>
					<p>
						To add a rotator widget to your template, use the EasyRotator Rotator widget on the <a href="widgets.php">Widgets</a> page.
					</p>
					
					<h3>Advanced: API Information</h3>
					<p>
						The EasyRotator API is used for communication between the EasyRotator desktop application and your WordPress installation.  The secret API Key is automatically generated 
						when you install and activate the EasyRotator plugin.  There's usually no reason to 
						modify the key, but if your computer or WordPress installation has been compromised it's a wise idea to reset the key.  You can also occasionally 
						reset it as a preemptive security measure.  Resetting the key will not affect existing rotators.
					</p>
					<p style="font-style: italic; color: #999;">
						Note for advanced users: If you're having trouble launching the EasyRotator desktop application via the EasyRotator button in the Post/Page Editor, the information below can be used to manually add a connection within the desktop application.
					</p>
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row">
									<label for="easyrotator_admin_apiurl">API URL:</label>
								</th>
								<td>
									<input type="text" readonly="readonly" value="<?php echo( esc_attr( $this->url . 'engine/main.php' ) ); ?>" name="easyrotator_admin_apiurl" id="easyrotator_admin_apiurl" class="large-text code" onclick="jQuery(this).focus();jQuery(this).select();" />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label for="easyrotator_admin_apikey">Current API Key:</label>
								</th>
								<td>
									<input type="text" readonly="readonly" value="<?php echo( esc_attr( $curKey ) ); ?>" name="easyrotator_admin_apikey" id="easyrotator_admin_apikey" class="larger-text code" onclick="jQuery(this).focus();jQuery(this).select();" />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"></th>
								<td>
                                    <?php
                                    if (current_user_can('manage_options')):
                                    ?>
                                        <input type="submit" value="<?php _e('Reset API Key'); ?>" class="button-secondary" />
                                        <?php
                                    else:
                                        echo('<em>If you believe the API key needs to be reset, please contact a site administrator.</em>');
                                    endif;
                                    ?>
                                </td>
							</tr>
						</tbody>
					</table>
					
					<h3>Need More Help?</h3>
					<p>
						For detailed assistance with using EasyRotator for WordPress, see the <a href="admin.php?page=easyrotator_admin_help">Help</a> page.
					</p>
					
				</form>
				
			</div>
		
		<?php
	}
	
	function admin_page_help()
	{
		//include_once(dirname(__FILE__) . '/template/admin_page_help.php');
		?>
		
		<div class="wrap easyrotator_admin_wrap">
			<div id="icon-easyrotator-adminheader" class="icon32 icon32-easyrotator-admin"><br /></div>
			<h2>EasyRotator - Help</h2>
			<div class="easyrotator_admin_subheader">Add professional, customizable photo sliders to your site in seconds with <a href="http://www.dwuser.com/easyrotator/" target="_blank">EasyRotator</a> from <a href="http://www.dwuser.com/" target="_blank">DWUser.com</a>.</div>
			
			<div id="easyrotator_helppage_content_wrap"><p style="color:#999;font-style:italic;">Loading...</p></div>
			
			<script type="text/javascript">
			jQuery(function($) {
				var data = {
							action: 'easyrotator_help_content',
							security: '<?php echo( wp_create_nonce('nonce_easyrotator_help_content') ); ?>'
						};
				setTimeout(function() {
					$.post(ajaxurl, data, function(result){  
						$('#easyrotator_helppage_content_wrap').html(result || '<p style="font-style: italic;">Error Loading.  <a href="http://www.dwuser.com/support/easyrotator/wordpress/" target="_blank">See here</a>.</p>');
					});
				}, 50); // a little delay to avoid overload
			});
			</script>
		</div>
		<?php
	}
	
		function ajax_easyrotator_help_content()
		{
			global $wp_version;
			check_ajax_referer('nonce_easyrotator_help_content', 'security');
			
			$response = wp_remote_get( 'http://easyrotatorwp.dwuser.com/support/easyrotator/wordpress/plugin-ajax/admin-help.php?version=' . $wp_version );
			if (is_wp_error( $response ))
			{
				echo('<p style="font-style: italic;">Error loading content.  <a href="http://www.dwuser.com/support/easyrotator/wordpress/" target="_blank">See here</a>.</p>');
				die();
			}
			else
			{
				echo( $response['body'] );
				die();
			}
		}
	
	
	function hook_admin_head()
	{
		// WAS: Append some general CSS; not enough to warrant an external file via admin_print_styles.  THEN: moved it all to external file.  Moved version-specific styles below to correct hook.
	}
	
	function hook_admin_print_styles()
	{
		// Add version-specific doctoring-up CSS
		global $wp_version;
		
		echo('<style type="text/css">' . "\n");
		if (version_compare($wp_version, '2.9', '<')) // TODO: Possible expand versions?
		{
			?>
			#easyrotator_edit_shortcut_box .easyrotator_rotator_list td {
				padding-top: 8px;
				padding-bottom: 8px;
			}
			<?php
		}
		if (version_compare($wp_version, '3.2', '<'))
		{
			?>
			#easyrotator_edit_shortcut_box {
				background-color: #F3F3F3;
			}
			#easyrotator_edit_shortcut_box span.easyrotator_meta_title_icon
			{
				margin-bottom: -2px;
				margin-top: -1px;
			}
			<?php
		}
		echo('</style>');
		
		// Add IE9(+?) specific fixes to fix SWF rendering... thx MS for nuking conditional comments so we have to do this hacky sniffing baloney.
		// UPDATE: Seems to be related to virtualized 3d acceleration.  Not needed for now, apparently.  May be restored in future.

		// Here's the un-minified version for ref:
		/*
		#easyrotator_edit_shortcut_box .er_edit_btn_canvasWrap.er_collapsed {
			width: auto !important;
			height: auto !important;
			overflow: visible !important;
			visibility: hidden !important;
			position: absolute !important;
			z-index: -1000 !important;
		}
		#easyrotator_edit_shortcut_box .er_edit_btn_canvasWrap.er_expanded {
			overflow: visible !important;
			position: relative !important;
			visibility: visible !important;
		}


		div.easyrotator_widget_form_wrap .er_edit_btn_canvasWrap.er_collapsed {
			width: auto !important;
			height: auto !important;
			overflow: visible !important;
			position: absolute !important;
			visibility: hidden !important;
			z-index: -1000 !important;
		}
		div.easyrotator_widget_form_wrap .er_edit_btn_canvasWrap.er_expanded {
			overflow: visible !important;
			position: relative !important;
			visibility: visible !important;
		}
		*/
		/*
		?>
		<div 
		<script type="text/javascript">
		(function($)
		{
			if ($.browser.msie && parseInt($.browser.version) > 8)
			{
				$('head').append('<style type="text/css">#easyrotator_edit_shortcut_box .er_edit_btn_canvasWrap.er_collapsed{width:auto!important;height:auto!important;overflow:visible!important;visibility:hidden!important;position:absolute!important;z-index:-1000!important}#easyrotator_edit_shortcut_box .er_edit_btn_canvasWrap.er_expanded{overflow:visible!important;position:relative!important;visibility:visible!important}div.easyrotator_widget_form_wrap .er_edit_btn_canvasWrap.er_collapsed{width:auto!important;height:auto!important;overflow:visible!important;position:absolute!important;visibility:hidden!important;z-index:-1000!important}div.easyrotator_widget_form_wrap .er_edit_btn_canvasWrap.er_expanded{overflow:visible!important;position:relative!important;visibility:visible!important}</style>');
			}
		})(jQuery);
		</script>
		<?php
		*/
	}
	
	
	// Show 'em...
	function hook_admin_notices()
	{
		global $erwp, $current_user, $wp_version;
		$user_id = $current_user->ID;
		
		// --- Check configuration and show relevant notices / errors ---
		$contentDirStatus = $erwp->createContentDirIfNeeded();
		
		/*
		// DEBUG!
		echo('<div class="updated"><p>');
		var_dump(get_user_option('rich_editing'));
		echo('</p></div>');
		*/

        // Make sure this user is allowed to edit
        if ( !current_user_can('edit_posts') || !current_user_can('edit_pages') )
            return;

		// Show the welcome/setup notice if needed
		if (get_user_option('easyrotator_welcome_notice_hide') !== 'yes')
		{
			include( dirname(__FILE__) . '/template/welcome.php' );
		}
		
		// Show the lovebar if needed
		$showtime = (int) get_user_option('easyrotator_lovebar_showtime');
		if ($showtime > 0 && time() >= $showtime)
		{
			$lovebarTitle = get_user_option('easyrotator_lovebar_title');
			include( dirname(__FILE__) . '/template/lovebar.php' );
		}
		
		// See if user dir is OK
		if (!$contentDirStatus['success'])
		{
			// Problem!  Show an error.
			echo('<div class="error easyrotator_error_notice"><p><span style="font-weight: bold;">EasyRotator Error!</span></p><p>' . $contentDirStatus['message'] . '</p></div>');
		}
	}
	
		// ...and let the user hide 'em.
		function ajax_easyrotator_welcome_notice_hide()
		{
			check_ajax_referer('nonce_easyrotator_welcome_notice_hide', 'security');

			global $current_user;
	        $user_id = $current_user->ID;
			update_user_option($user_id, 'easyrotator_welcome_notice_hide', 'yes');

			echo('{success:true, message:\'Preference updated.\'}');
			die();
		}
		
		// ... and manage them
		function ajax_easyrotator_lovebar_actions()
		{
			check_ajax_referer('nonce_easyrotator_lovebar_actions', 'security');
			
			global $current_user;
	        $user_id = $current_user->ID;
			
			$showtime = (int) get_user_option('easyrotator_lovebar_showtime');
			$showurl = get_user_option('easyrotator_lovebar_showurl');
			
			$action = @$_POST['eraction']; // remindLater, hide, or content
			if ($action == 'remindLater')
			{
				if ($showtime > 0)
					update_user_option($user_id, 'easyrotator_lovebar_showtime', (time() + 86400) . ''); // one day out
				echo('1');
			}
			elseif ($action == 'hide')
			{
				update_user_option($user_id, 'easyrotator_lovebar_showtime', '-2');
				echo('1');
			}
			else // $action == 'content'
			{
				$response = wp_remote_get( $showurl );
				if (is_wp_error( $response ))
				{
					echo('<p style="font-style: italic;">Error loading content.</p>');
				}
				else
				{
					echo( $response['body'] );
				}
			}
			
			die();
		}
		
		
	function hook_admin_dashboard_setup()
	{
		// Add the easyrotator dashboard widget (disabled currently)
		////// wp_add_dashboard_widget('easyrotator_dashboard_widget', 'EasyRotator', array( $this, 'dashboard_widget_function' ));
	}
	
		function dashboard_widget_function()
		{
			// Quicklinks, ajax content
			?>
			<p><span style="font-weight: bold;">Quicklinks:</span>&nbsp; <a href="admin.php?page=easyrotator_admin_help">EasyRotator Help</a></p>
			
			<p style="font-weight: bold;">Latest News:</p>
			<div class="easyrotator_dashboard_widget_content_wrap"><p style="color:#999;font-style:italic;">Loading...</p></div>
			<script type="text/javascript">
			jQuery(function($) {
				var data = {
							action: 'easyrotator_dashboard_widget_content',
							security: '<?php echo( wp_create_nonce('nonce_easyrotator_dashboard_widget_content') ); ?>'
						};
				setTimeout(function() {
					$.post(ajaxurl, data, function(result){  
						$('div.easyrotator_dashboard_widget_content_wrap').html(result || '<p style="font-style: italic;">Error Loading.</p>');
					});
				}, 50); // a little delay to avoid overload
			});
			</script>
			<?php
		}
		
		function ajax_easyrotator_dashboard_widget_content()
		{
			global $wp_version;
			check_ajax_referer('nonce_easyrotator_dashboard_widget_content', 'security');
			
			$response = wp_remote_get( 'http://easyrotatorwp.dwuser.com/support/easyrotator/wordpress/plugin-ajax/dashboard-widget.php?version=' . $wp_version );
			if (is_wp_error( $response ))
			{
				echo('<p style="font-style: italic;">Error loading content.</p>');
				die();
			}
			else
			{
				echo( $response['body'] );
				die();
			}
		}
		
	
	function hook_admin_footer()
	{
		// If needed, get lovebar info via ajax
		if (get_user_option('easyrotator_lovebar_showtime') == '-3')
		{
			?>
			<script type="text/javascript">
			jQuery(function($) {
				var data = {
							action: 'easyrotator_get_lovebar_config',
							security: '<?php echo( wp_create_nonce('nonce_easyrotator_get_lovebar_config') ); ?>'
						};
				setTimeout(function() {
					$.post(ajaxurl, data, function(result){  
						// success
					});
				}, 500); // a little delay to avoid overload
			});
			</script>
			<?php
		}
	}
	
		function ajax_easyrotator_get_lovebar_config()
		{
			check_ajax_referer('nonce_easyrotator_get_lovebar_config', 'security');
			
			global $current_user;
		    $user_id = $current_user->ID;
			
			$response = wp_remote_get( 'http://easyrotatorwp.dwuser.com/lovebar/display.txt' ); // no $wp_version at this time
			if (is_wp_error( $response ))
			{
				// Failed.  We'll automatically keep trying on future requests.
				die();
			}
			else
			{
				// Note that we received a response
				$parts = explode( ',' , $response['body'], 3 ); // allow commas in last chunk
				if (count($parts) == 3)
				{
				
					// See what type of response we have - show or no?
					$showtime = '-1';
					$timeResponse = $parts[0];
					if ($timeResponse != '-1' && strlen($timeResponse) > 2)
					{
						$activateTime = (int) get_user_option('easyrotator_activatetime');
						$showtime = $activateTime + (int) $timeResponse;
						
						$urlResponse = $parts[1];
						update_user_option($user_id, 'easyrotator_lovebar_showurl', $urlResponse);
						
						$title = $parts[2];
						update_user_option($user_id, 'easyrotator_lovebar_title', $title);
					}
					update_user_option($user_id, 'easyrotator_lovebar_showtime', $showtime . '');
					
				} // else error... continue trying in future.
				die();
			}
		}
		
	
	function admin_inline_help_content()
	{
		// --- This method adds inline help content to pages ---
		// Add post/page editor first-exposure tooltips; render only if post/page editing or widget mode on admin page AND they haven't been hidden
	    $pageName = basename( $_SERVER['PHP_SELF'] );
	    $isPostPageEditor = in_array( $pageName, array( 'post-new.php', 'page-new.php', 'post.php', 'page.php' ) );
	    if ( $isPostPageEditor  &&  get_user_option('easyrotator_help_tooltips_postpagebtn_hide') !== 'yes' )
	    {
	    	?>
	    	<script type="text/javascript">
	    	(function($)
	    	{
	    		$(function(){
	    			var targetSelector;
	    			if ($('#edButtonHTML').hasClass('active') || $('#wp-content-wrap').hasClass('html-active'))
					{
						// HTML mode
						targetSelector = '#ed_easyrotator.ed_button';
					}
					else if ($('#edButtonPreview').hasClass('active') || $('#wp-content-wrap').hasClass('tmce-active'))
					{
						// Visual mode
						targetSelector = '#content_easyrotator.mceButton';
					}
					
					if (targetSelector)
					{
						// Set it up
						var ttip = $('<div class="easyrotator_tooltip_box" style="color: #555; padding-top: 6px; width: 221px;"><div class="easyrotator_tooltip_arrowGray"></div><div class="easyrotator_tooltip_arrowWhite"></div>' +
										'<p style="text-align: center;"><img src="<?php echo( $this->url . 'img/er_small_logo.png' ); ?>" alt="EasyRotator" width="144" height="30" /></p>' +
									    '<p><strong>Welcome!</strong> This is the <span style="wascolor: #222;">Insert&nbsp;EasyRotator</span> button, which you\'ll use to insert rotators into your posts and pages.</p>' +
									    '<p style="text-align: center; padding-top: 6px;"><a class="easyrotator_tooltip_close_btn button-secondary" href="#">OK</a></p>' +
									'</div>').appendTo('body');
						
						var destroy = function(immediate)
						{
							if (immediate)
								ttip.remove();
							else
								ttip.fadeOut(400, function(){
									setTimeout(function(){
										ttip.remove();
									}, 0);
								});
						};
						
						// Start checking for position, show on page load
						$(window).load(function(){
							
							var setup = function(target)
							{
								var position = function()
								{
									var offset = target.offset();
									offset.left += target.width()/2; // centered at bottom
									offset.top += target.height();
									// ttip positioning additional: x=-24px, y=+7px
									ttip.css({
										left: offset.left-26,
										top: offset.top+7+2
									});
								};
								
								var lastY = -1;
								var updatePositionAsNeeded = function()
								{
									if (!ttip || ttip.length == 0) return;
									
									var curPostY = ($('form#post')[0] || {}).offsetTop; // much more efficient than jq, if buggy in ie6,7
									if (curPostY != lastY)
									{
										lastY = curPostY;
										position();
									}
									
									// reposition as needed
									setTimeout(function(){
										updatePositionAsNeeded();
									}, 500);
								}
								updatePositionAsNeeded();
								
								ttip.show();
								
							};
							
							var i=0;
							var checkForSetup = function(targetSelector)
							{
								var target = $(targetSelector);
								if (target.length > 0)
									setup(target);
								else if (i++ < 20) // only try for 20 sec
									setTimeout(checkForSetup, 1000, targetSelector);
							};
							checkForSetup(targetSelector);
							
						});
						
						// Hide on mode change, btn click
						$('#edButtonHTML, #edButtonPreview, a#content-html, a#content-tmce').click(function(){
							destroy(true);
						});
						ttip.find('a.easyrotator_tooltip_close_btn').click(function(e){
							e.preventDefault();
							destroy();
							
							// ajax to not show again
							var data = {
								action: 'easyrotator_hide_tooltip',
								security: '<?php echo(wp_create_nonce('nonce_easyrotator_hide_tooltip')); ?>',
								tooltip: 'postPageBtn_intro'
							};
							$.post(ajaxurl, data, function(result){ });
							
						});
					} // end if target.length>0
					
	    		});
	    	})(jQuery);
	    	</script>
	    	<?php
	    }
	    
	    
	    // Add hover effect to EasyRotator button in wysiwyg editor view
	    if ( $isPostPageEditor )
	    {
	    	?>
	    	<script type="text/javascript">
	    	(function($){
	    		$(window).load(function(){
	    			var tries = 0;
	    			var setup = function(){
	    				// Setup hover
						var btnImg = $('#content_easyrotator.mceButton img.mceIcon');
						if (btnImg.length > 0)
						{
							var srcUp = btnImg.attr('src');
							var srcOver = srcUp.split('er_icon_20_bw.png').join('er_icon_20.png');
							var img = $('<img src="' + srcOver + '" />'); // preload
							$('#content_easyrotator.mceButton').hover(function(){
								btnImg.attr('src', srcOver);
							}, function(){
								btnImg.attr('src', srcUp);
							});
						}
						else if (tries++ < 10) // try for 10 secs, then give up.
						{
							setTimeout(setup, 1000);
						}
	    			};
	    			setup();
	    		});
	    	})(jQuery);
	    	</script>
	    	<?php
	    }
	    
	}
	
		function ajax_easyrotator_hide_tooltip()
		{
			check_ajax_referer('nonce_easyrotator_hide_tooltip', 'security');

			$tooltip = @$_POST['tooltip']; // what to hide
			
			global $current_user;
	        $user_id = $current_user->ID;
			if ($tooltip == 'postPageBtn_intro')
			{
				update_user_option($user_id, 'easyrotator_help_tooltips_postpagebtn_hide', 'yes');
			}

			echo('{success:true, message:\'Preference updated.\'}');
			die();
		}
		
	
	function hook_admin_add_custom_footer_scripts()
	{
		// Write out the custom script to reposition the div.easyrotator_admin_subheader immediately after the h2, incase it has been moved by a .updated alert.
		?>
		<script type="text/javascript">
		(function($)
		{
			var update = function() {
				var subheader = $('div.easyrotator_admin_wrap div.easyrotator_admin_subheader');
				subheader.insertAfter(subheader.parent().children('h2:first'));
			};
			update();
			setTimeout(update,1);
			setTimeout(update,1000);
		})(jQuery);
		</script>
		<?php
	}
	
	
		
	
	// Ref: http://codex.wordpress.org/TinyMCE_Custom_Buttons, http://www.mattvarone.com/wordpress/adding-tinymce-buttons-to-wordpress-3-2-fullscreen-mode/
	function hook_init_configure_editor()
	{
		// Make sure this user is allowed to edit
		if ( !current_user_can('edit_posts') || !current_user_can('edit_pages') )
			return;
			
		// Continue only if we're in rich-edit mode ... [no longer, due to fs buttons in 3.2+]
		if ( get_user_option('rich_editing' ) == 'true' ) 
		{
			add_filter( 'mce_buttons'         , array( $this, 'editor_tinymce_button' ) );
			add_filter( 'mce_external_plugins', array( $this, 'editor_tinymce_plugin' ) );
			// if additional content is needed: add_filter( 'the_editor'          , array( $this, 'editor_hidden_field'   ) );
		}
		
		// Add fullscreen buttons for 3.2+
		add_filter( 'wp_fullscreen_buttons', array( $this, 'editor_tinymce_fsbutton' ) );
	}
	
	function editor_tinymce_fsbutton($buttons)
	{
		$buttons[] = 'separator';
		$buttons['easyrotator'] = array(
			'title' => 'Add EasyRotator To This Post...',
			'onclick' => 'easyrotator_fs_dialog_launch();',
			'both' => true
		);
		return $buttons;
	}
	
	function editor_tinymce_button($buttons)
	{
		array_push($buttons, 'separator', 'easyrotator');
		return $buttons;
	}
	
	function editor_tinymce_plugin($plugin_array)
	{
		$plugin_array['easyrotator'] = $this->url . 'js/tinymce/editor-plugin.js';
		return $plugin_array;
	}
	
	
	/**
	 * This method is used whenever on the admin post/page editor pages.  Used to write out the manager dialog, 
	 * which is launched via the tinymce button or the quicktag button.
	 **/
	function editor_easyrotator_manage_dialog() { //was editor_tinymce_plugin_dialog()
		global $erwp;
		
		// Render only if post/page editing or widget mode on admin page
	    $pageName = basename( $_SERVER['PHP_SELF'] );
	    if ( in_array( $pageName, array( 'post-new.php', 'page-new.php', 'post.php', 'page.php', 'widgets.php' ) ) ) { // TOASK: good check, esp widgets?
	        
	        $engineURL = $this->url . 'engine/main.php';
			$apiInfo = $this->api_getInfo();
	        $inWidgetMode = ($pageName == 'widgets.php');
	        
	        echo('<div style="display:none;">
    				
    				<script type="text/javascript">
    				easyrotator_tinymce_dialog_launch = function()
    				{
    					');
    					
    					// Switch output based on whether we have any errors right now...
    					$contentDirStatus = $erwp->createContentDirIfNeeded();
    					if ($contentDirStatus['success'])
    					{
    						// Allow the dialog to be launched
    						echo(
	    					'jQuery(\'#easyrotator_manage_dialog\').dialog({
								title: \'Select a Rotator To Insert\',
								width: 700,
								height: 500,
								zIndex: 159999 // need to go over fs overlay if applicable (which is 14999)
							}).dialog("open");
							');
						}
						else
						{
							// Show the error.
							echo(
							'alert("There\'s currently a problem with your EasyRotator installation!  For details, see the red notice at the top of the current page.\n\nCorrect the error, then reload this page and try again.")'
							);
						}
						
						// Also setup the close/insert callback for swf
						echo('
					
						window[\'easyRotatorInsertPostCode\'] = function(code)
	        			{
							// [For reference] var isMCE = tinyMCE.activeEditor && ! tinyMCE.activeEditor.isHidden(); //(tinyMCEPopup && ( ed = tinyMCEPopup.editor ) && ! ed.isHidden());
							
							// Insert into active editor, attempting to add undo level while we\'re at it
		        			tinyMCE.activeEditor.execCommand("mceInsertContent", false, code);
		        			try {
		        				tinyMCE.activeEditor.undoManager.add();
		        			} catch(e){}
	        				
	        				// Finally, close dialog
	        				jQuery("#easyrotator_manage_dialog").dialog("close");
	        			};
	        			
					};
					
					easyrotator_fs_dialog_launch = function()
					{
						if (window[\'fullscreen\']) // just for safety
						{
							// See if we\'re in Visual/tinyMCE or HTML mode in fullscreen; ref: wp-fullscreen.dev.js.
							var s = fullscreen.settings;
							if ( s.has_tinymce && s.mode == \'tinymce\' )
							{
								easyrotator_tinymce_dialog_launch();
							}
							else
							{
								easyrotator_quicktags_dialog_launch();
							}
						} else alert(\'Error!\');
					};
					
					easyrotator_widgetMode_dialog_launch = function()
					{
						');
    					
    					// Switch output based on whether we have any errors right now...
    					$contentDirStatus = $erwp->createContentDirIfNeeded();
    					if ($contentDirStatus['success'])
    					{
    						// Allow the dialog to be launched
    						echo(
	    					'jQuery(\'#easyrotator_manage_dialog\').dialog({
								title: \'Select a Rotator To Display in Widget\',
								width: 700,
								height: 500 // no zIndex needed
							}).dialog("open");
							');
						}
						else
						{
							// Show the error.
							echo(
							'alert("There\'s currently a problem with your EasyRotator installation!  For details, see the red notice at the top of the current page.\n\nCorrect the error, then reload this page and try again.")'
							);
						}
						
						// Also setup the close/insert callback for swf
						echo('
					
						window[\'easyRotatorInsertPostCode\'] = function(code)
	        			{
							// Call the widget-insert-code callback
	        				try{
	        					window[\'easyrotator_widget_insertCodeCallback\'](code);
	        				}catch(e){}
	        				
	        				// Finally, close dialog
	        				jQuery("#easyrotator_manage_dialog").dialog("close");
	        			};
					};
    				</script>
    				
    				<div id="easyrotator_manage_dialog">
    					<!--<p>Choose a rotator from the list below to embed in your post:</p>-->
    					' . 
    					($inWidgetMode ? 
    						  '<p style="margin-top: 0.45em;">Use the manager below to create or select an existing rotator to display in the widget:</p>'
    						: '<p style="margin-top: 0.45em;">Use the manager below to create or select an existing rotator to embed in your post:</p>'
    					)
    					. '
    					<div id="erWPManagerContent">
				        	<p>
					        	To view this page ensure that Adobe Flash Player version 
								10.0.0 or greater is installed. 
							</p>
							<a href="//www.adobe.com/go/getflashplayer"><img src="//www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" /></a>
							</script> 
				        </div>
    					
    					');
	        			
	        
	        			// ref: tinyMCE.activeEditor.execCommand('mceInsertContent', false|0, '[bla]')
	        			echo('
	        				<script type="text/javascript">
	        				
	        				easyrotator_manage_dialog_runEmbed = function()
	        				{
	        				var swfVersionStr = "10.0.0";
				            var xiSwfUrlStr = "";
            				var flashvars = {
            									connectionName:\'' . str_replace('\'', '\\\'', $apiInfo['name']) . '\', 
            									connectionURL:\'' . str_replace('\'', '\\\'', $apiInfo['url']) . '\', 
            									connectionKey:\'' . str_replace('\'', '\\\'', $apiInfo['key']) . '\'
            									' . ($inWidgetMode ? '      ,inWidgetMode:\'true\'       ' : '') . '
            								};
				            var params = {};
				            params.quality = "high";
				            params.bgcolor = "#ffffff";
				            params.wmode = "transparent";
				            params.allowscriptaccess = "sameDomain";
				            params.allowfullscreen = "true";
				            var attributes = {};
				            attributes.id = "EasyRotatorWizard_wpExplorer";
				            attributes.name = "EasyRotatorWizard_wpExplorer";
				            attributes.align = "middle";
				            swfobject.embedSWF(
				                "' . $this->url . 'img/EasyRotatorWizard_wpExplorerOS.swf?rev=6", "erWPManagerContent",
				                "660", "390", 
				                swfVersionStr, xiSwfUrlStr, 
				                flashvars, params, attributes);
	        				};
	        				easyrotator_manage_dialog_runEmbed();
	        				
	        				</script>
	        				
	        				
	        			');

	        			echo('	</div>
	        	  </div>');
	        	  
	        	  
	    	   	// Add the helpers for preview/edit/manage buttons (used in meta box and widget view)
	        	?>
	        	  
	        	<script type="text/javascript">
	        	(function($){
	        	
	        		// Global EditButton helpers
					easyrotator_edit_button_appAvailable = false;
					easyrotator_edit_button_appAvailable_tested = false; // have we tested yet?
					easyrotator_edit_button_embedCounter = 0;
				
					// Helpers
					easyrotator_previewRotator = function(id, name)
					{
						// Use delay to avoid things being synchronous in FF (Flash/FF bug)
						if ($.browser.mozilla)
						{
							setTimeout(function(id, name){
								window['easyrotator_previewRotator_real'](id, name);
							}, 300, id, name); // 300 seems to be good in testing, but may need to be increased if things misbehave.
						}
						else
						{
							window['easyrotator_previewRotator_real'](id, name);
						}
					}
					easyrotator_previewRotator_real = function(id, name)
					{
						// Remove any existing preview, then create new box
						$('#easyrotator_preview_dialog').remove();
						var b = $('body');
						var w = b.width() - 100,
							h = b.height() - 100;
						$('<div id="easyrotator_preview_dialog"></div>').hide().appendTo('body').dialog({
							title:'EasyRotator Preview: ' + name,
							width:w,
							height:h,
							zIndex:169999 // on top of 		 dialog <strike>no need for zIndex since won't be on top of fs</strike>
						}).dialog('open');
						var box = $('#easyrotator_preview_dialog');
						
						var preload = $('<div>Loading Preview...</div>').css({
							position: 'absolute',
							fontSize: '24px',
							lineHeight: '30px'
						}).appendTo(box);
						preload.css({
							left: ((box.width()-preload.width()) / 2),
							top: ((box.height()-preload.height()) / 2)
						});
						
						var iframeSrc = '<?php echo($engineURL); ?>?action=renderFrame&path=' + encodeURIComponent(id);
						var iframe = $('<iframe style="visibility:hidden;" src="' + iframeSrc + '" width="' + (box.width()-4) + '" height="' + (box.height()-4) + '" frameborder="0" scrolling="no"></iframe>').appendTo(box);
						iframe.load(function()
						{
							var b = $(this).contents().find('body');
							var w = iframe.width();
							var h = iframe.height();
							var rotator = b.children('div') //('.dwuserEasyRotator');
							rotator.css({
								position: 'absolute',
								left: ((w-rotator.width())/2),
								top: ((h-rotator.height())/2)
							});
							preload.hide();
							iframe.css({visibility:'visible'});
						});
					};
					
					// Edit Button Helpers
					easyrotator_edit_button_notifyAppAvailable = function(available)
					{
						easyrotator_edit_button_appAvailable = available;
						if (available)
						{
							// Enable meta box buttons
							var tds = $('#easyrotator_edit_shortcut_box td.er_edit_btn_cell').each(function()
							{
								var $this = $(this);
								$this.removeClass('er_nopadcell');
								$this.find('div.er_edit_btn_canvasWrap').removeClass('er_collapsed').addClass('er_expanded');
							});
							
							// Enable widget box buttons
							$('.easyrotator_widget_form_wrap span.er_edit_btn_canvasWrap, .easyrotator_widget_form_wrap div.er_edit_btn_canvasWrap').removeClass('er_collapsed').addClass('er_expanded');
						}
					};
					easyrotator_embedEditBtn = function(id, editPath)
					{
						//easyrotator_edit_button_appAvailable_tested
						var swfVersionStr = "10.0.0";
				        var xiSwfUrlStr = "";
            			var flashvars = {
            								connectionName: '<?php echo(str_replace('\'', '\\\'', $apiInfo['name'])); ?>', 
            								connectionURL: '<?php echo(str_replace('\'', '\\\'', $apiInfo['url'])); ?>', 
            								connectionKey: '<?php echo(str_replace('\'', '\\\'', $apiInfo['key'])); ?>',
            								<?php if ($inWidgetMode) { ?>
            									testAppAvailable: (easyrotator_edit_button_appAvailable ? 'false' : 'true'), //testAppAvailable: 'true', // avoid issues with no testing due to hidden Flash.  TODO
            								<?php } else { ?>
            									testAppAvailable: (easyrotator_edit_button_appAvailable_tested ? 'false' : 'true'),
            								<?php } ?>
            								editPath: editPath
            							};
            			easyrotator_edit_button_appAvailable_tested = true; // make sure flag is updated
				        var params = {};
				        params.quality = "high";
				        params.bgcolor = "#ffffff";
				        params.wmode = "transparent";
				        params.allowscriptaccess = "sameDomain";
				        params.allowfullscreen = "true";
				        var attributes = {};
				        attributes.id = id;
				        attributes.name = id;
				        swfobject.embedSWF(
			                "<?php echo($this->url); ?>img/EasyRotatorWizard_wpEditButton.swf?rev=2", id, 
			                "100%", "100%", 
			                swfVersionStr, xiSwfUrlStr, 
			                flashvars, params, attributes);
			                
					};
					easyrotator_edit_button_notifyClick = function()
					{
						// Show an overlay notification, so the user knows what's happening.
						var notification = $('<div style="position:absolute;display:none;zIndex:1000;font-size:30px; line-height: 40px; font-weight:bold; padding: 20px; background: #FFC; text-align: center;">Launching Editor... <br />Please Wait...</div>').appendTo('body');
						var b = $('body');
						notification.css({
							left: ((b.width()-notification.width())/2),
							top: ((b.height()-notification.height())/2)
						}).show();
						setTimeout(function(){
							notification.fadeOut(2000, function()
							{
								notification.remove();
							});
						}, 800);
							
					};
					
					// Manage button helpers
					easyrotator_manageRotator = function(id, widgetMode)
					{
						// Set the global flag, launch manager
						window['easyrotator_communicationDialog_manage_edit_path'] = id;
						if (widgetMode)
						{
							// Launch widget mode
							easyrotator_widgetMode_dialog_launch();
						}
						else if (window['edCanvas'] && !(window['tinyMCE'] && tinyMCE.activeEditor))
						{
							// Only HTML view enabled.
							easyrotator_quicktags_dialog_launch();
						}
						else if (!(window['edCanvas']) && window['tinyMCE'] && tinyMCE.activeEditor)
						{
							// Only tinyMCE view enabled
							easyrotator_tinymce_dialog_launch();
						}
						else
						{
							// Both apparently enabled; try to determine which is active
							if ($('#edButtonHTML').hasClass('active') || $('#wp-content-wrap').hasClass('html-active'))
								easyrotator_quicktags_dialog_launch();
							else if ($('#edButtonPreview').hasClass('active') || $('#wp-content-wrap').hasClass('tmce-active'))
								easyrotator_tinymce_dialog_launch();
							else
								alert('Unable to detect current mode; editor couldn\'t be launched directly.  Use the EasyRotator button on the editor above to launch the manager.');
						}
						
						// If this is IE, SWF won't be recreated automatically.  Therefore, we need to tell the SWF to perform the startup action that checks manage_edit_path as set above.
						if ($.browser.msie)
						{
							try {
								var worked = false;
								$('#easyrotator_manage_dialog object, #easyrotator_manage_dialog embed').each(function() {
									if (this['easyrotator_communicationDialog_notifyRefresh'])
									{
										this['easyrotator_communicationDialog_notifyRefresh']();
										worked = true;
									}
								});
								if (!worked)
								{
									// Clean way failed (e.g. in WP < 3).  Do it the ugly way.
									$('#easyrotator_manage_dialog object, #easyrotator_manage_dialog embed').replaceWith('<div id="erWPManagerContent"></div>');
									easyrotator_manage_dialog_runEmbed();
								}
							} catch(e){}
						}
					};
					
					<?php
					// --------------------------------
					// Widget helpers!
					// --------------------------------
					if ($inWidgetMode) {
					?>
					
					// --- Widget helpers ---
					// ref: magic method: easyrotator_widget_insertCodeCallback (after easyrotator_widgetMode_dialog_launch)
					
					// The method called after each of the widget boxes enters this world
					easyrotator_widget_processWidgets = function()
					{
						setTimeout(easyrotator_widget_processWidgets_continue, 50); // wait for any goodies to take place
					};
					
					easyrotator_widget_processWidgets_continue = function()
					{
						// Helper: for setting up buttons
						var setupEditButton = function(path, btns)
						{
							// Get the new button id
							var editBtnID = 'er_edit_btn_flash' + (easyrotator_edit_button_embedCounter++);
							
							// Create the markup for the button
							btns.find('.er_edit_btn_flashOverlay_wrap').html('<div id="' + editBtnID + '"></div>');
							easyrotator_embedEditBtn(editBtnID, path);
							
							// If we already have an answer about the edit button being available, show it now
							if (easyrotator_edit_button_appAvailable)
								easyrotator_edit_button_notifyAppAvailable(true);
						};
					
						// Look for un-initialized boxes
						$('div.easyrotator_widget_form_wrap').each(function()
						{
							var wrap = $(this);
							if (!wrap.data('easyrotatorWidgetBoxInitialized'))
							{
								wrap.data('easyrotatorWidgetBoxInitialized', true); // set initialized flag
								
								// Wire-up the widget form; wrap for persistent scope
								(function(wrap){
									
									// Title box toggle
									var check = wrap.find('input.easyrotator_widgetFormField_showTitle');
									var titleBox = wrap.find('div.easyrotator_widgetForm_titleBox, p.easyrotator_widgetForm_titleBox'); //input.easyrotator_widgetFormField_title');
									var firstCall = true;
									var updateTitleBox = function()
									{
										if (!check.is(':checked'))
											titleBox[firstCall ? 'hide' : 'slideUp']();
										else
											titleBox.slideDown();
										firstCall = false;
									};
									var updateTitleBoxDelayed = function()
									{
										setTimeout(updateTitleBox, 1);
									};
									updateTitleBox();
									check.change(updateTitleBoxDelayed).bind('click', updateTitleBoxDelayed);
									
									
									// Rotator path helpers
									var pathField = wrap.find('input.easyrotator_widgetFormField_path');
									var btns = wrap.find('div.easyrotator_widgetForm_curPopup'); //wrap.find('p.easyrotator_widgetForm_controlBtns');
									var nameField = wrap.find('p.easyrotator_widgetForm_rotatorName');
									var lastSavedPath = pathField.val();
									var showSaveNotification = function(show)
									{
										if (show)
										{
											wrap.find('p.er_save_notification').slideDown();
											wrap.find('hr.er_bottom_hr').hide();
										}
										else
										{
											wrap.find('p.er_save_notification').slideUp(function(){
												wrap.find('hr.er_bottom_hr').show();
											});
										}
									};
									var setRotatorPath = function(path)
									{
										// Update the hidden field; load the name
										pathField.val(path);
										
										// Show save notification if changed
										if (path != lastSavedPath)
											showSaveNotification(true);
											
										// Recreate the edit button
										setupEditButton(path, btns);
										
										// Update the name field, loading if a real path has been specified
										if (path == 'No_Rotator_Path_Specified' || path == '')
										{
											// Nuke the buttons, notify that nothing is specified
											nameField.html('<span class="er_loading_name">(No rotator specified)<span>').attr('title', '');
											btns.hide();
										}
										else
										{
											// Load name via ajax
											nameField.html('<span class="er_loading_name">Loading rotator name...</span>').attr('title', 'Path: ' + path);
											var data = {
												action: 'easyrotator_get_rotator_names',
												security: '<?php echo(wp_create_nonce('nonce_easyrotator_get_rotator_names')); ?>',
												paths: path
											};
											$.post(ajaxurl, data, function(result){
												try{
													var obj = eval('(' + result + ')');
													if (obj.success)
													{
														var name = obj.names[path];
														if (!name || name == '~~ERROR~~')
														{
															nameField.html('<span class="er_loading_name">Error - Select rotator again.</span>');
														}
														else
														{
															nameField.html(name);

															// Show, setup btns
															btns.show();
														}
													}
													else throw 'bla';
												}catch(e){
													nameField.html('<span class="er_loading_name">Error loading name; reload page.</span>');
												}
											});
										}
					
									};
									setRotatorPath(pathField.val()); // update initially, loading name if needed
									wrap.closest('form').find('input[type=submit]').click(function(){
										lastSavedPath = pathField.val();
										showSaveNotification(false);
									});
									
									
									// Setup buttons
									wrap.find('a.er_preview_btn').click(function(e)
									{
										e.preventDefault();
										easyrotator_previewRotator(pathField.val(), nameField.text());
									});
									wrap.find('a.er_manage_btn').click(function(e)
									{
										e.preventDefault();
										easyrotator_widget_insertCodeCallback = function(code)
										{
											// Strip out the shortcode, so we only have the path
											var path = code.replace(/\[[^\]]+\]/g, '');
											setRotatorPath(path);
										};
										easyrotator_manageRotator(pathField.val(), true);
									});
									btns.hide(); // hide initially, until we know that something is available.  Edit btn will be set up later.
									
									// Setup manage btn
									wrap.find('a.er_manager_btn').click(function(e)
									{
										e.preventDefault();
										easyrotator_widget_insertCodeCallback = function(code)
										{
											// Strip out the shortcode, so we only have the path
											var path = code.replace(/\[[^\]]+\]/g, '');
											setRotatorPath(path);
										};
										easyrotator_widgetMode_dialog_launch();
									});
									
									// Setup the button hover action
									wrap.find('div.easyrotator_widgetForm_curPopup').hover(function(){
										$(this).addClass('er_expanded');
									}, function() {
										$(this).removeClass('er_expanded');
									}).click(function(){
										$(this).toggleClass('er_expanded'); //touchscreen tap
									});
									
									
								})(wrap);
								
							}
						});
					};
					
					
					<?php
					} // end widget helpers
					?>
					
					
				})(jQuery);
				</script>
	        	  
	        	  <?php
	    }	
	}
	
	
	function editor_quicktags()
	{
		global $erwp;
	
		// Append the easyrotator button to the Quicktags section, making it look like the other buttons.
		?>
		<script type="text/javascript">
        jQuery(function($)
        {
            var addButtonIfNeeded, tryInterval = 10;
            addButtonIfNeeded = function()
            {
                tryInterval *= 2;
                if ($('#ed_toolbar').children().length == 0)
                    setTimeout(addButtonIfNeeded, tryInterval); // toolbar not ready; wait a tad
                else
                    $('#ed_toolbar').append('<input type="button" id="ed_easyrotator" class="ed_button" onclick="easyrotator_quicktags_dialog_launch();" title="Insert rotator into post/page..." value="Insert EasyRotator" style="wascolor:#09F;" />');
            }
            addButtonIfNeeded();
        });
		
    	easyrotator_quicktags_dialog_launch = function()
    	{
    		
    		<?php
    		// Switch output based on whether we have any errors right now...
    		$contentDirStatus = $erwp->createContentDirIfNeeded();
    		if ($contentDirStatus['success'])
    		{
    			// Allow the dialog to be launched
    			echo(
	    		'jQuery(\'#easyrotator_manage_dialog\').dialog({
					title: \'Select a Rotator To Insert\',
					width: 700,
					height: 500,
					zIndex: 159999 // need to go over fs overlay if applicable (which is 14999)
				}).dialog(\'open\');');
			}
			else
			{
				// Show the error.
				echo(
				'alert("There\'s currently a problem with your EasyRotator installation!  For details, see the red notice at the top of the current page.\n\nCorrect the error, then reload this page and try again.")'
				);
			}
			?>
			
			// Setup the close/insert callback for swf
			window['easyRotatorInsertPostCode'] = function(code)
	     	{
	    		try {
	     			edInsertContent(edCanvas, code);
	     		} catch (e) {
	     			alert('Unable to insert; an error occurred and the text wasn\'t able to be inserted because the editor was inaccessible.');
	     		}
	     		jQuery("#easyrotator_manage_dialog").dialog("close");
	     	};
	     			
		}

		</script>
		<?php
	}	
	
	/**
	 * Track changes to the editor as they happen, to provide contextualized editing info
	 **/
	function editor_edit_monitor()
	{
		?>
		<script type="text/javascript">
		jQuery(function($) {
			
			// note helpers above, along with the manager launch code
			
			var firstCall = true;
			var displayRotatorEditBoxes = function(rotators)
			{
				var isFirst = firstCall;
				firstCall = false;
				
				// we have an array of rotator codes
				var box = $('#easyrotator_edit_shortcut_box');
				var len = rotators.length;
				if (len > 0)
				{
				
					// Update the box title
					var titleNumbers = ['One','Two','Three','Four','Five','Six','Seven','Eight','Nine','Ten'];
					var titleText = titleNumbers[len-1] || String(len);
					titleText += ' Rotator' + (len==1 ? '' : 's');
					$('#easyrotator_edit_shortcut_box span.easyrotator_meta_title_count').text(titleText);
				
					// Create the rows
					var table = box.find('table.easyrotator_rotator_list');
					var tbody = table.children('tbody').empty();
					for (var i=0; i<len; i++)
					{
						var code = rotators[i];
						
						var editBtnID = 'er_edit_btn_flash' + (easyrotator_edit_button_embedCounter++);
						var tr = $(
							'<tr data-code="' + code + '">' + 
								'<td class="col1 name_col" title="Code: ' + code + '"><span class="er_loading_name">Loading Name...</span></td>' +
								'<td><a href="#" class="button-secondary er_preview_btn" title="Preview this rotator...">Preview</a></td>' +
								'<td title="Edit this rotator with the EasyRotator program..." class="er_edit_btn_cell er_nopadcell">' +
									'<div class="er_edit_btn_canvasWrap er_collapsed">' +
										'<a href="#" class="button-secondary er_edit_btn">Edit</a>' + 
										'<div class="er_edit_btn_flashOverlay_wrap">' + // where the 
											'<div id="' + editBtnID + '"></div>' + // where the flash goes
										'</div>' +
									'</div>' +
								'</td>' +
								'<td><a href="#" class="button-secondary er_manage_btn" title="Open rotator manager...">Open Manager</a></td>' +
							'</tr>');
						if (i == 0)
							tr.addClass('alpha');
						if (i == len-1)
							tr.addClass('omega');
						
						(function(code, tr){ // for persistent scope for code, tr
						
							// Setup listeners
							tr.find('a.er_preview_btn').click(function(e)
							{
								e.preventDefault();
								easyrotator_previewRotator(code, tr.find('td.name_col').text());
							});
							tr.find('a.er_manage_btn').click(function(e)
							{
								e.preventDefault();
								easyrotator_manageRotator(code);
							});
						
						})(code, tr);
					
						tbody.append(tr);
						
						// Setup the edit btn
						easyrotator_embedEditBtn(editBtnID, code, tr);
					}
					
					// If we already have an answer about the edit button being available, show them now
					if (easyrotator_edit_button_appAvailable)
						easyrotator_edit_button_notifyAppAvailable(true);
					
					// Run ajax
					var data = {
						action: 'easyrotator_get_rotator_names',
						security: '<?php echo(wp_create_nonce('nonce_easyrotator_get_rotator_names')); ?>',
						paths: rotators.join(',')
					};
						
					$.post(ajaxurl, data, function(result){  
						try {
							var obj = eval('(' + result + ')');
							if (obj.success)
							{
								var names = obj.names;
								tbody.find('td.name_col').each(function()
								{
									var td = $(this);
									var path = td.parent().attr('data-code');
									var name = names[path];
									if (name == '~~ERROR~~')
									{
										td.find('span.er_loading_name').text('Error - Invalid rotator path.');
										td.parent().find('a').hide();
									}
									else
										td.html(name);
								});
							}
							else throw 'bla';
						}
						catch (e) {
							table.find('span.er_loading_name').text('Error loading name; reload page.');
						}
					});
				
					// Show box
					box.fadeIn('fast');
					
				}
				else // if len==0
				{
					if (firstCall)
						box.hide();
					else
						box.fadeOut('fast');
				}
				//console.log('TOEDIT:', rotators); // ref: hook_admin_init_add_meta_boxes
			};
			
			var processContent_code = '';
			var processContent_timeout;
			var processContent_first = true;
			var processContent = function(code)
			{
				processContent_code = code;
				// After the first call, only process this every second, so we don't have rapid-fire repeat calls
				if (processContent_first)
				{
					processContent_first = false;
					processContent_real();
				}
				else
				{
					clearTimeout(processContent_timeout);
					processContent_timeout = setTimeout(processContent_real, 700);
				}
			};
			
			var rotatorsInEditor = '';
			var rotatorsInEditor_first = true;
			var processContent_real = function()
			{
				var code = processContent_code;
			
				var i, rotators = [];
				var code = code || ''; // double-check!
				var matches = code.match(/\[easyrotator[^\]]*\].+?\[\/easyrotator\]/gm);
				for (i=0; matches && i<matches.length; i++)
				{
					var shortcode = matches[i];
					var rotatorIDMatches = shortcode.match(/\[easyrotator[^\]]*\](.+?)\[\/easyrotator\]/m);
					if (rotatorIDMatches)
					{
						var rotatorID = rotatorIDMatches[1].replace(/[^A-Z0-9_\/]/i, '');
						if (rotatorID != '')
							rotators.push(rotatorID);
					}
				}
				
				// Remove duplicates
				var separator = '~~';
				var rotatorsInEditor_new = '';
				for (i=0; i<rotators.length; i++)
				{
					var tempToAdd = rotators[i] + separator;
					if (rotatorsInEditor_new.indexOf(tempToAdd) == -1)
						rotatorsInEditor_new += tempToAdd;
				}
				// Trim off the ending ~~ if needed
				if (rotatorsInEditor_new.length > separator.length)
					rotatorsInEditor_new = rotatorsInEditor_new.substring(0, rotatorsInEditor_new.length - separator.length);
				
				// See if there's anything new, or this is first time
				if (rotatorsInEditor_new != rotatorsInEditor || rotatorsInEditor_first)
				{
					rotatorsInEditor_first = false; // reset flag
					rotatorsInEditor = rotatorsInEditor_new;
					displayRotatorEditBoxes( rotatorsInEditor.length == 0 ? [] : rotatorsInEditor.split(separator) );
				}
			};


			// First, listeners for standard HTML.  Keyup to handle typing/pasting, focus to handle modifications by edInsertContent or edInsertTag.
			var rawCanvas = $(edCanvas);
			rawCanvas.bind('keyup focus', function()
			{
				processContent(rawCanvas.val());
			});
			
			// Process the initial value...
			processContent(rawCanvas.val());
			
			// Next, listeners for tinymce.  onKeyUp to handle typing/pasting, onChange to handle other manipulations.
			var tinyMCE_listener = function(ed)
			{
				processContent(ed.getContent());
			};
			var tinyMCE_setupListeners = function(method)
			{
				if (window['tinyMCE'])
				{
					if (tinyMCE.activeEditor)
					{
						tinyMCE.activeEditor.onChange.add(tinyMCE_listener);
						tinyMCE.activeEditor.onKeyUp.add(tinyMCE_listener);
					}
					else if (method)
					{
						setTimeout(method, 1000, method); // try again momentarily
					}
				} // else no tinymce visual editor
			};
			tinyMCE_setupListeners(tinyMCE_setupListeners);
			
			// Finally, listen for exiting fullscreen view if applicable.  Update only when hiding that, since we won't show the edit panel in fs.  Ref: wp-fullscreen.dev.js
			if (window['fullscreen'] && fullscreen.pubsub && fullscreen.pubsub.subscribe)
				fullscreen.pubsub.subscribe('hide', function(){  
					// Wait a second, for the other updates to occur
					setTimeout(function(){
						var s = fullscreen.settings;
						if (s.has_tinymce && s.mode === 'tinymce')
							processContent( tinyMCE.activeEditor.getContent() );
						else
							processContent( rawCanvas.val() );
					}, 100);
				});
				
		});
		</script>
		<?php
	}
		
		// Helper ajax method to get rotator names
		function ajax_easyrotator_get_rotator_names()
		{
			check_ajax_referer('nonce_easyrotator_get_rotator_names', 'security');

			global $erwp;
	        
	        $paths = explode(',', @$_POST['paths']);
	        $names = $erwp->getRotatorNames( $paths );
	        
	        echo('{success:true, names:{');
	        $output = '';
	        foreach ($names as $path=>$name)
	        {
	        	if ($name === false)
	        		$name = '~~ERROR~~';
	        	$output .= '"' . str_replace('"', '\\"', $path) . '":"' . esc_attr($name) . '",'; // since we'll be displaying in HTML, use esc_attr
	        }
	        echo( substr( $output, 0, -1 ) ); // trim the end , off
	        echo('}}');

			die();
		}
	
	
	/**
	 * Add pseudo-meta box to post & page editor pages
	 **/
	function hook_admin_init_add_meta_boxes()
	{
		$box_css_id = 'easyrotator_edit_shortcut_box';
		$title = '<span class="easyrotator_meta_title_icon"></span>EasyRotator: <span class="easyrotator_meta_title_count">One Rotator</span> in';
		add_meta_box(
			$box_css_id,
			$title . ' Post',
			array( $this, 'meta_box_shortcutbox' ),
			'post',
			'normal',
			'high'
		);
		add_meta_box(
			$box_css_id,
			$title . ' Page',
			array( $this, 'meta_box_shortcutbox' ),
			'page',
			'normal',
			'high'
		);
	}

	function meta_box_shortcutbox($post)
	{
		// TODO!
		?>
		<table class="form-table easyrotator_rotator_list">
			<tbody>
				<?php /* example:
				<tr class="alpha">
					<td style="width:100%;" title="Code: erf_123456/erc_123456"><span class="er_loading_name">Loading Name...</span></td>
					<td><a href="#" class="button-secondary" title="Preview this rotator...">Preview</a></td>
					<td><a href="#" class="button-secondary" title="Edit this rotator with the EasyRotator program...">Edit</a></td>
					<td><a href="#" class="button-secondary" title="Open rotator manager...">Open Manager</a></td>
				</tr>
				*/
				?>
			</tbody>
		</table>
		<?php
	}
	
	
	
	function hook_shortcode($atts, $content='NoneSpecified', $code="" ) {
	   // $atts    ::= array of attributes
	   // $content ::= text within enclosing form of shortcode element
	   // $code    ::= the shortcode found, when == callback name
	   // examples: [my-shortcode]
	   //           [my-shortcode/]
	   //           [my-shortcode foo='bar']
	   //           [my-shortcode foo='bar'/]
	   //           [my-shortcode]content[/my-shortcode]
	   //           [my-shortcode foo='bar']content[/my-shortcode]
	   
		global $erwp;
		
		$atts = /*extract*/( shortcode_atts( array(
			'align' => 'center',
			//'id' => 'NoneSpecified',
		), $atts ) );
		$requestedID = $content;
		
		$content = '<div class="easyRotatorWrapper" align="' . $atts['align'] . '">';
		$content .= $erwp->renderRotator($requestedID);
		$content .= '</div>';
		
		if (is_search())
		{
			$index = $erwp->strpos_preg($content, '|<div[^>]+?class="erabout["\s][^>]*>|i');
			$index2 = strpos($content, '>', $index) + 1;
			$closePos = $erwp->findCorrespondingEndingTag($content, $index, 'div');
		
			$before = $erwp->substring($content, 0, $index2);
			//$contentDiv = $erwp->substring($content, $index2, $closePos['closeStart']);
			$after = substr($content, $closePos['closeStart']);
			
			$content = $before . $after;
			
			// Strip out <noscript> too
			$index = $erwp->strpos_preg($content, '|<noscript>|i');
			$index2 = strpos($content, '>', $index) + 1;
			$closePos = $erwp->findCorrespondingEndingTag($content, $index, 'noscript');
		
			$before = $erwp->substring($content, 0, $index2);
			//$contentDiv = $erwp->substring($content, $index2, $closePos['closeStart']);
			$after = substr($content, $closePos['closeStart']);
			
			$content = $before . $after;
		}

        if (is_feed())
        {
            $replacementText = '<div class="easyRotatorWrapper easyRotatorWrapperRSS" align="' . $atts['align'] . '">';
            $firstImage = stripos($content, '<img');
            if ($firstImage !== false)
            {
                preg_match('|<img.*?\ssrc="([^"]+)"|i', $content, $matches);
                if (is_array($matches) && count($matches) == 2)
                {
                    $replacementText .= '<img src="' . $matches[1] . '" class="easyRotatorRSSPreviewImg" />';
                }
            }
            $replacementText .= '<!--easyRotatorRSSPreviewText--></div>';
            $content = $replacementText;
        }
		
		return $content; //return $matches[0];
		
	}
	
	
	function api_getInfo()
	{
		global $erwp;
	
		// Make up the name, get the saved key from the db.
		$name = get_bloginfo('name') . ' - Default Connection';
		$key = $erwp->getAPIKey();
		
		// Get the dynamic real path to the engine, e.g. $this->url . 'engine/main.php';  Don't worry about SSL unless it's been enabled on wp.
		$url = $this->url . 'engine/main.php';
		
		return array('name'=>$name, 'url'=>$url, 'key'=>$key);		
	}
	
	function api_reKey()
	{
		global $erwp;
		
		// Re-key the api key
		$erwp->resetAPIKey();
	}
	
	
}
	
$easyrotator = new EasyRotator();

// Incorporate compatibility for issues other plugins introduce.
require_once(dirname(__FILE__) . '/compatibility.php');


// -----------------------
// ------- Widget --------
// -----------------------

// Define widget class
class EasyRotatorWidget extends WP_Widget
{
	function EasyRotatorWidget() 
	{
		$desc = 'A custom rotator created with the EasyRotator program';
		$widget_options = array( 'classname' => 'EasyRotatorWidget', 'description' => $desc );
		$control_options = array(); // width, height, id_base
		parent::WP_Widget('easyrotator_widget', 'EasyRotator Rotator', $widget_options, $control_options);
	}
	
	function form( $instance )
	{global $easyrotator;
		$defaults = array( 'title'=>'', 'showTitle'=>'false', 'path'=>'No_Rotator_Path_Specified' );
		$instance = wp_parse_args( (array) $instance, $defaults );
		
		$showTitle = ($instance['showTitle'] == 'true');
		$title = strip_tags($instance['title']);
		$path = strip_tags($instance['path']);
		
		?>
		
		<div class="easyrotator_widget_form_wrap">
		
			<!-- <p><input id="<?php echo( $this->get_field_id('showTitle') ); ?>" name="<?php echo( $this->get_field_name('showTitle') ); ?>" type="checkbox" value="true" <?php echo( $showTitle ? 'checked="checked"' : ''); ?> /><label for="<?php echo( $this->get_field_id('showTitle') ); ?>"> Show Title</label></p> -->
			<!-- <p id="<?php echo( $this->get_field_id('title') ); ?>_box"><input id="<?php echo( $this->get_field_id('showTitle') ); ?>" name="<?php echo( $this->get_field_name('showTitle') ); ?>" type="checkbox" value="true" <?php echo( $showTitle ? 'checked="checked"' : ''); ?> /> <label for="<?php echo( $this->get_field_id('title') ); ?>">Show This Widget Title: </label><input class="widefat" id="<?php echo( $this->get_field_id('title') ); ?>" name="<?php echo( $this->get_field_name('title') ); ?>" type="text" value="<?php echo( $title ); ?>" /></p> -->
			<p style="margin-bottom:0.3em; padding-bottom:0;">
				<input class="easyrotator_widgetFormField_showTitle" id="<?php echo( $this->get_field_id('showTitle') ); ?>" name="<?php echo( $this->get_field_name('showTitle') ); ?>" type="checkbox" value="true" <?php echo( $showTitle ? 'checked="checked"' : ''); ?> /><label for="<?php echo( $this->get_field_id('showTitle') ); ?>"> <!--Display Title at Top of Widget-->Display Widget Title</label>
			</p>
			<p class="easyrotator_widgetForm_titleBox"<?php echo( $showTitle ? '' : ' style="display:none;"' ); ?>><input class="widefat easyrotator_widgetFormField_title" id="<?php echo( $this->get_field_id('title') ); ?>" name="<?php echo( $this->get_field_name('title') ); ?>" type="text" value="<?php echo( $title ); ?>" /></p>
			
			
			<p><input class="easyrotator_widgetFormField_path" id="<?php echo( $this->get_field_id('path') ); ?>" name="<?php echo( $this->get_field_name('path') ); ?>" type="hidden" value="<?php echo( $path ); ?>" /></p>
			
			<hr />
			<p style="font-weight: bold;">Rotator Displayed in Widget:</p>

			<div class="easyrotator_widgetForm_nameWrap">
				<p class="easyrotator_widgetForm_rotatorName"><span class="er_loading_name">Loading Name...</span></p>
				<div class="easyrotator_widgetForm_curPopup">
					<div class="er_btn">
					  &nbsp;
					</div>
					<div class="er_floater">
						<!-- btns -->
						<p style="width: 100%; color: #666;">Rotator management tools:</p>
						<p style="text-align: center;">
							<a href="#" class="button-secondary er_preview_btn" title="Preview this rotator...">Preview</a>
							<span class="er_edit_btn_canvasWrap er_collapsed">
								<a href="#" class="button-secondary er_edit_btn" title="Edit this rotator...">Edit</a>
								<span class="er_edit_btn_flashOverlay_wrap">
									<!-- div to be replaced will be in here -->
								</span>
							</span>
						</p>
						<p style="text-align: center; margin-bottom: 4px; padding-bottom: 0;">
							<a href="#" class="button-secondary er_manage_btn" title="Open rotator manager...">Open Manager</a>
						</p>
					</div>
				</div>
			</div>
				
			<!-- class="easyrotator_widgetForm_controlBtns" -->
			<hr />
			<p><a href="#" class="button-secondary er_manager_btn" title="Create / select rotator that's displayed in this widget...">Create / Select Rotator for Widget...</a></p>
			<hr class="er_bottom_hr" />
			<p class="er_save_notification" style="display: none;">Don't forget to click Save!</p>
			
		</div>
		<script type="text/javascript">
		// do the initialization momentarily
		jQuery(function($){
			easyrotator_widget_processWidgets();
			$('body').one('mouseup', function()
			{
				// detect end of drag to perform processing on new widget // TOASK: better way?
				setTimeout(function()
				{
					easyrotator_widget_processWidgets();
				}, 10);
				setTimeout(function()
				{
					easyrotator_widget_processWidgets();
				}, 500); // just-in-case for ie especially
				setTimeout(function()
				{
					easyrotator_widget_processWidgets();
				}, 1000); // just-in-case for ie especially
				setTimeout(function()
				{
					easyrotator_widget_processWidgets();
				}, 1500); // just-in-case for ie especially
			});
		});
		</script>
		<?php
	}
	
	function update( $new_instance, $old_instance )
	{
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['showTitle'] = $new_instance['showTitle'] == 'true' ? 'true' : 'false';
		$instance['path'] = strip_tags($new_instance['path']);
		
		if (false) // debug
		{
			echo('INSTANCE:');
			var_dump($new_instance);
			die('');
		}
		
		return $instance;
	}
	
	function widget( $args, $instance )
	{
		// Get info
		extract($args, EXTR_SKIP);
		
		$title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
		$showTitle = $instance['showTitle'] == 'true' ? true : false;
		$id = empty($instance['path']) ? 'Invalid_Rotator_Path_Specified' : apply_filters('easyrotator_widget_display_path', $instance['path']);
		
		// Render content
		
		echo( $before_widget );
		if ($showTitle) //(!empty($title))
			echo( $before_title . $title . $after_title );
		
		$rotatorCode = $this->getRotatorContent( $id );
		echo( $rotatorCode );
		
		echo( $after_widget );
	}
	
	// ------ HELPERS ------
	
	function getRotatorContent( $path )
	{
		global $erwp;
	
		$content = $erwp->renderRotator( $path );
		return $content;
	}
}

// Link it up
add_action('widgets_init', create_function('', 'return register_widget("EasyRotatorWidget");'));


// -----------------------
// Deactivate callback
// -----------------------

if ( !class_exists('EasyRotatorLifecycleCallbacks') ):
class EasyRotatorLifecycleCallbacks
{
	function handle_activate()
	{
		// Nuke the getting started preference.  Don't use delete_user_option since only since 3.0.
		global $current_user;
		if (is_object($current_user) && function_exists( 'update_user_option' ))
		{
	    	$user_id = $current_user->ID;
			@update_user_option($user_id, 'easyrotator_activatetime', time());
			@update_user_option($user_id, 'easyrotator_lovebar_showtime', '-3');
			@update_user_option($user_id, 'easyrotator_lovebar_showurl', 'no');
		}
	}
	function handle_deactivate()
	{
		// Nuke the getting started preference.  Don't use delete_user_option since only since 3.0.
		global $current_user;
		if (is_object($current_user) && function_exists( 'update_user_option' ))
		{
	    	$user_id = $current_user->ID;
			@update_user_option($user_id, 'easyrotator_welcome_notice_hide', 'no');
			@update_user_option($user_id, 'easyrotator_help_tooltips_postpagebtn_hide', 'no');
		}
	}
}
endif; // end class_exists test

// Link it up
register_deactivation_hook( __FILE__, array( 'EasyRotatorLifecycleCallbacks', 'handle_deactivate' ) );
register_activation_hook( __FILE__, array( 'EasyRotatorLifecycleCallbacks', 'handle_activate' ) );

// -----------------------
// Template function
// -----------------------

function easyrotator_display_rotator( $rotatorID, $echo=true )
{
	global $erwp;
	$content = $erwp->renderRotator($rotatorID);
	if ($echo)
		echo($content);
	return $content;
}


// --------------------------------------------------
} // end version check else.

?>