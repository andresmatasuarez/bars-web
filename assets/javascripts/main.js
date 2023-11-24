import 'jquery-focuspoint/js/jquery.focuspoint.js';
import 'jquery-form';
import 'jquery-validation';

import fancybox from 'fancybox';

import bars from './bars';
import contactForm from './contact_form';
import pressForm from './press_form';
import slider from './slider';
import stickyMenu from './sticky_menu';

// jQuery setup
fancybox($);
$.fn.extend({ stickyMenu, slider });

$(function () {
  bars();
  contactForm();
  pressForm();
});
