const MARGIN_TOP_WHEN_FIXED = 15;
const MARGIN_BOTTOM_WHEN_FIXED = 0;
const OFFSET = 42 + MARGIN_TOP_WHEN_FIXED; // sticky header (.nav-menu height)

export default function stickyElement(parentElementSelector, stickyElementSelector) {
  $(parentElementSelector).map(function () {
    const parent = $(this);

    const sticky = parent.find(stickyElementSelector);
    const stickyTop = sticky.offset().top - sticky.outerHeight();

    $(window).scroll(function() {
      const windowTop = $(window).scrollTop();

      if (stickyTop - OFFSET < windowTop) {
        if (parent.offset().top + parent.outerHeight() - sticky.outerHeight() - OFFSET - MARGIN_BOTTOM_WHEN_FIXED > windowTop) {
          sticky.css('position', 'fixed');
          sticky.css('top', OFFSET);
          sticky.css('bottom', 'unset');
        } else {
          sticky.css('position', 'absolute');
          sticky.css('top', 'unset')
          sticky.css('bottom', MARGIN_BOTTOM_WHEN_FIXED);
        }
      } else {
        sticky.css('position', 'unset');
        sticky.css('top', 'unset');
        sticky.css('bottom', 'unset');
      }
    });
  });
}
