<?php

/*
Copyright 2011 DWUser.com.
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





	// Add the ajax hide method
	$ajax_nonce = wp_create_nonce('nonce_easyrotator_welcome_notice_hide');	
	
	
	// TWO POSSIBLE INSTALL/SETUP METHODS: 
	// 		1. Wait for explorer install on first launch
	// 		2. Guided install immediately after plugin activation (new default)
	
	$useAfterActivationSetup = true;
	
	
	if ($useAfterActivationSetup) {
?>

<div class="updated easyrotator_welcome_notice">
	
	<div class="part1"> <!-- while detecting the needed action step -->
      <p class="er_titleLine" style="font-weight: bold;">Welcome to EasyRotator!</p>
      <p><img src="<?php echo( $this->url ); ?>img/loader16_blue.gif" style="vertical-align: -3px;"/> &nbsp;Checking setup configuration... Please wait...</p>
    </div>
    
    <div class="part2 er_hidden">
      <p class="er_titleLine"><span style="font-weight: bold;">Welcome to EasyRotator! You're almost ready to start creating rotators!</span></p>
      <div style="display: none;" class="er_chromeAIRBugNote"><p>It appears that you're using Google Chrome.&nbsp; There's a new issue that may break auto-installation in Chrome.&nbsp; Please <a href="http://www.dwuser.com/support/easyrotator/kb/chrome-air-problem/" target="_blank">click here</a> to learn more about how you can avoid this problem.</p></div>
      <div class="state1">
        <p>To use the EasyRotator admin, you need to install the latest version of the <a href="http://get.adobe.com/flashplayer/" target="_blank">Adobe Flash Player</a>.&nbsp; Once you've finished updating, reload this page to continue.</p>
      </div>
      <div class="state2 er_hidden">
        <p>You need to install the EasyRotator editor application.&nbsp; Simply download and run the installer:</p>
        
          <!-- initially -->
          <p style="padding-top: 0.5em; padding-bottom: 1.1em;">
          	<a href="#" class="button-primary er_installer_link">Download EasyRotator Application Installer</a> <!-- after ten, unbolded and text becomes "Re-Download Installer" -->
            <a href="#" class="button-primary er_continue_link er_hidden er_afterTen">Click Here Once Application Has Successfully Been Installed...</a>
          </p>
        
      </div>
      <div class="state3 er_hidden">
        
        <p>You need to install the EasyRotator editor application.&nbsp; Click the Install button below, then choose <span style="font-style: italic;">Open</span> if prompted:</p>
        
        <div class="er_install_btn_wrap">
          
          <p>
            <span class="WASer_install_btn_wrap" style="position: relative; display: inline-block; min-width: 214px; padding: 70px 0 80px 0;">
            	<!-- the btns -->
            	<a href="#" class="button-primary er_install_btn_link er_tillTen">Install EasyRotator Application</a> <!-- label will change on click -->
                    	<!-- will be updated like this: <a href="#" class="button-secondary er_hidden er_afterTen">Re-Launch Auto-Installer</a> -->
	            
	            <!-- the swf floater -->
	            <span class="er_install_btn_holder" style="display: inline-block; position: absolute; left: 0; right: 0; top: 0; bottom: 0;">
	            	<!-- holder div (replaced with swf) goes here -->
	            </span>
	            
    	    </span>
    	    <span style="display: inline-block; padding: 70px 0 80px 10px;" class="er_hidden er_afterTen">
	    	    <a href="#" class="button-primary er_continue_link">Click Here Once Application Has Successfully Been Installed...</a>
	    	</span>
    	  </p>
          
          <p style="color: #999;" class="er_hidden er_afterTen">
            Having trouble with the auto-installer? Click <a href="#" style="color: #999;" class="er_revertManual_link">here</a> to install manually.
          </p>
          
        </div>
        
      </div> <!-- end .state3 -->

    </div> <!-- end .part2 -->
    
    
    <div class="part3 er_hidden">
      <p class="er_titleLine"><span style="font-weight: bold;">EasyRotator setup is complete and you're ready to start creating rotators!&nbsp;</span> <!--<span style="background: #AEF; padding: 1px 2px; font-weight: bold;">--></p>
      <p>To get started, use the new EasyRotator button in the Post Editor to add a rotator to your post/page. Or, use the EasyRotator Rotator widget on the <a href="widgets.php">Widgets</a> page.</p>
      <div class="er_notice_footer">
        <p><a class="er_help_button" href="admin.php?page=easyrotator_admin_help" target="_blank">Detailed Help</a> &nbsp;&nbsp;<a class="er_close_button" href="#" title="Dismiss this message...">Dismiss</a></p>
      </div>
    </div>
    
</div>
			
			
			
			<script type="text/javascript">
			//'
			(function($){
				$(function(){
					
					// ---- Main setup ----
					
					var box = $('div.easyrotator_welcome_notice');
					
					// [utils]
					var curPart = 1, 
						curState = 1;
					var setView = function(part, state)
					{
						// Set the part
						if (part != curPart)
						{
							curPart = part;
							box.find('div.part1').toggleClass('er_hidden', part!=1);
							box.find('div.part2').toggleClass('er_hidden', part!=2);
							box.find('div.part3').toggleClass('er_hidden', part!=3);
						}
						
						// Set the state if needed
						if (part == 2 && state !== undefined && state != curState)
						{
							curState = state;
							var p2 = box.find('div.part2');
							p2.find('div.state1').toggleClass('er_hidden', state!=1);
							p2.find('div.state2').toggleClass('er_hidden', state!=2);
							p2.find('div.state3').toggleClass('er_hidden', state!=3);
						}
					};
					var updateManualLinks = function(mac, win)
					{
						box.find('a.er_installer_link').attr('href', /Mac/i.test(navigator.platform) ? mac : win);
					};

                    var verifyInstallation = function(exitSuccessCallback)
                    {
                        // Create overlay
                        var id = 'erwn_verifyInstall_overlay';
                        var swfID = id + '_swf';
                        $('#' + id).remove();
                        var overlay = $('<div id="erwn_verifyInstall_overlay"><div style="text-align: center; padding-top: 200px;">Validating Installation... Please Wait...</div><div id="' + swfID + '">&nbsp;</div></div>').css({position:'fixed', left:0, top:0, width:'100%', height:'100%', zIndex:20000009, backgroundColor:'#FFF', opacity:0.8, color:'#666', font:'30px/36px bold Arial,Verdana,sans-serif'}).appendTo('body');

                        // Create callbacks
                        var callbackSuccess = id + '_cbS';
                        var callbackFailure = id + '_cbF';
                        var callbackNull = id + '_cbN';
                        window[callbackSuccess] = function()
                        {
                            // App really was installed successfully.  Continue.
                            overlay.remove();
                            exitSuccessCallback();
                        };
                        window[callbackFailure] = function()
                        {
                            // False start.  Notify we're not ready.
                            overlay.remove();
                            alert('Oops!  It appears that the EasyRotator application installation hasn\'t yet successfully completed.  Please check to see if the installer is still running, and be patient while each step of the installation completes.  If it isn\'t running, re-launch the auto installer, or follow the manual installation instructions if you\'re having trouble.');
                        };
                        window[callbackNull] = function(){};

                        // Embed detector SWF
                        var swfVersionStr = "10.0.0";
                        var xiSwfUrlStr = "";
                        var flashvars = {
                            callbackError: callbackFailure,
                            callbackUnavailable: callbackFailure,
                            callbackAvailable: callbackSuccess,
                            callbackClick: callbackNull
                        };
                        var params = {};
                        params.quality = "high";
                        params.bgcolor = "#ffffff";
                        params.wmode = "transparent";
                        params.allowscriptaccess = "sameDomain";
                        params.allowfullscreen = "true";
                        var attributes = {};
                        attributes.id = swfID;
                        attributes.name = swfID;
                        swfobject.embedSWF(
                            "<?php echo($this->url); ?>img/EasyRotatorWizard_installDetector.swf?rev=1", swfID,
                            "1", "1",
                            swfVersionStr, xiSwfUrlStr,
                            flashvars, params, attributes);

                        // If we don't have anything after 15 seconds, fail it.
                        setTimeout(function(){
                            if (overlay && overlay.parent() && overlay.parent().length > 0)
                                window[callbackFailure]();
                        }, 15000);

                    };
					
					// See if FP is available
					if (!swfobject.hasFlashPlayerVersion('10.0.0'))
					{
						// Switch to 'upgrade fp' message
						setView(2);
					}
					else
					{
						// Create the callbacks - erwn=easyrotator_welcome_notice
						var callbackError = 'erwn_callbackError';
						var callbackAvailable = 'erwn_callbackAvailable';
						var callbackUnavailable = 'erwn_callbackUnavailable';
						var callbackClick = 'erwn_callbackClick';

						window[callbackError] = function(mac, win)
						{
							// Use the passed URLs to update the links
							updateManualLinks(mac, win);
							
							// Air doesn't seem to be available, or isn't working properly; try manual install.
							setView(2, 2);
						};
						window[callbackUnavailable] = function(mac, win)
						{
							// App isn't installed, but auto-install is available.  Switch to install view.
							setView(2, 3);
							
							// Use the passed URLs to update the links, in case we need to fall back to manual
							updateManualLinks(mac, win);
						};
						window[callbackAvailable] = function()
						{
							// App is installed; life is good.  Switch to confirmation view.
							setView(3);
						};
						
						var clickProcessed = false;
						window[callbackClick] = function()
						{
							// Auto-install button was just clicked... update UI, set timeout to update it again.
							if (clickProcessed)
								return;
							clickProcessed = true;
							
							var wrap = box.find('div.er_install_btn_wrap');
							var btn = wrap.find('a.er_install_btn_link');
							
							btn.text('Please Be Patient While Installer Initializes...');
							
							setTimeout(function()
							{
								// Update the btn, show new elements
								btn.removeClass('button-primary').addClass('button-secondary').text('Re-Launch Auto-Installer');
								wrap.find('.er_afterTen').removeClass('er_hidden');
							}, 10000); // 10 secs by default... may be updated
						};
						
						// Create the magic SWF
						(function(){
							var id = 'erwn_installDetector_' + Math.round(Math.random() * 1000);
							var swfHolder = box.find('span.er_install_btn_holder, div.er_install_btn_holder').append('<span style="display: inline-block; position: absolute; left: 0; right: 0; top: 0; bottom: 0;" id="' + id + '"></span>');
						
							var swfVersionStr = "10.0.0";
				        	var xiSwfUrlStr = "";
	            			var flashvars = {
    	        								callbackError: callbackError,
    	        								callbackUnavailable: callbackUnavailable,
    	        								callbackAvailable: callbackAvailable,
    	        								callbackClick: callbackClick
	            							};
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
				                "<?php echo($this->url); ?>img/EasyRotatorWizard_installDetector.swf?rev=1", id, 
				                "100%", "100%", 
				                swfVersionStr, xiSwfUrlStr, 
			    	            flashvars, params, attributes);
						})();
						
						// Wire up the continue btns
						box.find('a.er_continue_link').click(function(e)
						{
							e.preventDefault();

                            verifyInstallation(function(){
							    setView(3); // go to confirmation view.
                            });
						});
						
						// Wire up the 'having trouble? try manual install' link
						box.find('a.er_revertManual_link').click(function(e)
						{
							e.preventDefault();
							
							// Lest we have problems with the SWF coming back to haunt us (making bogus calls), nuke it.
							box.find('span.er_install_btn_holder, div.er_install_btn_holder').html('');
							
							// Switch back to manual view
							setView(2, 2);
						});
						
						// Wire up the manual install link (not the actual link, mind you --that comes from the SWF--, but rather the UI behaviors)
						box.find('a.er_installer_link').mousedown(function()
						{
							var $this = $(this);
							setTimeout(function($this)
							{
								// Update the first button
								$this.removeClass('button-primary').addClass('button-secondary').text('Re-Download Installer');
								
								// Show the continue button
								var continueLink = $this.parent().find('a.er_afterTen').removeClass('er_hidden');
							}, 10000, $this); // 10 secs by default... may be updated. be careful to keep scope!
						});
					}
					
					
					// --------------------
					
					// Wire up the close button ajax
					$('div.easyrotator_welcome_notice a.er_close_button').click(function(e)
					{
						e.preventDefault();
						
						var btn = $(this);
						var box = btn.closest('div.updated, div.error');
						var data = {
							action: 'easyrotator_welcome_notice_hide',
							security: '<?php echo($ajax_nonce); ?>'
						};
						btn.html('<em>Dismissing...</em>');
						
						$.post(ajaxurl, data, function(result){  
							try {
								var obj = eval('(' + result + ')');
								if (obj.success)
								{
									btn.remove();
									box.slideUp();
								}
								else throw 'bla';
							}
							catch (e) {
								btn.html('<em>Error Disimissing.</em>');
							}
						});
					});


                    // Display preemptive error message for Chrome21+ users on Windows, Chrome 23+ users on Mac.  There's a new bug affecting AIR and Chrome.
                    var chromeNoteBox = box.find('div.er_chromeAIRBugNote');
                    var chromeMatches = navigator.userAgent.match(/\sChrome\/(\d+)/i);
                    if (/^win/i.test(navigator.platform) && chromeMatches)
                    {
                        var chromeVersion = parseInt(chromeMatches[1]);
                        if (chromeVersion > 20) // problem appeared in Chrome 21 on Win
                        {
                            chromeNoteBox.show();
                        }
                    }
                    else if (/^mac/i.test(navigator.platform) && chromeMatches)
                    {
                        var chromeVersion = parseInt(chromeMatches[1]);
                        if (chromeVersion > 22) // problem appeared in Chrome 23 on Mac
                        {
                            chromeNoteBox.show();
                        }
                    }

					
				});
			})(jQuery);
			</script>
			
			
	
	
<?php
	}
	else // if ($useAfterActivationSetup)
	{
?>
	
			<div class="updated easyrotator_welcome_notice">
			
				<p class="er_titleLine"><span style="font-weight: bold;">Welcome to EasyRotator!</span></p>
				<p>To get started, use the new EasyRotator button in the Post Editor to add a rotator to your post/page. Or, use the EasyRotator Rotator widget on the <a href="widgets.php">Widgets</a> page. &nbsp;&nbsp;</p>
				<p><a class="er_help_button" href="admin.php?page=easyrotator_admin_help" target="_blank">Detailed Help</a> &nbsp;&nbsp;<a class="er_close_button" href="#" title="Dismiss this message...">Dismiss</a></p>
			
			</div>
	
			<script type="text/javascript">
			(function($){
				$(function(){
					$('div.easyrotator_welcome_notice a.er_close_button').click(function(e)
					{
						e.preventDefault();
						
						var btn = $(this);
						var box = btn.closest('div.updated, div.error');
						var data = {
							action: 'easyrotator_welcome_notice_hide',
							security: '<?php echo($ajax_nonce); ?>'
						};
						btn.html('<em>Dismissing...</em>');
						
						$.post(ajaxurl, data, function(result){  
							try {
								var obj = eval('(' + result + ')');
								if (obj.success)
								{
									btn.remove();
									box.slideUp();
								}
								else throw 'bla';
							}
							catch (e) {
								btn.html('<em>Error Disimissing.</em>');
							}
						});
					});
				});
			})(jQuery);
			</script>
	
<?php
	} // end if ($useAfterActivationSetup)
?>