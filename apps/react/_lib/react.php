<?php

  // The helpers of the application.
  //
  // reactData      a file of _data/ as an array - products, team, sales, examples
  // reactGroups    the groups of _data/examples.json, each with its examples: the cards of
  //                the home page and of the patterns page
  // reactExample   the entry of one example with the examples before and after it in the
  //                list - what examples/_inits.pad and _exits.pad put round the page
  // reactCart      the cart a session holds - product id => quantity - as the islands show
  //                it: lines with name, price and quantity, the count and the total

  function reactData ( $name ) {

    static $files = [];

    return $files [$name] ??= json_decode ( file_get_contents ( APP . "_data/$name.json" ), TRUE ) ?? [];

  }

  function reactGroups () {

    $catalog = reactData ( 'examples' );
    $groups  = [];

    foreach ( $catalog ['groups'] as $group ) {
      $group ['examples'] = array_values ( array_filter ( $catalog ['examples'], fn ( $one ) => $one ['group'] == $group ['key'] ) );
      $group ['count']    = count ( $group ['examples'] );
      $groups []          = $group;
    }

    return $groups;

  }

  function reactExample ( $page ) {

    $catalog  = reactData ( 'examples' );
    $list     = $catalog ['examples'];
    $groups   = array_column ( $catalog ['groups'], 'title', 'key' );
    $at       = array_search ( $page, array_column ( $list, 'page' ) );
    $none     = [ 'page' => '', 'title' => '' ];

    if ( $at === FALSE )
      return [ 'page' => $page, 'title' => ucfirst ( basename ( $page ) ), 'icon' => '⚛️', 'lede' => '',
               'server' => [], 'client' => [], 'files' => [], 'groupTitle' => 'Patterns',
               'prev' => $none, 'next' => $none ];

    $example                = $list [$at] + [ 'files' => [] ];
    $example ['groupTitle'] = $groups [ $example ['group'] ] ?? '';
    $example ['prev']       = $list [ $at - 1 ] ?? $none;
    $example ['next']       = $list [ $at + 1 ] ?? $none;

    return $example;

  }

  function reactCart ( $counts ) {

    $products = array_column ( reactData ( 'products' ), NULL, 'id' );
    $lines    = [];

    foreach ( $counts as $id => $quantity )
      if ( isset ( $products [$id] ) )
        $lines [] = [ 'id'       => $products [$id] ['id'],
                      'name'     => $products [$id] ['name'],
                      'emoji'    => $products [$id] ['emoji'],
                      'price'    => $products [$id] ['price'],
                      'quantity' => $quantity,
                      'amount'   => round ( $products [$id] ['price'] * $quantity, 2 ) ];

    return [ 'lines' => $lines,
             'count' => array_sum ( array_column ( $lines, 'quantity' ) ),
             'total' => round ( array_sum ( array_column ( $lines, 'amount' ) ), 2 ) ];

  }

?>
