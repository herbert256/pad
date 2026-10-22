<?php

  // Every page: the menu of the frame - the overview, the model, then the examples by group -
  // and, on an example, its place in the catalog. The declarations of _lib/select.php are
  // kept as they stand before the page's PHP adds its own; _exits.php, which runs after it,
  // works out what the cells of an example show.
  //
  // The overview draws no example through {page}, so this runs once a request.

  $psSelect    = $padSelect;
  $psRelations = $padRelations;
  $psCatalog   = psCatalog ();
  $psFlat      = [];
  $isExample   = FALSE;

  $frameMenu = [ [ 'link' => '?index', 'label' => 'Overview',  'icon' => 'home'     ],
                 [ 'link' => '?model', 'label' => 'The model', 'icon' => 'database' ] ];

  foreach ( psGroups () as $group => $tagline ) {

    $frameMenu [] = [ 'head' => $group ];

    foreach ( $psCatalog [$group] as $page => [ $label, $about ] ) {

      $frameMenu [] = [ 'link' => "?$page", 'label' => $label, 'icon' => '' ];
      $psFlat    [] = [ $page, $label ];

      if ( $page == $padPage ) {
        $isExample    = TRUE;
        $exampleGroup = $group;
        $exampleTitle = $label;
        $exampleText  = $about;
      }

    }

  }

  $title = $isExample ? $exampleTitle : ( $padPage == 'model' ? 'The model' : 'PAD Select' );

  if ( $isExample ) {

    $here = array_search ( $padPage, array_column ( $psFlat, 0 ) );
    $all  = count ( $psFlat );

    [ $prevPage, $prevTitle ] = $psFlat [ ( $here + $all - 1 ) % $all ];
    [ $nextPage, $nextTitle ] = $psFlat [ ( $here + 1 ) % $all ];

  }

?>
