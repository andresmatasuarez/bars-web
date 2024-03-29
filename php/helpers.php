<?php
  require_once 'editions.php';

  const DATE_FULL_TAG = 'full';

  function sortByDateString($a, $b) {
    // safeguard against NULL values
    $t1 = isset($a['date']) ? $a['date']->getTimestamp() : 0;
    $t2 = isset($b['date']) ? $b['date']->getTimestamp() : 0;
    return $t1 - $t2;
  }

  function parseScreening($screening) {
    $tz = new DateTimeZone('America/Argentina/Buenos_Aires');

    if (substr($screening, 0, strlen("streaming!")) === "streaming!") {
      preg_match('/(\s*streaming!(?<venue>([A-Za-z]+))?:\s*)?(?P<date>[^\s]+)\s*/', $screening, $matches);
      $rawDate = strtolower(trim($matches['date']));
      $date = $rawDate === DATE_FULL_TAG ? null : dateWithoutTime(parseDate($rawDate . ' ' . $tz->getName(), 'm-d-Y e'));
      return array(
        'raw' => $screening,
        'streaming' => true,
        'alwaysAvailable' => $rawDate === DATE_FULL_TAG,
        'venue' => strtolower(trim($matches['venue'])),
        'isoDate' => $date === null ? null : $date->format(DateTime::ATOM)
      );
    }

    preg_match('/(\s*(?<venue>([A-Za-z]+))\s*[^\.]*(\s*\.\s*(?<room>.+))?:\s*)?(?P<date>[^\s]+)\s+(?P<time>.+)/', $screening, $matches);
    $date = strtolower(trim($matches['date']));
    $time = strtolower(trim($matches['time']));
    $date = parseDate($date . ' ' . $time . ' ' . $tz->getName(), 'm-d-Y H:i e');
    return array(
      'raw' => $screening,
      'venue' => strtolower(trim($matches['venue'])),
      'room' => strtolower(trim($matches['room'])),
      'time' => $time,
      'isoDate' => $date->format(DateTime::ATOM)
    );
  }

  function parseScreenings($screeningsValue) {
    $screenings = array_filter(explode(',', $screeningsValue)); // removes empty screenings
    $screenings = array_map('trim', $screenings);
    $screenings = array_map('parseScreening', $screenings);
    usort($screenings, 'sortByDateString');
    return $screenings;
  }

  function getDateInSpanish($date) {
    return $date->format('j') . ' de ' . getSpanishMonthName($date->format('F')) . ' de ' . $date->format('Y');
  }

  function dateWithTZ($date) {
    return $date->setTimezone(new DateTimeZone('America/Argentina/Buenos_Aires'));
  }

  function dateWithoutTime($date) {
    $newDate = dateWithTZ(new DateTime());
    $newDate->setTimestamp($date->getTimestamp());
    return $newDate->settime(0,0);
  }

  function parseDate($date = null, $format = null){
    if (empty($date)) {
      return null;
    }

    if (!isset($format) || $format === 'c') {
      $date = new DateTime($date);
    } else {
      $date = DateTime::createFromFormat($format, $date);
    }
    return dateWithTZ($date);
  }

  function prepareMovieForDisplaying($post) {
    $isMovie = get_post_type($post->ID) === 'movie';

    if ($isMovie){
      $year = get_post_meta($post->ID, '_movie_year', true);
      $country = get_post_meta($post->ID, '_movie_country', true);
      $runtime = get_post_meta($post->ID, '_movie_runtime', true);
      $info = ($year !== '') ? $year : '';

      if ($country !== '') {
        $info = ($info !== '' ? $info . ' - ' : '') . $country;
      }

      if ($runtime) {
        $info = ($info !== '' ? $info . ' - ' : '') . $runtime . ' min.';
      }

      $section = get_post_meta($post->ID, '_movie_section', true);
      $screeningsValue = get_post_meta($post->ID, '_movie_screenings', true);
    } else {
      $section = get_post_meta($post->ID, '_movieblock_section', true);
      $info = get_post_meta($post->ID, '_movieblock_runtime', true) . ' min.';
      $screeningsValue = get_post_meta($post->ID, '_movieblock_screenings', true);
    }

    $screenings = parseScreenings($screeningsValue);

    return array(
      "id" => $post->ID,
      "title" => get_the_title($post->ID),
      "section" => $section,
      "permalink" => get_post_permalink($post->ID),
      "thumbnail" => get_the_post_thumbnail($post->ID, 'movie-post-thumbnail'),
      "screenings" => $screenings,
      "info" => $info
    );
  }

  // @src http://stackoverflow.com/questions/2762061/how-to-add-http-if-its-not-exists-in-the-url
  function addHttp($url) {
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = "http://" . $url;
    }
    return $url;
  }
?>
