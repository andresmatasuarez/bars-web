<?php

  function parseScreening($screening) {
    if (substr($screening, 0, strlen("streaming!")) === "streaming!") {
      preg_match('/(\s*streaming!(?<venue>([A-Za-z]+))?:\s*)?(?P<date>[^\s]+)\s*/', $screening, $matches);
      return array(
        'streaming' => true,
        'venue' => strtolower(trim($matches['venue'])),
        'date' => strtolower(trim($matches['date'])),
      );
    }

    preg_match('/(\s*(?<venue>([A-Za-z]+))\s*[^\.]*(\s*\.\s*(?<room>.+))?:\s*)?(?P<date>[^\s]+)\s+(?P<time>.+)/', $screening, $matches);
    return array(
      'venue' => strtolower(trim($matches['venue'])),
      'room' => strtolower(trim($matches['room'])),
      'date' => strtolower(trim($matches['date'])),
      'time' => strtolower(trim($matches['time']))
    );
  }

  function parseScreenings($screeningsValue) {
    $screenings = array_filter(explode(',', $screeningsValue)); // removes empty screenings
    $screenings = array_map('trim', $screenings);
    $screenings = array_map('parseScreening', $screenings);
    return $screenings;
  }

  function groupScreeningsByVenue($screenings) {
    $grouped = array();
    foreach($screenings as $key => $screening){
      $venue = $screening['venue'];

      if (isset($grouped[$venue])) {
        $grouped[$venue][] = $screening;
      } else {
        $grouped[$venue] = array();
        $grouped[$venue][] = $screening;
      }
    }
    return $grouped;
  }

  function renderWarningMessage($title, $message, $customClass = '') {
?>
    <div class="bars-warning-message <?php echo $customClass; ?>">
      <div>
        <span class="fa fa-exclamation-circle"></span>
      </div>
      <h1><?php echo $title; ?></h1>
      <p><?php echo $message; ?></p>
    </div>
<?php
  }

  function getDateInSpanish($date) {
    return $date->format('j') . ' de ' . getSpanishMonthName($date->format('F')) . ' de ' . $date->format('Y');
  }

  function parseDate($date = null){
    if (empty($date)) {
      return null;
    }

    $date = new DateTime($date);
    $date->setTimezone(new DateTimeZone('America/Argentina/Buenos_Aires'));
    return $date;
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
