<?php

  function parseScreening($screening) {
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

?>
