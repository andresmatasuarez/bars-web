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


(function() {
	// Load plugin specific language pack
	//tinymce.PluginManager.requireLangPack('easyrotator');

	tinymce.create('tinymce.plugins.EasyRotatorPlugin', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
			
			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceEasyRotator');
			ed.addCommand('mceEasyRotator', function() {
				/*
				jQuery('#easyrotator_tinymce_dialog').dialog({
					title: 'Select a Rotator To Insert',
					width: 700,
					height: 500
				});
				*/
				try {
					easyrotator_tinymce_dialog_launch();
				} catch (e) {
					alert('Error - unable to process action.');
				}
			});

			// Register easyrotator button
			ed.addButton('easyrotator', {
				title : 'Add EasyRotator Rotator To This Post...',
				cmd : 'mceEasyRotator',
				image : url + '/../../img/er_icon_20_bw.png'
			});

			// Add a node change handler, selects the button in the UI when a image is selected
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('example', n.nodeName == 'IMG');
			});
			
		},

		/**
		 * Creates control instances based in the incomming name. This method is normally not
		 * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
		 * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
		 * method can be used to create those.
		 *
		 * @param {String} n Name of the control to create.
		 * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
		 * @return {tinymce.ui.Control} New control instance or null if no control was created.
		 */
		createControl : function(n, cm) {
			return null;
		},

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
				longname : 'EasyRotator Worpdress TinyMCE Button',
				author : 'dwuser',
				authorurl : 'http://www.dwuser.com/',
				infourl : 'http://www.dwuser.com/easyrotator/',
				version : '1.0'
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('easyrotator', tinymce.plugins.EasyRotatorPlugin);
})();