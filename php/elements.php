<?php

  function renderScreening($date, $time, $room = NULL) {
    $dayName = ucwords(getSpanishDayName(DateTime::createFromFormat('m-d-Y', $date)->format('l')));
    $dayNumber = DateTime::createFromFormat('m-d-Y', $date)->format('d');
    echo '<div class="screening">';
      echo '<div class="screening-dayname">';
        echo $dayName;
      echo '</div>';
      echo '<div class="screening-daynumber">';
        echo $dayNumber;
      echo '</div>';
      echo '<div class="screening-hour">';
        echo $time;
      echo '</div>';

      if (isset($room)) {
        echo '<div>';
          echo '<strong>' . strtoupper($room) . '</strong>';
        echo '</div>';
      }
    echo '</div>';
  }

?>
