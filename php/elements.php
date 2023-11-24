<?php

  require_once 'helpers.php';
  require_once 'editions.php';

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
        <span class="fa-solid fa-circle-exclamation fa-4x"></span>
      </div>
      <h1><?php echo $title; ?></h1>
      <p><?php echo $message; ?></p>
    </div>
<?php
  }

  function renderJury($jury) {
?>
    <div class="jury">
    <?php
      // Checks whether jury comes from DB or from editions file.
      if (isset($jury['postId'])) {
    ?>
        <div class="jury-image">
          <?php echo $jury['thumbnail']; ?>
        </div>
    <?php
      } else {
    ?>
        <div class="jury-image focuspoint" data-focus-x="<?php echo $jury['pic']['focus']['x']; ?>" data-focus-y="<?php echo $jury['pic']['focus']['y']; ?>" data-image-w="<?php echo $jury['pic']['focus']['w']; ?>" data-image-h="<?php echo $jury['pic']['focus']['h']; ?>">
          <img src="<?php echo get_bloginfo('template_directory') . '/' . $jury['pic']['url']; ?>" />
        </div>
    <?php
      }
    ?>

      <div class="jury-info">
        <div class="jury-name text-oswald"><?php echo $jury['name']; ?></div>
        <p class="text-opensans indented"><?php echo $jury['description']; ?></p>
      </div>
    </div>
<?php
  }
?>
