<?php

  function parts ( ) {

    global $for, $item, $manual, $padPage, $parts, $xitem, $xref;

    $parts = [];
    return $parts;

    if ( ! str_starts_with ( $padPage, 'index') and
         ! str_starts_with ( $padPage, 'manual/') and
         ! str_starts_with ( $padPage, 'Xref/') and
         ! str_starts_with ( $padPage, 'develop/') ) {

      $parts ['dev'] ['part'] = $padPage;
      $parts ['dev'] ['link'] = '';

      return $parts;

    }

    if ( $padPage == 'manual/index' and $manual ) {

      $parts ['now'] ['part'] = $manual;
      $parts ['now'] ['link'] = '';

    }

    if ( str_starts_with ( $padPage, 'develop/') and $padPage <> 'develop/index') {

      if ( str_starts_with ( $padPage, 'develop/show') ) {
        $parts ['now'] ['part'] = $item;
        $parts ['now'] ['link'] = '';
      } else {
        $parts ['dev'] ['part'] = $padPage;
        $parts ['dev'] ['link'] = '';
      }

    }

    if ( $padPage == 'Xref/xref' or $padPage == 'Xref/go' ) {

      $parts ['f'] ['part'] = strtolower ( $for );
      $parts ['f'] ['link'] = '';

      $parts ['i'] ['part'] = $xitem;
      $parts ['i'] ['link'] = '';

    }

    if ( $padPage == 'develop/xref' ) {

      if ( $xref ) {

        $plode = padExplode ( $xref, '/' );
        $key   = array_key_last ($plode);
        $last  = $plode [$key];

        unset ( $plode [$key] );

        $xref = '';

        foreach ( $plode as $key => $value ) {
          $xref .= "/$value";
          $parts ["x$key"] ['part'] = $value;
          $parts ["x$key"] ['link'] = "develop/xref&xref=$xref";
        }

      }

    }

    return $parts;

  }

  function forLink () {

    global $first, $for, $xitem;

    return 'Xref/xref'
    . '&first='  . ($first )
    . '&for='    . urlencode(($for ))
    . '&xitem='  . ($xitem );

  }

  function itemLink () {

    global $second;

    if ( ! $second )
      return '';

    return forLink ();

  }

  function secondLink () {

    global $go, $second;

    if ( ! isset ($go ) )
      return '';

    return forLink () . '&second='  . $second ;

  }

?>