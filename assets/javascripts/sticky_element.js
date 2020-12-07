const MOVIE_POST_MARGIN_BOTTOM = 10;
const SHCEDULE_DAY_PADDING_BOTTOM = 15;
const MARGIN_BOTTOM_WHEN_FIXED = MOVIE_POST_MARGIN_BOTTOM + SHCEDULE_DAY_PADDING_BOTTOM;

const OFFSET = 15; // Space separating element being scrolled from the top
const MIN_HEIGHT_TO_SCROLL = 100;

export default function stickyElement(
  parentElementSelector,
  stickyElementSelector,
  // allows a dynamic offset to be passed. This callback gets called for  each scroll event.
  offsetCallback
) {
  $(parentElementSelector).map(function (index) {
    const parent = $(this);
    const sticky = parent.find(stickyElementSelector);

    $(window).scroll(function() {
      const windowTop = $(window).scrollTop();
      const parentOffsetTop = parent.offset().top;

      const offset = OFFSET + offsetCallback();

      // If parent's height doesn't surpass min height, element won't be scrolled.
      // This is to avoid elements to scroll only for a few pixels.
      if (parent.outerHeight() < MIN_HEIGHT_TO_SCROLL) {
        return;
      }

      if (parentOffsetTop < windowTop + offset) {
        if (parentOffsetTop + parent.outerHeight() - sticky.outerHeight() - offset - MARGIN_BOTTOM_WHEN_FIXED > windowTop) {
          sticky.css('position', 'fixed');
          sticky.css('top', offset);
          sticky.css('bottom', 'unset');
        } else {
          sticky.css('position', 'absolute');
          sticky.css('top', 'unset')
          sticky.css('bottom', MOVIE_POST_MARGIN_BOTTOM);
        }
      } else {
        sticky.css('position', 'unset');
        sticky.css('top', 'unset');
        sticky.css('bottom', 'unset');
      }
    });
  });
}
