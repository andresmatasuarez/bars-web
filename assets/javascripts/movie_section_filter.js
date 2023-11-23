import $ from 'jquery';

export default function movieSectionFilter() {
  const object = this;

  // Default filter: all sections.
  object.val('all');

  object.change(function () {
    const selected = object.val();

    // First of all, hide all schedule days and movie posts.
    $('.schedule .schedule-day, .schedule .scratch, .schedule .schedule-day .movie-post').hide();

    if (selected === 'all') {
      $('.schedule .schedule-day, .schedule .scratch, .schedule .schedule-day .movie-post').show();
    } else {
      $(`.schedule .schedule-day .movie-post[section="${selected}"]`).each(function () {
        $(this).show();
        $(this).closest('.schedule .schedule-day').show();
        $(this).closest('.schedule .schedule-day').next('.schedule .scratch').show();
      });
    }

    $('.schedule .schedule-day:visible:odd').removeClass('odd even').addClass('odd');
    $('.schedule .schedule-day:visible:even').removeClass('odd even').addClass('even');
  });
}
