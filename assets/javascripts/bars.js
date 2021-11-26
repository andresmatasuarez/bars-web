import stickyElement from './sticky_element';
import collapsible from './collapsible';

export default function(){

  // IMAGE THUMBS HANDLING
  // Home page posts image resize & cropping
  $('.posts .post-thumbnail img').wrap(() => '<div style="width:270px; height:170px;"></div>');
  $('.posts .post-thumbnail img').parent().imgLiquid();

  // Single post related posts
  $('#page-single .post-related-posts .post-related-post img').wrap(() => '<div style="width:200px; height:170px;"></div>');
  $('#page-single .post-related-posts .post-related-post img').parent().imgLiquid();

  // Sidebar image widget resize & cropping
  $('#sidebar .bars-widget.sidebar.image img').wrap(() => '<div style="width:300px; height:150px;"></div>');
  $('#sidebar .bars-widget.sidebar.image img').parent().imgLiquid({ horizontalAlign: 'center', verticalAlign: 'center' });

  if (window.IS_CALL_OPEN) {
    // Call is open
    const callNavItem      = $('#header-menu .nav-menu li:nth-child(6)');
    const callNavItemLink  = callNavItem.find('a');
    const callPageHref     = callNavItemLink.attr('href');
    const callNavItemLabel = callNavItemLink.html();
    callNavItem.addClass('call-is-open');
    callNavItemLink.css('visibility', 'hidden');
    callNavItemLink.html('WWWWWWWW'); // Longer text so it takes more horizontal space
    callNavItem.html(`
      ${callNavItem.html()}
      <span class="call-is-open-label call-is-open-label-pre">Abierta la</span>
      <a class="call-is-open-label call-is-open-link" href="${callPageHref}">
        ${callNavItemLabel}
      </a>
      <span class="call-is-open-label call-is-open-label-post">
        ${$('#current-edition-year').html()}
      </span>
    `);
  }

  // Sticky navigation menu
  $('#header-menu').stickyMenu();

  // Focuspoint
  $('.focuspoint').focusPoint({
    throttleDuration: 100 //re-focus images at most once every 100ms.
  });

  // Slider
  $('.slider').slider();

  // Selections
  $('#schedule-section-filters').movieSectionFilter();
  $('.movie-post .movie-post-title').dotdotdot();

  // Programación
  $('body').on('click', '#movie-selector', function(){
    let scroll = 0;
    $('#movie-' + $(this).data('movieId')).prevAll().each(function(index,elem){
      scroll += $(elem).outerHeight(true);
    });

    $('.movie-info-displayer').animate({
      scrollTop: scroll
    });
  });

  // Fancybox initialization for sidebar image widgets
  $('.fancybox.sidebar').fancybox({
    padding   : 2,
    type      : 'image',
    scrolling : 'hidden',
    helpers   : { overlay: { locked: true } }
  });

  // Fancybox initialization for movie displayers.
  $('#page-selection .movie-post > a').fancybox({
    padding   : 2,
    scrolling : 'hidden',
    helpers   : { overlay: { locked: true } },
    beforeLoad(){
      $.fancybox.showLoading();
      $.ajax({
        async: false,
        url: $(this.element).attr('link'),
        data: {
          edition: window.CURRENT_EDITION,
        },
        success(data) {
          $('#movie-container').html(data);
          $.fancybox.hideLoading();
        }
      });
    },
    afterClose(){
      $('#movie-container').empty();
    }
  });

  // Sort movies by hour asc.
  $('.schedule-day .movie-posts').each(function(){
    const movies = $(this).find('.movie-post');
    movies.sort((x, y) => {
      let hourX = $(x).find('.movie-post-hour').html();
      let hourY = $(y).find('.movie-post-hour').html();

      if (!hourX || !hourY) {
        return 0;
      }

      hourX = hourX.split(':');
      hourY = hourY.split(':');

      const dateX = new Date();
      dateX.setHours(parseInt(hourX[0], 10));
      dateX.setMinutes(parseInt(hourX[1], 10));

      const dateY = new Date();
      dateY.setHours(parseInt(hourY[0], 10));
      dateY.setMinutes(parseInt(hourY[1], 10));

      x = dateX.getTime();
      y = dateY.getTime();

      return ((x < y) ? -1 : ((x > y) ?  1 : 0));
    });

    $(this).find('.movie-post').remove();

    $(this).append(movies);

  });

  // Selection page
  // Select current edition by default.
  if (window.CURRENT_EDITION) {
    $('#edition-selector').val(window.CURRENT_EDITION);
  }
  $('#edition-selector').change(function(){
    const selectedEdition = parseInt($(this).val(), 10);
    const queryString = new URLSearchParams(window.location.search);

    if (selectedEdition === window.LATEST_EDITION) {
      queryString.delete('edition');
    } else {
      queryString.set('edition', selectedEdition);
    }

    window.location.search = queryString.toString();
  });

  // Sticky days in selection.php
  stickyElement('.schedule-day-info', '.sticky-day-wrapper', () => {
    return $('#header-menu').hasClass('sticky') ? 42 : 0;
  });

  collapsible('collapsible-trigger', 'collapsible');

};
