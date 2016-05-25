import $ from 'jquery';

export default function stickyMenu() {
  const object          = $(this);
  const adminBarOffset  = $('#wpadminbar').length ? $('#wpadminbar').height() : 0;
  const stickyTop       = object.offset().top;
  const initialPosition = object.css('position');
  const initialTop      = object.css('top');

  $(window).scroll(function() {
    const scrollTop = $(window).scrollTop();

    // If we scroll more than the navigation, change its position to fixed and add class 'sticky'.
    // Otherwise, change it back to absolute and remove the class.
    if (scrollTop > stickyTop) {
      object.css({ position: 'fixed', top: adminBarOffset }).addClass('sticky');
    } else {
      object.css({ position: initialPosition, top: initialTop }).removeClass('sticky');
    }
  });
}
