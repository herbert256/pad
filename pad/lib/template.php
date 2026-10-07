<?php

  // String handling for template text: telling whether a stretch of source is balanced,
  // and the general splitting helpers the whole engine uses.
  //
  // The balance group answers "may I cut here without breaking a tag pair". padOpenCloseList
  // collects every tag that has a closing {/tag} in the string, padOpenCloseCountOne checks
  // that one tag opens as often as it closes (padOpenCount counts the openers, leaving out a
  // self-closing {tag .../}), padOpenCloseCount does that for a whole list,
  // padOpenCloseOk combines them for the text following a marker (padOpenClosePos says where
  // the level's own marker stands), and padCheckTag is a
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
  // padGetList  splits a semicolon list, numeric entries becoming the numbers they spell

  function padOpenCloseOk ( $string, $check) {

    return padOpenClosePos ( $string, $check ) !== FALSE;

  }

  // Where the marker of the level the string is the content of stands: the first one with
  // every tag pair after it balanced - a marker of a nested pair has that pair's closing
  // tag after it. Only the first marker was looked at, so a nested level's @end@ in front
  // of the level's own hid it - {items}@start@{subs}(@start@{$s}@end@){/subs}@end@{/items}
  // failed the strict check as an @start@ without its @end@ - and the split took place at
  // the nested marker.

  function padOpenClosePos ( $string, $check ) {

    $pos = strpos ( $string, $check );

    while ( $pos !== FALSE ) {

      $after = substr ( $string, $pos + strlen ( $check ) );

      if ( padOpenCloseCount ( $after, padOpenCloseList ( $after ) ) )
        return $pos;

      $pos = strpos ( $string, $check, $pos + 1 );

    }

    return FALSE;

  }

  // The @start@ and @end@ of a text that renders exactly once - a level no tag opened: the
  // page itself when its PHP answered no list, or a padCode pass. level/start.php cuts a
  // tag's level at its own markers into a prelude, a body walked per row and a coda; here
  // all three render once and in order, which is the text with its own markers taken out.
  // Only the markers that belong to this text are taken (padOpenClosePos), never a nested
  // tag's, and strict mode holds them to the order level/start.php does.

  function padSectionsOnce ( $text ) {

    $start = padOpenClosePos ( $text, '@start@' );
    $end   = padOpenClosePos ( $text, '@end@'   );

    if ( $start === FALSE and $end === FALSE )
      return $text;

    if ( $GLOBALS ['padCheckSyntax'] ) {

      if ( $start !== FALSE and $end === FALSE )
        padError ( "an @start@ needs its @end@ behind it" );

      if ( $end !== FALSE and $start === FALSE )
        padError ( "an @end@ needs its @start@ before it" );

      if ( $start !== FALSE and $end !== FALSE and $end < $start )
        padError ( "the @start@ stands before the @end@, not behind it" );

    }

    $cuts = [];

    if ( $start !== FALSE ) $cuts [$start] = 7;
    if ( $end   !== FALSE ) $cuts [$end]   = 5;

    krsort ( $cuts );

    foreach ( $cuts as $pos => $len )
      $text = substr_replace ( $text, '', $pos, $len );

    return $text;

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

    // Text stays the UTF-8 it is: control characters - C0, DEL and the C1 range - become
    // spaces, and a byte that is no UTF-8 at all becomes a ?. Every byte above 0x7F was
    // blanked, so "Zoë Müller" came out "Zo M ller". The cut keeps whole characters.

    $input = mb_scrub ( (string) $input, 'UTF-8' );
    $input = preg_replace('/[\x{00}-\x{1F}\x{7F}-\x{9F}]/u', ' ', $input);
    $input = preg_replace('/\s+/u', ' ', $input);

    if ( strlen($input) > $len )
      $input = mb_strcut ( $input, 0, $len, 'UTF-8' );

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

    // An end of 0 is an end: tested for truth, '5..0' lost it and became 1..5, '0..0' the
    // default 1..10, and so did a bare '0', which is 1..0 as any bare 'b' is 1..b.

    if     ( $p2 !== '' ) { }
    elseif ( $p1 !== '' ) { $p2 = $p1; $p1 = 1;  }
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

    // A range is built whole, so one of more values than $padSeqMaxTries is refused: '1..
    // 100000000' asked PHP for a hundred-million-element array. Strict mode says so; the
    // lenient walk keeps the first $padSeqMaxTries values.

    $max   = $GLOBALS ['padSeqMaxTries'] ?? 1000000;
    $count = $increment ? floor ( $span / $increment ) + 1 : 1;

    if ( is_numeric ( $p1 ) and $count > $max ) {

      if ( $padCheckSyntax )
        padError ( "the range '" . trim ( $input ) . "' has $count values, more than the $max a range may have" );

      $p2 = $p1 + ( ( $p2 >= $p1 ) ? 1 : -1 ) * ( $max - 1 ) * $increment;

    }

    return range ( $p1, $p2, $increment );

  }

  // Each numeric entry becomes the number it spells: intval cut a decimal to its whole part
  // and clamped an integer too long for PHP's int, so {sequence '1;2.5;4'} gave 1 2 4 and
  // '9223372036854775808' became 9223372036854775807. An integer that fits is an int, an
  // integer that does not stays the digits it is, any other number - 2.5, 1e3 - is a float.
  //
  // It is decided by the number, not by the spelling: '07' is 7, where the integer filter
  // turned leading zeros down and {sequence '00;07'} printed 00 07. And a number past what a
  // float holds stays the text written, as an over-long integer does: 1e400 became INF, a
  // value no field could be resolved to, and {sequence '3;1e400'} ended the request on
  // "Field '$sequence' not found".

  function padGetList ( $list ) {

    $list = explode ( ';', $list );

    foreach ( $list as $key => $value)
      if ( is_numeric ($value) ) {

        $number = trim ( $value ) + 0;

        if     ( is_int ( $number )                             ) $list [$key] = $number;
        elseif ( preg_match ( '/^\s*[+-]?[0-9]+\s*$/', $value ) ) $list [$key] = trim ( $value );
        elseif ( is_finite ( $number )                          ) $list [$key] = $number;
        else                                                       $list [$key] = trim ( $value );

      }

    return $list;

  }

?>
