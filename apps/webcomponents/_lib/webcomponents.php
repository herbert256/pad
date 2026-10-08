<?php

  // The helpers of the application.
  //
  // wcData     a file of _data/ as an array
  // wcGroups   the groups of _data/examples.json, each with its examples - the cards
  // wcExample  the entry of one example, with the ones before and after it

  function wcData ( $name ) {

    static $files = [];

    return $files [$name] ??= json_decode ( file_get_contents ( APP . "_data/$name.json" ), TRUE ) ?? [];

  }

  function wcGroups () {

    $catalog = wcData ( 'examples' );
    $groups  = [];

    foreach ( $catalog ['groups'] as $group ) {
      $group ['examples'] = array_values ( array_filter ( $catalog ['examples'], fn ( $one ) => $one ['group'] == $group ['key'] ) );
      $group ['count']    = count ( $group ['examples'] );
      $groups []          = $group;
    }

    return $groups;

  }

  function wcExample ( $page ) {

    $catalog = wcData ( 'examples' );
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

?>
