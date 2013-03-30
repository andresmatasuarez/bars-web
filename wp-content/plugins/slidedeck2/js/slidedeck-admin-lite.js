/*!
 * SlideDeck 2 Lite for WordPress Lite Admin JavaScript
 * 
 * More information on this project:
 * http://www.slidedeck.com/
 * 
 * Full Usage Documentation: http://www.slidedeck.com/usage-documentation 
 * 
 * @package SlideDeck
 * @subpackage SlideDeck 2 Lite for WordPress
 * 
 * @author dtelepathy
 */
/*!
Copyright 2012 digital-telepathy  (email : support@digital-telepathy.com)

This file is part of SlideDeck.

SlideDeck is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

SlideDeck is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with SlideDeck.  If not, see <http://www.gnu.org/licenses/>.
*/
(function($){$(document).ready(function(){$('body').bind('slidedeck:lens-change-update-choices',function(){if($('#options-total_slides').length){$('#options-total_slides').attr('readonly',true);$('#options-total_slides').parent().append('<em><a class="upgrade-modal" href="'+slideDeck2AddonsURL+'" rel="slidecount">Upgrade</a> to get more slides per deck.</em>')}if($('#slidedeck-covers').length){$('#slidedeck-covers').append('<span class="lite-disabled-mask"><em><a class="upgrade-modal" href="'+slideDeck2AddonsURL+'" rel="covers">Upgrade</a> to get access to covers.</em></span>')}});$('body').trigger('slidedeck:lens-change-update-choices');SlideDeckPlugin.dtLabsAccountModal=new SimpleModal({context:"dt-account",onComplete:function(modal){$('.upsell-modal .cta').on('click','a',function(e){if($(this).attr('id')=='dt-labs-learn-more'){$(this).siblings('.no-thanks').click()}else{e.preventDefault();$.ajax({url:this.href,type:"POST",success:function(response){},})}SlideDeckPlugin.dtLabsAccountModal.close()})}})})})(jQuery);