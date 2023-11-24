import $ from 'jquery';

export default function slider() {
  const w = this.width();
  const h = this.height();
  const slides = this.find('.slide');
  const slidesCount = slides.length;

  let interval = null;
  let currentSlide = this.find('.slide[slide-position=0]');

  const slideFunction = (toLeft) => {
    const currentSlidePosition = parseInt(currentSlide.attr('slide-position'));
    let d;
    if (currentSlidePosition === 0 && toLeft) {
      d = slidesCount - 1;
    } else if (currentSlidePosition === slidesCount - 1 && !toLeft) {
      d = 0;
    } else {
      d = currentSlidePosition + (toLeft ? -1 : 1);
    }

    slides.each(function () {
      $(this).animate(
        {
          left: `${(parseInt($(this).attr('slide-position')) - d) * w}px`,
        },
        'slow',
        function () {
          if (interval === null) {
            interval = setInterval(() => slideFunction(false), 5000);
          }
        },
      );
    });
    currentSlide = this.find(`.slide[slide-position=${d}]`);
  };

  // Slider controls behaviour
  this.find('.slider-control').on('click', function (/*el, event*/) {
    if (interval !== null) {
      clearInterval(interval);
      interval = null;
    }
    slideFunction($(this).hasClass('left'));
  });

  // Slider interval function.
  interval = setInterval(() => slideFunction(false), 5000);

  // Hide slider controls.
  this.find('.slider-controls').hide();
  this.hover(() => {
    // Only toggle slider controls if there is more than one slide.
    if ($('.slide').length > 1) {
      this.find('.slider-controls').fadeToggle(200);
    }
  });

  // Link slides to respective posts.
  slides.each(function () {
    $(this).click(function () {
      window.location = $(this).attr('permalink');
    });
  });

  // Place slides in a consecutive way.
  let left = 0;
  slides.each(function () {
    $(this)
      .find('img')
      .wrap(function () {
        return `<div style="left:${left}px; display:block; width:${w}px; height:${h}px;"></div>`;
      });
    $(this).find('img').parent().imgLiquid();
    left += w;
  });
}
