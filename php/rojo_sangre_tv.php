<?php
/*
 * Template Name: tmpl_rojosangretv
 *
 * @package WordPress
 * @subpackage bars2013
 */

  require_once 'editions.php';

  get_header();

  $edition = Editions::current();

  $chapterIds = [
    'U2S3LVH57zo', // programa 1
    'd58qkkjxXC8', // programa 2
    'DLaJ0JvUq8c', // programa 3
    'JFsCyfADn2Q', // programa 4
    'HFI3KDezfbU', // programa 5
    'f9oEjfTDdLw', // programa 6
    'c54LZ5NP3xc'  // programa 7
  ];

?>
        <div id="page-juries" class="page">
          <div class="page-header">
            #RojoSangreTV
          </div>

          <div class="scratch"></div>

          <div class="content text-opensans indented" >

            <p>
              Rojo Sangre TV salió al aire los viernes a la medianoche de septiembre a noviembre
              de 2018, a través de Comarca SI, canal 32.3, de TDA. Los mejores cortos del
              <strong>Festival Buenos Aires Rojo Sangre</strong> en la televisión. Con la conducción
              estelar de Ariel Toronja y gran elenco!
            </p>

            <div>
              <?php
                for($i = 0; $i < count($chapterIds); $i++){
                  echo '<iframe width="560" height="315" src="https://www.youtube.com/embed/' . $chapterIds[$i] . '" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe><br />';
                }
              ?>
            </div>

          </div>


  <?php
    get_sidebar();
    get_footer();
  ?>
