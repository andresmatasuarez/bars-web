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

?>
