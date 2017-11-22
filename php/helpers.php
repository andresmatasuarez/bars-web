<?php

  function parseScreening($screening) {
    preg_match('/^(?:([A-Za-z]+)\s*:\s*)?(.*)\s+(.*)$/', $screening, $matches);
    return array(
      'venue' => strtolower($matches[1]),
      'date' => $matches[2],
      'time' => $matches[3]
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
