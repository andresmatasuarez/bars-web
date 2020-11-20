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

  function groupScreeningsByVenue($screenings) {
    $grouped = array();
    foreach($screenings as $key => $screening){
      $parsed = parseScreening($screening);
      $venue = $parsed['venue'];

      if (isset($grouped[$venue])) {
        $grouped[$venue][] = $parsed;
      } else {
        $grouped[$venue] = array();
        $grouped[$venue][] = $parsed;
      }
    }
    return $grouped;
  }

  function renderSelector($id, $title, $options, $customClass = '') {
?>
    <div class="bars-selector-wrapper <?php echo $customClass; ?>">
      <div class="bars-selector-title">
        <?php echo $title; ?>
      </div>
      <div class="bars-selector">
        <select id="<?php echo $id; ?>">
          <?php
            foreach($options as $value => $label) {
              echo '<option value="' . $value . '">' . $label . '</option>';
            }
          ?>
        </select>
      </div>
    </div>
<?php
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

  function renderRegularMovieScreening($title, $sectionValue, $permalink, $postThumbnail, $venue, $time, $info) {
?>
    <div class="movie-post" section="<?php echo $sectionValue; ?>">
<?php
      if (!is_null($time)) {
        echo '<div class="movie-post-hour">' . $time . '</div>';
      }
?>
      <a href="#movie-container" link="<?php echo $permalink; ?>">
        <div class="movie-post-thumbnail">
<?php
        if ($venue != '') {
          echo '<div class="movie-post-venue">' . ucwords($venue) . '</div>';
        }
?>
          <div class="movie-post-section"><?php echo sectionByValue($sectionValue); ?></div>
          <?php echo $postThumbnail; ?>
        </div>
        <div class="movie-post-title-container">
          <div class="movie-post-title">
            <span class="movie-post-title-text">
              <?php echo $title; ?>
            </span>
          </div>
          <div class="movie-post-info"><?php echo $info; ?></div>
        </div>
      </a>
    </div>
<?php
  }

  // @src http://stackoverflow.com/questions/2762061/how-to-add-http-if-its-not-exists-in-the-url
  function addHttp($url) {
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = "http://" . $url;
    }
    return $url;
  }
?>
