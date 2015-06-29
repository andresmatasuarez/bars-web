<?php
/*
 * Template Name: tmpl_juries
 *
 * @package WordPress
 * @subpackage bars2013
 */

  require_once 'editions.php';

  get_header();

  $edition = Editions::current();

?>
        <div id="page-juries" class="page">
          <div class="page-header">
            Premios y Jurados <?php echo $edition['title']; ?>
          </div>

          <div class="scratch"></div>

          <div class="content text-opensans indented" >

            <p>
              Los jurados tienen la ardua tarea de evaluar las películas presentadas y decidir quién se lleva
              los premios. Se eligieron figuras destacadas que tuvieran cierta predisposición hacia el género
              o vinculación con el ámbito cinematográfico.
            </p>

            <p>
              Se entregarán los siguientes premios:
            </p>

            <table class="awards">
              <tr>
                <th>Por elección del jurado</th>
                <th>Por votación del público</th>
              </tr>
              <tr>
                <td colspan="2"><div class="scratch"></div></td>
              </tr>
              <?php
                $awards_jury     = $edition['awards']['jury'];
                $awards_audience = $edition['awards']['audience'];
                for($i = 0; $i < count($awards_jury) || $i < count($awards_audience); $i++ ){
                  echo '<tr>';
                  echo $i < count($awards_jury) ? '<td>' . $awards_jury[$i] . '</td>' : '<td></td>';
                  echo $i < count($awards_audience) ? '<td>' . $awards_audience[$i] . '</td>' : '<td></td>';
                  echo '</tr>';
                }
              ?>
            </table>

          </div>

          <div>
            <div class="page-header juries">
              Jurados
            </div>

            <div class="scratch"></div>

            <div>

              <?php

                if (!isset($edition['juries']) || empty($edition['juries'])){

                  echo '<div class="content text-opensans indented"><p>Los jurados no han sido seleccionados todavía.</p></div>';

                } else {

                  foreach($edition['juries'] as $section => $juries){

                    echo '<div class="page-header juries section">';
                    echo    '<span class="genericon genericon-video"></span>';
                    echo    $section;
                    echo    '<div class="scratch"></div>';
                    echo '</div>';

                    echo '<div class="juries">';
                    foreach($juries as $index => $jury){
                      echo '<div class="jury">';
                      echo    '<div class="jury-image focuspoint" data-focus-x="' . $jury['pic']['focus']['x'] . '" data-focus-y="' . $jury['pic']['focus']['y'] . '" data-image-w="' . $jury['pic']['focus']['w'] . '" data-image-h="' . $jury['pic']['focus']['h'] . '">';
                      echo      '<img src="'. get_bloginfo('template_directory') . '/' . $jury['pic']['url'] . '" />';
                      echo    '</div>';
                      echo    '<div class="jury-info">';
                      echo      '<div class="jury-name text-oswald">' . $jury['name'] . '</div>';
                      echo      '<p class="text-opensans indented">' . $jury['description'] . '</p>';
                      echo    '</div>';
                      echo '</div>';
                    }
                    echo '</div>';
                  }

                }
              ?>

            </div>

          </div>




  <?php
    get_sidebar();
    get_footer();
  ?>