<?php

  // The helpers of the application.
  //
  // htmxData      a file of _data/ as an array
  // htmxGroups    the groups of _data/examples.json, each with its examples - the cards
  // htmxExample   the entry of one example, with the ones before and after it
  // htmxCart      the cart a session holds - product id => quantity - with names and total

  function htmxData ( $name ) {

    static $files = [];

    return $files [$name] ??= json_decode ( file_get_contents ( APP . "_data/$name.json" ), TRUE ) ?? [];

  }

  function htmxGroups () {

    $catalog = htmxData ( 'examples' );
    $groups  = [];

    foreach ( $catalog ['groups'] as $group ) {
      $group ['examples'] = array_values ( array_filter ( $catalog ['examples'], fn ( $one ) => $one ['group'] == $group ['key'] ) );
      $group ['count']    = count ( $group ['examples'] );
      $groups []          = $group;
    }

    return $groups;

  }

  function htmxExample ( $page ) {

    $catalog = htmxData ( 'examples' );
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

  function htmxCart ( $counts ) {

    $products = array_column ( htmxData ( 'products' ), NULL, 'id' );
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

?>
