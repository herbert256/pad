<?php

  // The helpers of the application.
  //
  // alpineData     a file of _data/ as an array
  // alpineGroups   the groups of _data/examples.json, each with its examples - the cards
  // alpineExample  the entry of one example, with the ones before and after it
  // alpineText     a request value that is one text - a list sent for it is the default

  // A request value that is one text: its text, or the default when it came as a list - a
  // pasted link or a crawler's ?q[]=x - where the (string) cast warned "Array to string
  // conversion", an error under PAD, and the page answered 500.

  function alpineText ( $name, $default = '' ) {

    $value = padRequest ( $name, $default );

    return is_scalar ( $value ) ? (string) $value : $default;

  }

  function alpineData ( $name ) {

    static $files = [];

    return $files [$name] ??= json_decode ( file_get_contents ( APP . "_data/$name.json" ), TRUE ) ?? [];

  }

  function alpineGroups () {

    $catalog = alpineData ( 'examples' );
    $groups  = [];

    foreach ( $catalog ['groups'] as $group ) {
      $group ['examples'] = array_values ( array_filter ( $catalog ['examples'], fn ( $one ) => $one ['group'] == $group ['key'] ) );
      $group ['count']    = count ( $group ['examples'] );
      $groups []          = $group;
    }

    return $groups;

  }

  function alpineExample ( $page ) {

    $catalog = alpineData ( 'examples' );
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
