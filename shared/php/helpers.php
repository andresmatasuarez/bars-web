<?php
  require_once 'editions.php';

  const DATE_FULL_TAG = 'full';

  function getSpanishMonthName($englishMonth){
    switch(strtolower($englishMonth)){
      case 'january'   :  return 'enero';
      case 'february'  :  return 'febrero';
      case 'march'     :  return 'marzo';
      case 'april'     :  return 'abril';
      case 'may'       :  return 'mayo';
      case 'june'      :  return 'junio';
      case 'july'      :  return 'julio';
      case 'august'    :  return 'agosto';
      case 'september' :  return 'septiembre';
      case 'october'   :  return 'octubre';
      case 'november'  :  return 'noviembre';
      case 'december'  :  return 'diciembre';
      default          :  return null;
    }
  }

  function getSpanishDayName($englishDay){
    switch(strtolower($englishDay)){
      case 'monday'    :  return 'lunes';
      case 'tuesday'   :  return 'martes';
      case 'wednesday' :  return 'miércoles';
      case 'thursday'  :  return 'jueves';
      case 'friday'    :  return 'viernes';
      case 'saturday'  :  return 'sábado';
      case 'sunday'    :  return 'domingo';
      default          :  return null;
    }
  }

  function displayDateInSpanish($date){
    return getSpanishDayName($date->format('l')) . ' ' . $date->format('j') . ' de ' . getSpanishMonthName($date->format('F'));
  }

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

    // Extract optional ticket URL after the last '|'
    $originalRaw = $screening;
    $ticketUrl = '';
    $pipePos = strrpos($screening, '|');
    if ($pipePos !== false) {
      $ticketUrl = trim(substr($screening, $pipePos + 1));
      $ticketUrl = str_replace(array('%2C', '%7C'), array(',', '|'), $ticketUrl);
      $screening = substr($screening, 0, $pipePos);
    }

    preg_match('/(\s*(?<venue>([A-Za-z]+))\s*[^\.]*(\s*\.\s*(?<room>.+))?:\s*)?(?P<date>[^\s]+)\s+(?P<time>.+)/', $screening, $matches);
    $date = strtolower(trim($matches['date']));
    $time = strtolower(trim($matches['time']));
    $date = parseDate($date . ' ' . $time . ' ' . $tz->getName(), 'm-d-Y H:i e');
    $result = array(
      'raw' => $originalRaw,
      'venue' => strtolower(trim($matches['venue'])),
      'room' => strtolower(trim($matches['room'])),
      'time' => $time,
      'isoDate' => $date->format(DateTime::ATOM)
    );
    if ($ticketUrl !== '') {
      $result['ticketUrl'] = $ticketUrl;
    }
    return $result;
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

  function getShortsForBlock($blockId) {
    $shorts = get_posts(array(
      'post_type' => 'movie',
      'meta_query' => array(
        array(
          'key' => '_movie_movieblock',
          'value' => $blockId,
        ),
      ),
      'posts_per_page' => -1,
      'orderby' => 'menu_order',
      'order' => 'ASC',
    ));

    return array_map(function($short) {
      $year = get_post_meta($short->ID, '_movie_year', true);
      $country = get_post_meta($short->ID, '_movie_country', true);
      $runtime = get_post_meta($short->ID, '_movie_runtime', true);
      $shortInfo = ($year !== '') ? $year : '';
      if ($country !== '') {
        $shortInfo = ($shortInfo !== '' ? $shortInfo . ' • ' : '') . $country;
      }
      if ($runtime) {
        $shortInfo = ($shortInfo !== '' ? $shortInfo . ' • ' : '') . $runtime . ' min.';
      }

      return array(
        'id' => $short->ID,
        'title' => get_the_title($short->ID),
        'info' => $shortInfo,
        'thumbnail' => get_the_post_thumbnail($short->ID, get_template() . '-movie-post-thumbnail'),
        'heroThumbnail' => get_the_post_thumbnail($short->ID, 'large'),
        'directors' => get_post_meta($short->ID, '_movie_directors', true) ?: null,
        'synopsis' => get_post_meta($short->ID, '_movie_synopsis', true) ?: null,
        'streamingLink' => get_post_meta($short->ID, '_movie_streamingLink', true) ?: null,
      );
    }, $shorts);
  }

  function prepareMovieForDisplaying($post) {
    $isMovie = get_post_type($post->ID) === 'movie';

    if ($isMovie){
      $year = get_post_meta($post->ID, '_movie_year', true);
      $country = get_post_meta($post->ID, '_movie_country', true);
      $runtime = get_post_meta($post->ID, '_movie_runtime', true);
      $info = ($year !== '') ? $year : '';

      if ($country !== '') {
        $info = ($info !== '' ? $info . ' • ' : '') . $country;
      }

      if ($runtime) {
        $info = ($info !== '' ? $info . ' • ' : '') . $runtime . ' min.';
      }

      $section = get_post_meta($post->ID, '_movie_section', true);
      $screeningsValue = get_post_meta($post->ID, '_movie_screenings', true);
    } else {
      $section = get_post_meta($post->ID, '_movieblock_section', true);
      $runtime = get_post_meta($post->ID, '_movieblock_runtime', true);
      if (!$runtime) {
        $shorts = get_posts(array(
          'post_type' => 'movie',
          'meta_query' => array(array('key' => '_movie_movieblock', 'value' => $post->ID)),
          'posts_per_page' => -1,
          'fields' => 'ids',
        ));
        $total = 0;
        foreach ($shorts as $shortId) {
          $total += (int) get_post_meta($shortId, '_movie_runtime', true);
        }
        if ($total > 0) {
          $runtime = $total;
        }
      }
      $info = $runtime ? $runtime . ' min.' : '';
      $screeningsValue = get_post_meta($post->ID, '_movieblock_screenings', true);
    }

    $screenings = parseScreenings($screeningsValue);

    $result = array(
      "id" => $post->ID,
      "slug" => get_post_field('post_name', $post->ID),
      "title" => get_the_title($post->ID),
      "section" => $section,
      "permalink" => get_post_permalink($post->ID),
      "thumbnail" => get_the_post_thumbnail($post->ID, get_template() . '-movie-post-thumbnail'),
      "heroThumbnail" => get_the_post_thumbnail($post->ID, 'large'),
      "screenings" => $screenings,
      "info" => $info,
      "isBlock" => !$isMovie,
    );

    $streamingLinkKey = $isMovie ? '_movie_streamingLink' : '_movieblock_streamingLink';
    $streamingLink = get_post_meta($post->ID, $streamingLinkKey, true);
    if ($streamingLink) $result['streamingLink'] = $streamingLink;

    if ($isMovie) {
      $directors = get_post_meta($post->ID, '_movie_directors', true);
      $cast = get_post_meta($post->ID, '_movie_cast', true);
      $synopsis = get_post_meta($post->ID, '_movie_synopsis', true);
      $trailerUrl = get_post_meta($post->ID, '_movie_trailer', true);

      if ($directors) $result['directors'] = $directors;
      if ($cast) $result['cast'] = $cast;
      if ($synopsis) $result['synopsis'] = $synopsis;
      if ($trailerUrl) $result['trailerUrl'] = $trailerUrl;
    } else {
      $result['shorts'] = getShortsForBlock($post->ID);
    }

    return $result;
  }

  // @src http://stackoverflow.com/questions/2762061/how-to-add-http-if-its-not-exists-in-the-url
  function addHttp($url) {
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = "http://" . $url;
    }
    return $url;
  }
?>
