<?php

  function padOpenCloseOk ( $string, $check) {

    if ( strpos ( $string, $check ) === FALSE )
      return FALSE;

    list ( $dummy, $string ) = explode ( $check, '.' . $string . '.', 2 );

    $tags = padOpenCloseList ( $string );

    return padOpenCloseCount ( $string, $tags);

  }

  function padOpenCloseList ( $string ) {

    $tags = [];

    $p1 = strpos($string, '{/', 0);

    while ($p1 !== FALSE) {

      $p2 = strpos($string, '}', $p1);

      if ( $p2 !== FALSE ) {

        $p3 = strpos($string, ' ', $p1);
        if ($p3 !== FALSE and $p3 < $p2 )
          $p2 = $p3;

        $tag = substr($string, $p1+2, $p2-$p1-2);
        if ( padValidTag ($tag) )
          $tags [$tag] = TRUE;

      }

      $p1 = strpos($string, '{/', $p1+1);

    }

    return $tags;

  }

  function padOpenCloseCount ( $string, $tags ) {

   foreach ( $tags as $tag => $dummy )
      if ( ! padOpenCloseCountOne ( $string, $tag ) )
        return FALSE;

    return TRUE;

  }

  function padOpenCloseCountOne ( $string, $tag ) {

    if ( ( substr_count($string, '{'.$tag.' ' ) + substr_count($string, '{'.$tag.'}' ) )
           <>
         ( substr_count($string, '{/'.$tag.' ') + substr_count($string, '{/'.$tag.'}') ) )
      return FALSE;

    return TRUE;

  }

  function padCheckTag ($tag, $string) {

    return ( substr_count($string, "{".$tag.' ') == substr_count($string, "{/" . $tag.'}') ) ;

  }

  function padSplit ( $needle, $haystack, &$before, &$after ) {

    $array = explode ( $needle, $haystack, 2 );

    $before = trim ( $array [0] ?? '' );
    $after  = trim ( $array [1] ?? '' );

  }

  function padBetween ( $string, $open, $close, &$before, &$between, &$after ) {

    $before = $between = $after = '';

    $p1 = strpos ( $string, $open );
    if ( $p1 === FALSE ) return FALSE;

    $start = $p1 + strlen($open);
    $p2 = strpos ( $string, $close, $start );
    if ( $p2 === FALSE ) return FALSE;

    if ( $p1 > 0 )
      $before = substr ( $string, 0, $p1 );

    $between = substr ( $string, $start, $p2 - $start );

    $afterPos = $p2 + strlen($close);
    if ( $afterPos < strlen ( $string ) )
      $after = substr ( $string, $afterPos );

    return TRUE;

  }

  function padExplode ( $haystack, $limit, $number=0 ) {

    if ($number)
      $explode = explode ( $limit, $haystack, $number );
    else
      $explode = explode ( $limit, $haystack );

    foreach ($explode as $key => $value ) {

      $explode [$key] = trim($value);

      if ( $limit == '|' ) $explode [$key] = str_replace ( '&pipe;',  '|', $explode [$key] );
      if ( $limit == '=' ) $explode [$key] = str_replace ( '&eq;',    '=', $explode [$key] );
      if ( $limit == ',' ) $explode [$key] = str_replace ( '&comma;', ',', $explode [$key] );

      if ( $explode [$key] === '' )
        unset ( $explode [$key] );

    }

    return array_values ( $explode );

  }

  function padMakeSafe ( $input, $len=2048 ) {

    if ( is_array($input) or is_object($input) )
      $input = padJson ($input);

    $input = preg_replace('/[\x00-\x1F\x7F-\xFF]/', ' ', $input);
    $input = preg_replace('/\s+/', ' ', $input);

    if ( strlen($input) > $len )
      $input = substr ( $input, 0, $len );

    $input = trim($input);

    return $input;

  }

  function padGetRange ( $input, $increment=1 ) {

    $parts = padExplode ($input, '..');

    $p1 = $parts[0] ?? '';
    $p2 = $parts[1] ?? '';

    if ( $p2 )
      return range ( $p1, $p2, $increment );
    elseif ( $p1 )
      return range ( 1, $p1, $increment );
    else
      return range ( 1, 10, $increment );

  }

  function padGetList ( $list ) {

    $list = explode ( ';', $list );

    foreach ( $list as $key => $value)
      if ( is_numeric ($value) )
        $list [$key] = intval($value);

    return $list;

  }

?>