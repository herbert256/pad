<?php

  // Text-level reading of one tag's pairs, for the features that act on a template before it
  // renders: the slot fills a custom tag takes out of its content (lib/slot.php) and the
  // layout blocks the build step resolves (lib/layout.php).
  //
  // padPairScan lists every {tag ...} ... {/tag} of one name in a text, nested ones too,
  // each with the offsets of its opener and closer, the text of its parameters and the
  // text between. An opener is {tag followed by a space, a } or a /, so {slots} is not a
  // {slot}; its end is found by brace depth, so a parameter holding a tag of its own reads
  // to its real end; {tag .../} opens nothing. A closer may carry options or a pipe,
  // {/tag | upper}. An opener without a closer is left out.

  function padPairScan ( $text, $tag ) {

    $pairs  = [];
    $stack  = [];
    $offset = 0;
    $open   = '{' . $tag;
    $close  = '{/' . $tag;

    while ( TRUE ) {

      $nextOpen  = padPairNext ( $text, $open,  $offset );
      $nextClose = padPairNext ( $text, $close, $offset );

      if ( $nextOpen === FALSE and $nextClose === FALSE )
        break;

      if ( $nextClose === FALSE or ( $nextOpen !== FALSE and $nextOpen < $nextClose ) ) {

        $end = padPairTagEnd ( $text, $nextOpen + strlen ( $open ) );

        if ( $end === FALSE )
          break;

        $parms  = substr ( $text, $nextOpen + strlen ( $open ), $end - $nextOpen - strlen ( $open ) );
        $single = str_ends_with ( $parms, '/' );

        if ( $single )
          $pairs [] = [ 'start' => $nextOpen, 'inner' => $end + 1, 'close' => $end + 1, 'end' => $end + 1,
                        'parms' => trim ( substr ( $parms, 0, -1 ) ), 'single' => TRUE, 'depth' => count ( $stack ) ];
        else
          $stack [] = [ 'start' => $nextOpen, 'inner' => $end + 1, 'parms' => trim ( $parms ) ];

        $offset = $end + 1;

      } else {

        $end = padPairTagEnd ( $text, $nextClose + strlen ( $close ) );

        if ( $end === FALSE )
          break;

        if ( $stack ) {
          $one = array_pop ( $stack );
          $pairs [] = [ 'start' => $one ['start'], 'inner' => $one ['inner'], 'close' => $nextClose,
                        'end' => $end + 1, 'parms' => $one ['parms'], 'single' => FALSE,
                        'depth' => count ( $stack ) ];
        }

        $offset = $end + 1;

      }

    }

    usort ( $pairs, fn ( $a, $b ) => $a ['start'] <=> $b ['start'] );

    return $pairs;

  }

  function padPairNext ( $text, $needle, $offset ) {

    while ( ( $pos = strpos ( $text, $needle, $offset ) ) !== FALSE ) {

      $next = $text [ $pos + strlen ( $needle ) ] ?? '';

      if ( in_array ( $next, [ ' ', '}', '/', "\n", "\t", "\r" ], TRUE ) )
        return $pos;

      $offset = $pos + 1;

    }

    return FALSE;

  }

  function padPairTagEnd ( $text, $offset ) {

    $depth = 1;
    $end   = strlen ( $text );

    for ( $i = $offset; $i < $end; $i++ )
      if ( $text [$i] == '{' )
        $depth++;
      elseif ( $text [$i] == '}' and ! --$depth )
        return $i;

    return FALSE;

  }

  // Whether the stretch of text in front of an offset leaves every tag pair of the text
  // closed again - whether a tag at that offset stands directly in the text, not inside
  // another tag's content. The pairs are those the whole text closes, as for @else@.

  function padPairTopLevel ( $text, $offset ) {

    return padOpenCloseCount ( substr ( $text, 0, $offset ), padOpenCloseList ( $text ) );

  }

  // The name a pair's parameters give it: a quoted literal as it stands, anything else
  // evaluated - {slot $which}.

  function padPairName ( $parms ) {

    $first = trim ( padExplode ( $parms, ',' ) [0] ?? '' );

    if ( preg_match ( '/^([\'"])(.*)\1$/s', $first, $match ) )
      return $match [2];

    if ( $first === '' )
      return '';

    return (string) padEval ( $first );

  }

?>
