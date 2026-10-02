<?php

  // String handling for template text: telling whether a stretch of source is balanced,
  // and the general splitting helpers the whole engine uses.
  //
  // The balance group answers "may I cut here without breaking a tag pair". padOpenCloseList
  // collects every tag that has a closing {/tag} in the string, padOpenCloseCountOne checks
  // that one tag opens as often as it closes (padOpenCount counts the openers, leaving out a
  // self-closing {tag .../}), padOpenCloseCount does that for a whole list,
  // padOpenCloseOk combines them for the text following a marker, and padCheckTag is a
  // single-tag shorthand. lib/content.php uses these to place @content@ and @else@ at the
  // right nesting depth.
  //
  // padSplit    splits once on a needle, trimming both halves
  // padBetween  extracts before/between/after around an open and close delimiter
  // padExplode  the engine's standard explode: trims, drops empty parts, reindexes, and
  //             restores the &pipe; &eq; &comma; escapes when splitting on | = ,
  // padMakeSafe flattens any value to a single-line, control-character-free, length-capped
  //             string, for log lines and error messages
  // padGetRange turns "1..10" (or "10", or nothing) into a PHP range
  // padGetList  splits a semicolon list, converting numeric entries to int

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

    if ( padOpenCount ( $string, $tag )
           !=
         ( substr_count($string, '{/'.$tag.' ') + substr_count($string, '{/'.$tag.'}') ) )
      return FALSE;

    return TRUE;

  }

  // The openers of one tag in a string: {tag} and {tag ...}, but not a self-closing
  // {tag .../}, which opens nothing - counting it threw the balance of an enclosing pair of
  // the same name off by one, and that pair's {/tag} was left an orphan. The end of an
  // opener is found by brace depth, so a parameter holding a tag of its own, {tag x={$y}/},
  // is read to its real end; like level/tag.php, the slash must stand right before the }.

  function padOpenCount ( $string, $tag ) {

    $open   = '{' . $tag;
    $count  = 0;
    $offset = 0;

    while ( ( $pos = strpos ( $string, $open, $offset ) ) !== FALSE ) {

      $offset = $pos + strlen ( $open );
      $next   = $string [$offset] ?? '';

      if ( $next == '}' or ( $next == ' ' and ! padOpenSelfClosing ( $string, $offset ) ) )
        $count++;

    }

    return $count;

  }

  function padOpenSelfClosing ( $string, $offset ) {

    $depth = 1;
    $end   = strlen ( $string );

    for ( $i = $offset; $i < $end; $i++ )
      if ( $string [$i] == '{' )
        $depth++;
      elseif ( $string [$i] == '}' and ! --$depth )
        return ( $string [$i-1] == '/' );

    return FALSE;

  }

  // A closer may carry a pipe, {/if | upper}, so {/tag followed by a space closes too -
  // counting only {/tag} left such a nested pair looking open, and the enclosing tag's
  // {else} or {when} was skipped as if it belonged to the inner one.

  function padCheckTag ($tag, $string) {

    return ( substr_count($string, "{".$tag.' ') == substr_count($string, "{/" . $tag.'}')
                                                  + substr_count($string, "{/" . $tag.' ') ) ;

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

    global $padCheckSyntax;

    // '1..' quietly became the single row 1, and '..5' read as 5 - neither is what the
    // author meant by writing the dots. Strict mode asks for the missing end.

    if ( $padCheckSyntax and str_contains ( (string) $input, '..' ) ) {

      if ( str_ends_with ( trim ( $input ), '..' ) )
        padError ( "the range '" . trim ( $input ) . "' has no end" );

      if ( str_starts_with ( trim ( $input ), '..' ) )
        padError ( "the range '" . trim ( $input ) . "' has no start" );

    }

    $parts = padExplode ($input, '..');

    $p1 = $parts[0] ?? '';
    $p2 = $parts[1] ?? '';

    // An end of 0 is an end: tested for truth, '5..0' lost it and became 1..5, and '0..0'
    // the default 1..10.

    if     ( $p2 !== '' ) { }
    elseif ( $p1 )        { $p2 = $p1; $p1 = 1;  }
    else                  { $p1 = 1;   $p2 = 10; }

    // range() counts in steps, so a step of zero or no step at all never arrives anywhere and
    // is an error rather than an empty answer; a step given the wrong way round is the same
    // walk in reverse, which range() works out from the two ends by itself.

    if ( ! is_numeric ( $increment ) or ! (int) $increment )
      $increment = 1;

    // '1..z' asks for a range between a number and a letter, which has no meaning; both ends
    // have to be of one kind, and if they are not there are no values to answer with.

    if ( is_numeric ( $p1 ) != is_numeric ( $p2 ) )
      return [];

    $increment = abs ( $increment );

    // A step wider than the two ends are apart cannot be taken even once, which range() calls
    // an error; the whole distance is the widest step that means anything here. Letters are
    // measured the way range() walks them, by character code.

    $span = is_numeric ( $p1 ) ? abs ( $p2 - $p1 ) : abs ( ord ( $p2 ) - ord ( $p1 ) );

    if ( $span and $increment > $span )
      $increment = $span;

    return range ( $p1, $p2, $increment );

  }

  function padGetList ( $list ) {

    $list = explode ( ';', $list );

    foreach ( $list as $key => $value)
      if ( is_numeric ($value) )
        $list [$key] = intval($value);

    return $list;

  }

?>