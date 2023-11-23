<?php
/*
 * Template Name: tmpl_juries
 *
 * @package WordPress
 * @subpackage bars2013
 */

  require_once 'helpers.php';
  require_once 'elements.php';
  require_once 'editions.php';

  get_header();

  $latestEdition = Editions::current();
  if (!isset($_GET['edition'])){
    $currentEdition = $latestEdition;
  } else {
    $currentEdition = Editions::getByNumber(htmlspecialchars($_GET['edition']));
  }

  $juries = Editions::getJuries($currentEdition);
?>

<script>
  window.LATEST_EDITION = <?php echo $latestEdition['number'] ?>;
  window.CURRENT_EDITION = <?php echo $currentEdition['number'] ?>;
</script>

        <div id="page-juries" class="page">
          <div class="page-header">

            <div class="page-title">
              <div class="title-text">
                Premios y Jurados <?php echo Editions::getTitle($currentEdition); ?>
              </div>
              <?php
                renderSelector('edition-selector', 'Ediciones anteriores', Editions::getMapOfTitleByNumber());
              ?>
            </div>
          </div>

          <div class="scratch"></div>

          <?php
            $noAwardsDefined = !isset($currentEdition['awards']) || empty($currentEdition['awards']);
            if ($noAwardsDefined) {
              if ($currentEdition === Editions::current()) {
                renderWarningMessage(
                  'Los premios y categorías<br />todavía se están definiendo',
                  'Estate atento!<br />
                  Estarán disponibles en los próximos días. <br />'
                );
              } else {
                $linkToContact = get_permalink(get_page_by_path('contacto'));
                renderWarningMessage(
                  'Los premios y categorías para esta <br /> edición del festival no están disponibles',
                  "Volvé a intentar más tarde o <br /> comunicate con nosotros usando el <a href=\"{$linkToContact}\">formulario de contacto</a>."
                );
              }
            } else {
          ?>
              <div class="content text-opensans indented">
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
                    $awards_jury     = $currentEdition['awards']['jury'];
                    $awards_audience = $currentEdition['awards']['audience'];
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

                <?php
                  if (empty($juries)){
                    if ($currentEdition === Editions::current()) {
                      renderWarningMessage(
                        'Los jurados todavía no han sido seleccionados',
                        'Estate atento!<br />
                        Estarán disponibles en los próximos días. <br />'
                      );
                    } else {
                      $linkToContact = get_permalink(get_page_by_path('contacto'));
                      renderWarningMessage(
                        'Los jurados para esta <br /> edición del festival no están disponibles',
                        "Volvé a intentar más tarde o <br /> comunicate con nosotros usando el <a href=\"{$linkToContact}\">formulario de contacto</a>."
                      );
                    }
                  } else {
                ?>
                    <div>
                      <?php
                        foreach($juries as $sectionId => $juriesForSection){
                          echo '<div class="page-header juries section">';
                          echo    '<span class="fa-solid fa-film"></span>';
                          echo    getJurySectionLabel($sectionId);
                          echo    '<div class="scratch"></div>';
                          echo '</div>';

                          echo '<div class="juries">';
                          if (empty($juriesForSection)) {
                            echo '<div class="no-juries-yet">Los jurados para esta sección todavía no han sido seleccionados</div>';
                          } else {
                            foreach($juriesForSection as $index => $jury){
                              renderJury($jury);
                            }
                          }
                          echo '</div>';
                        }
                      ?>
                    </div>
                <?php
                  }
                ?>
              </div>

          <?php
            }
          ?>


  <?php
    get_sidebar();
    get_footer();
  ?>
