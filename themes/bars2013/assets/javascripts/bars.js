export default function () {
  // IMAGE THUMBS HANDLING
  // Home page posts image resize & cropping
  $('.posts .post-thumbnail img').wrap(() => '<div style="width:270px; height:170px;"></div>');
  $('.posts .post-thumbnail img').parent().imgLiquid();

  // Single post related posts
  $('#page-single .post-related-posts .post-related-post img').wrap(
    () => '<div style="width:200px; height:170px;"></div>',
  );
  $('#page-single .post-related-posts .post-related-post img').parent().imgLiquid();

  // Sidebar image widget resize & cropping
  $('#sidebar .bars-widget.sidebar.image img').wrap(
    () => '<div style="width:300px; height:150px;"></div>',
  );
  $('#sidebar .bars-widget.sidebar.image img')
    .parent()
    .imgLiquid({ horizontalAlign: 'center', verticalAlign: 'center' });

  if (window.IS_CALL_OPEN) {
    // Call is open
    const callNavItem = $('#header-menu .nav-menu li:nth-child(6)');
    const callNavItemLink = callNavItem.find('a');
    const callPageHref = callNavItemLink.attr('href');
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
    throttleDuration: 100, //re-focus images at most once every 100ms.
  });

  // Slider
  $('.slider').slider();

  // Fancybox initialization for sidebar image widgets
  $('.fancybox.sidebar').fancybox({
    padding: 2,
    type: 'image',
    scrolling: 'hidden',
    helpers: { overlay: { locked: true } },
  });

  // Selection page
  // Select current edition by default.
  if (window.CURRENT_EDITION) {
    $('#edition-selector').val(window.CURRENT_EDITION);
  }

  $('#edition-selector').change(function () {
    const selectedEdition = parseInt($(this).val(), 10);
    const queryString = new URLSearchParams(window.location.search);

    if (selectedEdition === window.LATEST_EDITION) {
      queryString.delete('edition');
    } else {
      queryString.set('edition', selectedEdition);
    }

    window.location.search = queryString.toString();
  });
}
