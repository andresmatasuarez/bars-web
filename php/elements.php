<?php

  function renderScreening($date, $time, $room = NULL) {
    echo '<div class="screening">';
    if ($date === 'full') {
      echo '<div class="screening-daynumber">';
        echo 'Mirala';
      echo '</div>';
      echo '<div class="screening-daynumber">';
        echo 'CUALQUIER';
      echo '</div>';
      echo '<div class="screening-daynumber">';
        echo 'DÍA';
      echo '</div>';
    } else {
      $dayName = ucwords(getSpanishDayName(DateTime::createFromFormat('m-d-Y', $date)->format('l')));
      $dayNumber = DateTime::createFromFormat('m-d-Y', $date)->format('d');
      echo '<div class="screening-dayname">';
        echo $dayName;
      echo '</div>';
      echo '<div class="screening-daynumber">';
        echo $dayNumber;
      echo '</div>';
    }

    if (isset($time)) {
      echo '<div class="screening-hour">';
        echo $time;
      echo '</div>';
    }

      if (isset($room)) {
        echo '<div>';
          echo '<strong>' . strtoupper($room) . '</strong>';
        echo '</div>';
      }
    echo '</div>';
  }

?>
