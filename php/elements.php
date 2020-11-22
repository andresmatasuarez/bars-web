<?php

  require_once 'helpers.php';

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

  function renderScreenings($screeningsValue, $venues) {
    $screenings = parseScreenings($screeningsValue);
    $groupedScreenings = groupScreeningsByVenue($screenings);

    $screeningVenuesCount = count($groupedScreenings);
    if ($screeningVenuesCount == 1) {
      foreach($groupedScreenings as $venue => $screenings){
        foreach($screenings as $key => $screening) {
          renderScreening($screening['date'], @$screening['time'], @$screening['room']);
        }
      }
    } else {
      foreach($groupedScreenings as $venue => $screenings){
        echo '<div>';
          echo '<div class="screenings-caption">' . $venues[$venue]['name'] . '</div>';
          echo '<div>';
            foreach($screenings as $key => $screening) {
              renderScreening($screening['date'], @$screening['time'], @$screening['room']);
            }
          echo '</div>';
        echo '</div>';
        echo '<div class="clear" />';
      }
    }
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

  function renderRegularMovieScreening($title, $sectionValue, $permalink, $postThumbnail, $info, $venue, $time) {
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

  function renderScheduleDay($day, $dayDisplay, $isEven, $posts, $venues) {
?>
    <div class="schedule-day <?php echo $isEven ? 'even' : 'odd'; ?>">
      <div class="schedule-day-info">
        <?php echo $dayDisplay; ?>
      </div>
      <div class="movie-posts">
      <?php
        foreach($posts as $post){
          setup_postdata($post); // post object only has post ID.

          $movieToDisplay = prepareMovieForDisplaying($post);

          foreach($movieToDisplay['screenings'] as $key => $screening){
            if ($screening['date'] !== $day){
              continue;
            }

            $venue = $screening['venue'];
            $venue = $venue === '' ? $venue : $venues[$venue]['name'];

            if (!isset($screening['streaming'])) {
              // If 'room' was entered and there is only one venue for this edition,
              // then display only the room.
              $room = $screening['room'];
              $venue = $room !== '' && count($venues) === 1 ? $room : $venue;
            }

            renderRegularMovieScreening(
              $movieToDisplay['title'],
              $movieToDisplay['section'],
              $movieToDisplay['permalink'],
              $movieToDisplay['thumbnail'],
              $movieToDisplay['info'],
              $venue,
              isset($screening['time']) ? $screening['time'] : NULL
            );
          }
        }

        // Restore global post data stomped by the_post().
        wp_reset_query();
      ?>
      </div>
    </div>
<?php
  }

?>
