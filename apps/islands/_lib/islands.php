<?php

  // The helpers of the application.
  //
  // islandsData     a file of _data/ as an array
  // islandsGroups    the groups of _data/examples.json, each with its examples - the cards
  // islandsExample   the entry of one example, with the ones before and after it
  // islandsCart      the cart a session holds - product id => quantity - with names and total

  function islandsData ( $name ) {

    static $files = [];

    return $files [$name] ??= json_decode ( file_get_contents ( APP . "_data/$name.json" ), TRUE ) ?? [];

  }

  function islandsGroups () {

    $catalog = islandsData ( 'examples' );
    $groups  = [];

    foreach ( $catalog ['groups'] as $group ) {
      $group ['examples'] = array_values ( array_filter ( $catalog ['examples'], fn ( $one ) => $one ['group'] == $group ['key'] ) );
      $group ['count']    = count ( $group ['examples'] );
      $groups []          = $group;
    }

    return $groups;

  }

  function islandsExample ( $page ) {

    $catalog = islandsData ( 'examples' );
    $list    = $catalog ['examples'];
    $groups  = array_column ( $catalog ['groups'], 'title', 'key' );
    $at      = array_search ( $page, array_column ( $list, 'page' ) );
    $none    = [ 'page' => '', 'title' => '' ];

    if ( $at === FALSE )
      return [ 'page' => $page, 'title' => ucfirst ( basename ( $page ) ), 'icon' => '', 'lede' => '',
               'server' => [], 'client' => [], 'files' => [], 'groupTitle' => 'Examples',
               'prev' => $none, 'next' => $none ];

    $example                = $list [$at] + [ 'files' => [] ];
    $example ['groupTitle'] = $groups [ $example ['group'] ] ?? '';
    $example ['prev']       = $list [ $at - 1 ] ?? $none;
    $example ['next']       = $list [ $at + 1 ] ?? $none;

    return $example;

  }

  function islandsCart ( $counts ) {

    $products = array_column ( islandsData ( 'products' ), NULL, 'id' );
    $lines    = [];

    foreach ( $counts as $id => $quantity )
      if ( isset ( $products [$id] ) )
        $lines [] = [ 'id' => $id, 'name' => $products [$id] ['name'], 'emoji' => $products [$id] ['emoji'],
                      'quantity' => $quantity, 'sum' => $products [$id] ['price'] * $quantity,
                      'amount' => number_format ( $products [$id] ['price'] * $quantity, 2 ) ];

    return [ 'lines' => $lines,
             'count' => array_sum ( array_column ( $lines, 'quantity' ) ),
             'total' => number_format ( array_sum ( array_column ( $lines, 'sum' ) ), 2 ) ];

  }

  function islandsWeight ( $sources ) {

    $manifest = padViteManifest ();
    $build    = dirname ( APPS ) . '/www/' . $GLOBALS ['padApp'] . '/' . trim ( $GLOBALS ['padViteBuild'], '/' ) . '/';
    $files    = [];

    if ( ! $manifest )
      return 0;

    foreach ( $sources as $source ) {

      if ( ! isset ( $manifest [$source] ) )
        continue;

      $files [] = $manifest [$source] ['file'];

      [ $css, $chunks ] = padViteChunk ( $manifest, $source );

      foreach ( array_merge ( $css, $chunks ) as $file )
        $files [] = $file;

    }

    $bytes = 0;

    foreach ( array_unique ( $files ) as $file )
      $bytes += is_file ( $build . $file ) ? filesize ( $build . $file ) : 0;

    return $bytes;

  }

?>
