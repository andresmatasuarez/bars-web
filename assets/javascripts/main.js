import fancybox from 'fancybox';

import 'jquery.dotdotdot/src/jquery.dotdotdot.js';
import 'jquery-form';
import 'jquery-validation';
import 'jquery-focuspoint/js/jquery.focuspoint.js';

import slider from './slider';
import stickyMenu from './sticky_menu';
import movieSectionFilter from './movie_section_filter';

// jQuery setup
fancybox($);
$.fn.extend({ stickyMenu, movieSectionFilter, slider });

import bars from './bars';
import contactForm from './contact_form';
import pressForm from './press_form';

$(function () {
  bars();
  contactForm();
  pressForm();
});
