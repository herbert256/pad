<?php

  // Siteswaps - the siteswap notation, {siteswap '531'/}. A juggling pattern written in the
  // siteswap notation jugglers have used since 1985 - each throw by the number of beats until
  // it is thrown again, 3 the cascade, 531, 441, 97531 - drawn as a ladder diagram: the right
  // and the left hand as two rails along the time, every throw a line from the beat it leaves
  // to the beat it lands, an arc back into the same hand, a line across to the other, coloured
  // by the orbit it belongs to; the throws written under their beats.
  //
  // padSiteswapRead     the throws of a pattern: vanilla (531), multiplex - several balls thrown
  //                     at once, [33]3 - and synchronous, both hands at once, (4,2x)(2x,4), with
  //                     * for the same again with the hands swapped; or what it cannot read
  // padSiteswapCheck    whether it can be juggled: the average of the throws is the number of
  //                     balls, and every beat - every hand of a beat, synchronous - catches as
  //                     many balls as it throws
  // padSiteswapOrbits   the orbits: the throws a ball passes through, round and round
  // padSiteswapGround   whether the pattern starts from the ground state, the balls thrown one a
  //                     beat as in the cascade - as 531 and 441 do, 51 does not
  // padSiteswapSvg      the ladder diagram
  //
  // The theorem the check follows: a sequence of throws t(0) ... t(n-1) can be juggled when the
  // beats they land on, (i + t(i)) mod n, are all different - two balls never land at once - and
  // then their average is the number of balls (Buhler, Eisenbud, Graham and Wright, "Juggling
  // drops and descents", 1994); for multiplex and synchronous patterns a beat or a hand catches as
  // many balls as it throws, as Juggling Lab checks them. A throw above 9 is a letter, a = 10 to
  // z = 35; in a synchronous pattern every throw is even and x sends it to the other hand.

  const PAD_SITESWAP_COLOURS = [ 'rail'  => [ '#6b7280', '#9ca3af' ], 'text' => [ '#111827', '#f3f4f6' ],
                                 'o1'    => [ '#0072b2', '#56b4e9' ], 'o2' => [ '#d55e00', '#f0a35e' ],
                                 'o3'    => [ '#009e73', '#3fd3a6' ], 'o4' => [ '#cc79a7', '#e7a6cb' ],
                                 'o5'    => [ '#b38600', '#f0c75e' ], 'o6' => [ '#5b5bd6', '#9d9df2' ] ];

  const PAD_SITESWAP_LONGEST = 200;

  // A pattern as [ sync, beats ]: beats the period's beats, each a list of throws [ value,
  // hand (0 right, 1 left - for a vanilla pattern the hand of the beat), lands in hand,
  // written as ] - or a string, what is wrong.

  function padSiteswapRead ( $text ) {

    $text = preg_replace ( '/\s+/u', '', (string) $text );

    if ( strlen ( $text ) > PAD_SITESWAP_LONGEST )
      return "a pattern of " . strlen ( $text ) . " characters - a pattern is at most " . PAD_SITESWAP_LONGEST;

    if ( $text === '' )
      return "no throws";

    if ( $text [0] == '(' )
      return padSiteswapSync ( $text );

    $beats = [];
    $at    = 0;
    $len   = strlen ( $text );

    while ( $at < $len ) {
      if ( $text [$at] == '[' ) {
        $end = strpos ( $text, ']', $at );
        if ( $end === FALSE )
          return "a multiplex '[' that does not close";
        $inner = substr ( $text, $at + 1, $end - $at - 1 );
        if ( strlen ( $inner ) < 1 )
          return "an empty multiplex []";
        $throws = [];
        foreach ( str_split ( $inner ) as $char ) {
          $value = padSiteswapValue ( $char );
          if ( $value === NULL )
            return "a throw '" . padNotationShow ( $char ) . "' in a multiplex - a throw is 0 to 9 or a to z";
          if ( $value == 0 )
            return "a 0 in the multiplex [$inner] - a multiplex throws balls, an empty hand is a 0 alone";
          $throws [] = $value;
        }
        $beats [] = $throws;
        $at = $end + 1;
        continue;
      }
      $value = padSiteswapValue ( $text [$at] );
      if ( $value === NULL )
        return "a throw '" . padNotationShow ( $text [$at] ) . "' - a throw is 0 to 9 or a to z, [ ] throws several at once, (4,2x) both hands";
      $beats [] = $value ? [ $value ] : [];
      $at++;
    }

    // A vanilla beat is thrown by the right hand and the left in turn; the hand it lands in
    // follows from the throw: an odd one crosses.

    $pattern = [];

    foreach ( $beats as $beat => $throws )
      $pattern [$beat] = array_map ( fn ( $value ) => [ $value, $beat % 2, ( $beat + $value ) % 2, padSiteswapChar ( $value ) ], $throws );

    return [ FALSE, $pattern ];

  }

  // A synchronous pattern: (left,right) pairs - each side a throw or a multiplex, every throw
  // even, x to cross - and * for the pairs again with the hands swapped. A pair takes two beats,
  // the second empty.

  function padSiteswapSync ( $text ) {

    $star = str_ends_with ( $text, '*' );

    if ( $star )
      $text = substr ( $text, 0, -1 );

    if ( ! preg_match ( '/^(?:\([^()]*\))+$/', $text ) )
      return "a synchronous pattern '" . padNotationShow ( $text ) . "' - pairs (left,right), (4,2x)(2x,4), with * for the same again with the hands swapped";

    preg_match_all ( '/\(([^()]*)\)/', $text, $pairs );

    $sides = [];

    foreach ( $pairs [1] as $pair ) {
      $parts = explode ( ',', $pair );
      if ( count ( $parts ) != 2 )
        return "a pair ($pair) - a pair is the left hand's throw and the right's, (4,2x)";
      $two = [];
      foreach ( $parts as $hand => $part ) {
        $part = ( strlen ( $part ) > 2 and $part [0] == '[' and str_ends_with ( $part, ']' ) ) ? substr ( $part, 1, -1 ) : $part;
        if ( ! preg_match ( '/^(?:[0-9a-wyzA-WYZ]x?)+$/', $part ) )
          return "a throw '" . padNotationShow ( $parts [$hand] ) . "' in ($pair) - a throw is an even number, x after it to cross";
        preg_match_all ( '/([0-9a-zA-Z])(x?)/', $part, $throws, PREG_SET_ORDER );
        if ( count ( $throws ) > 1 and $part === $parts [$hand] )
          return "a throw '" . padNotationShow ( $parts [$hand] ) . "' in ($pair) - several throws of one hand are a multiplex, [4x2]";
        $list = [];
        foreach ( $throws as $throw ) {
          $value = padSiteswapValue ( $throw [1] );
          if ( $value === NULL )
            return "a throw '" . padNotationShow ( $throw [0] ) . "' in ($pair) - a throw is an even number, x after it to cross";
          if ( $value % 2 )
            return "an odd throw $throw[0] in ($pair) - the throws of a synchronous pattern are even, x after one to cross";
          if ( $value == 0 and count ( $throws ) > 1 )
            return "a 0 in the multiplex " . $parts [$hand] . " - a multiplex throws balls";
          if ( $value )
            $list [] = [ $value, $throw [2] === 'x' ];
        }
        $two [$hand] = $list;
      }
      $sides [] = $two;
    }

    if ( $star )
      foreach ( $sides as $two )
        $sides [] = [ $two [1], $two [0] ];

    // The left hand is 1, the right 0; a pair's throws all leave on its first beat.

    $pattern = [];

    foreach ( $sides as $at => $two ) {
      $pattern [ 2 * $at ]     = [];
      $pattern [ 2 * $at + 1 ] = [];
      foreach ( [ 1 => $two [0], 0 => $two [1] ] as $hand => $list )
        foreach ( $list as list ( $value, $cross ) )
          $pattern [ 2 * $at ] [] = [ $value, $hand, $cross ? 1 - $hand : $hand, padSiteswapChar ( $value ) . ( $cross ? 'x' : '' ) ];
    }

    return [ TRUE, $pattern ];

  }

  function padSiteswapValue ( $char ) {

    if ( ctype_digit ( $char ) )
      return (int) $char;

    if ( ctype_alpha ( $char ) and strtolower ( $char ) !== 'x' )
      return ord ( strtolower ( $char ) ) - ord ( 'a' ) + 10;

    return NULL;

  }

  function padSiteswapChar ( $value ) {

    return $value < 10 ? (string) $value : chr ( ord ( 'a' ) + $value - 10 );

  }

  // The pattern written back, as the notation writes it: 531, [33]3, (4,2x)(2x,4).

  function padSiteswapText ( $sync, $pattern ) {

    if ( ! $sync )
      return implode ( '', array_map ( fn ( $throws ) => count ( $throws ) > 1 ? '[' . implode ( '', array_column ( $throws, 3 ) ) . ']' : ( $throws ? $throws [0] [3] : '0' ), $pattern ) );

    $text = '';

    for ( $beat = 0; $beat < count ( $pattern ); $beat += 2 ) {
      $side = [];
      foreach ( [ 1, 0 ] as $hand ) {
        $mine = array_values ( array_filter ( $pattern [$beat], fn ( $throw ) => $throw [1] == $hand ) );
        $side [] = count ( $mine ) > 1 ? '[' . implode ( '', array_column ( $mine, 3 ) ) . ']' : ( $mine ? $mine [0] [3] : '0' );
      }
      $text .= '(' . implode ( ',', $side ) . ')';
    }

    return $text;

  }

  // [ balls, '' ] when it can be juggled, else [ 0, what is wrong ]. A vanilla pattern counts
  // per beat, a synchronous one per beat and hand.

  function padSiteswapCheck ( $sync, $pattern ) {

    $period = count ( $pattern );
    $total  = 0;

    foreach ( $pattern as $throws )
      foreach ( $throws as $throw )
        $total += $throw [0];

    if ( $total % $period ) {
      $average = padNotationNum ( $total / $period );
      return [ 0, "throws that average $average - no whole number of balls, so two land at once" . padSiteswapClash ( $sync, $pattern ) ];
    }

    $thrown = $landed = [];

    foreach ( $pattern as $beat => $throws )
      foreach ( $throws as $throw ) {
        $from = $sync ? "$beat:$throw[1]" : $beat;
        $to   = $sync ? ( ( $beat + $throw [0] ) % $period ) . ":$throw[2]" : ( $beat + $throw [0] ) % $period;
        $thrown [$from] = ( $thrown [$from] ?? 0 ) + 1;
        $landed [$to]   = ( $landed [$to] ?? 0 ) + 1;
      }

    foreach ( $landed as $slot => $count )
      if ( $count != ( $thrown [$slot] ?? 0 ) )
        return [ 0, "throws no juggler can make" . padSiteswapClash ( $sync, $pattern ) ];

    if ( $total == 0 )
      return [ 0, "no ball thrown" ];

    return [ $total / $period, '' ];

  }

  // Where it goes wrong: the throws that land at once where a hand catches fewer.

  function padSiteswapClash ( $sync, $pattern ) {

    $period = count ( $pattern );
    $thrown = $landed = [];

    foreach ( $pattern as $beat => $throws )
      foreach ( $throws as $throw ) {
        $from = $sync ? "$beat:$throw[1]" : $beat;
        $to   = $sync ? ( ( $beat + $throw [0] ) % $period ) . ":$throw[2]" : ( $beat + $throw [0] ) % $period;
        $thrown [$from] = ( $thrown [$from] ?? 0 ) + 1;
        $landed [$to] [] = $throw [3] . ' of beat ' . ( $beat + 1 );
      }

    ksort ( $landed, SORT_NATURAL );

    foreach ( $landed as $slot => $who )
      if ( count ( $who ) > ( $thrown [$slot] ?? 0 ) )
        return ' - the ' . implode ( ' and the ', $who ) . ' land together on beat ' . ( (int) $slot + 1 )
             . ( $sync ? ' in the ' . ( str_ends_with ( (string) $slot, ':1' ) ? 'left' : 'right' ) . ' hand' : '' );

    return '';

  }

  // The orbits: each throw - [ beat, index ] - leads to the throw that sends its ball on, the
  // balls landing on a beat (and hand) taking its throws in order. The cycles of that are the
  // orbits; [ orbit of every throw, the orbits each with its throws ].

  function padSiteswapOrbits ( $sync, $pattern ) {

    $period  = count ( $pattern );
    $arrive  = [];
    $leave   = [];

    foreach ( $pattern as $beat => $throws )
      foreach ( $throws as $index => $throw ) {
        $slot = $sync ? "$beat:$throw[1]" : (string) $beat;
        $land = $sync ? ( ( $beat + $throw [0] ) % $period ) . ":$throw[2]" : (string) ( ( $beat + $throw [0] ) % $period );
        $leave  [$slot] [] = "$beat:$index";
        $arrive [$land] [] = "$beat:$index";
      }

    $next = [];

    foreach ( $arrive as $slot => $throws )
      foreach ( $throws as $at => $throw )
        $next [$throw] = $leave [$slot] [$at];

    $orbit  = [];
    $orbits = [];

    foreach ( $pattern as $beat => $throws )
      foreach ( $throws as $index => $throw ) {
        $key = "$beat:$index";
        if ( isset ( $orbit [$key] ) )
          continue;
        $number = count ( $orbits );
        $orbits [$number] = [];
        while ( ! isset ( $orbit [$key] ) ) {
          $orbit [$key] = $number;
          $orbits [$number] [] = $key;
          $key = $next [$key];
        }
      }

    return [ $orbit, $orbits ];

  }

  // Whether a vanilla or multiplex pattern starts from the ground state - the balls landing
  // one a beat on the beats right ahead - from one of its beats: the balls in the air at that
  // beat are those thrown before it by the pattern repeated, and land where they land.

  function padSiteswapGround ( $pattern, $balls ) {

    $period = count ( $pattern );
    $high   = max ( $balls, ...array_map ( fn ( $throws ) => $throws ? max ( array_column ( $throws, 0 ) ) : 0, $pattern ) );

    for ( $start = 0; $start < $period; $start++ ) {
      $state = array_fill ( 0, $high, 0 );
      for ( $back = 1; $back <= $high; $back++ )
        foreach ( $pattern [ ( ( $start - $back ) % $period + $period ) % $period ] as $throw )
          if ( $throw [0] - $back >= 0 and $throw [0] - $back < $high )
            $state [ $throw [0] - $back ]++;
      if ( $state === array_merge ( array_fill ( 0, $balls, 1 ), array_fill ( 0, $high - $balls, 0 ) ) )
        return TRUE;
    }

    return FALSE;

  }

  // The pattern in words: what each throw of the period does.

  function padSiteswapWords ( $sync, $pattern ) {

    $said = [];

    foreach ( $pattern as $beat => $throws ) {
      if ( ! $throws ) {
        if ( ! $sync )
          $said [] = '0, an empty hand';
        continue;
      }
      // A hand's throws of one beat together: one throw, or a multiplex, [33] at once.

      foreach ( $sync ? [ 1, 0 ] : [ $beat % 2 ] as $side ) {
        $mine = $sync ? array_values ( array_filter ( $throws, fn ( $one ) => $one [1] == $side ) ) : $throws;
        if ( ! $mine )
          continue;
        $words = [];
        foreach ( $mine as list ( $value, $hand, $lands, $written ) )
          $words [] = $written . ', ' . ( ( ! $sync and $value == 2 ) ? 'held'
                    : ( ( $lands == $hand ) ? "back into the same hand $value beats later" : "across to the other hand $value " . ( $value == 1 ? 'beat' : 'beats' ) . ' later' ) );
        $said [] = ( $sync ? ( $side ? 'the left hand ' : 'the right hand ' ) : '' )
                 . ( count ( $mine ) > 1 ? '[' . implode ( '', array_column ( $mine, 3 ) ) . '] at once: ' . implode ( ' and ', $words ) : ( $sync ? 'throws ' : '' ) . $words [0] );
      }
    }

    return implode ( '; ', $said );

  }

  // The ladder: the right hand's rail on top, the left's below, a beat 26 wide; a throw an arc
  // outward back into its hand or a line across to the other, coloured by its orbit, a dot where
  // it leaves; the window as many whole periods as show every throw land - at least 12 beats,
  // at most 60. The written throws stand under their beats.

  function padSiteswapSvg ( $sync, $pattern, $orbit, $id, $title, $desc ) {

    $period = count ( $pattern );
    $high   = max ( 1, ...array_map ( fn ( $throws ) => $throws ? max ( array_column ( $throws, 0 ) ) : 0, $pattern ) );
    $window = $period * max ( 1, (int) ceil ( max ( 12, $high + $period ) / $period ) );

    while ( $window > 60 and $window - $period >= $period )
      $window -= $period;

    $step   = 26;
    $left   = 30;
    $rails  = [ 0 => 20 + min ( 60, 6 + 4 * $high ), 1 => 0 ];
    $rails [1] = $rails [0] + 50;
    $height = $rails [1] + min ( 60, 6 + 4 * $high ) + 30;
    $width  = $left + $window * $step + 20;
    $num    = 'padNotationNum';
    $x      = fn ( $beat ) => $left + $beat * $step + $step / 2;

    $svg = '<clipPath id="' . $id . '-clip"><rect x="0" y="0" width="' . ( $x ( $window ) - $step / 2 ) . '" height="' . $height . '"/></clipPath>'
         . '<text class="pss-hand" x="12" y="' . ( $rails [0] + 4 ) . '" text-anchor="middle">R</text>'
         . '<text class="pss-hand" x="12" y="' . ( $rails [1] + 4 ) . '" text-anchor="middle">L</text>'
         . '<path class="pss-rail" d="M' . $left . ' ' . $rails [0] . 'H' . ( $width - 10 ) . 'M' . $left . ' ' . $rails [1] . 'H' . ( $width - 10 ) . '"/>'
         . '<g clip-path="url(#' . $id . '-clip)">';

    $dots   = '';
    $labels = '';

    for ( $beat = 0; $beat < $window; $beat++ ) {

      $at     = $beat % $period;
      $throws = $pattern [$at];

      foreach ( $throws as $index => list ( $value, $hand, $lands, $written ) ) {

        // A vanilla beat is thrown by the right hand and the left in turn along the window - a
        // pattern of odd period starts its second round with the other hand.

        if ( ! $sync ) {
          $hand  = $beat % 2;
          $lands = ( $beat + $value ) % 2;
        }

        // The second ball of a multiplex bows away from the first, so two throws of one value
        // stay two lines.

        $bow   = 8 * count ( array_filter ( array_slice ( $throws, 0, $index ), fn ( $one ) => $sync ? $one [1] == $throws [$index] [1] : TRUE ) );
        $class = 'pss-throw pss-o' . ( $orbit [ "$at:$index" ] % 6 + 1 );
        $x1    = $x ( $beat );
        $x2    = $x ( $beat + $value );
        $y1    = $rails [$hand];
        $y2    = $rails [$lands];

        if ( $hand == $lands ) {
          $lift = ( min ( 60, 6 + 4 * $value ) + $bow ) * ( $hand ? 1 : -1 );
          $svg .= '<path class="' . $class . '" d="M' . $num ( $x1 ) . ' ' . $y1 . 'C' . $num ( $x1 ) . ' ' . $num ( $y1 + $lift ) . ' ' . $num ( $x2 ) . ' ' . $num ( $y2 + $lift ) . ' ' . $num ( $x2 ) . ' ' . $y2 . '"/>';
        } elseif ( $bow )
          $svg .= '<path class="' . $class . '" d="M' . $num ( $x1 ) . ' ' . $y1 . 'Q' . $num ( ( $x1 + $x2 ) / 2 ) . ' ' . $num ( ( $y1 + $y2 ) / 2 - 2 * $bow ) . ' ' . $num ( $x2 ) . ' ' . $y2 . '"/>';
        else
          $svg .= '<path class="' . $class . '" d="M' . $num ( $x1 ) . ' ' . $y1 . 'L' . $num ( $x2 ) . ' ' . $y2 . '"/>';

        $dots .= '<circle class="pss-dot pss-o' . ( $orbit [ "$at:$index" ] % 6 + 1 ) . '" cx="' . $num ( $x1 ) . '" cy="' . $y1 . '" r="3.5"/>';

      }

      if ( ! $sync )
        $labels .= '<text class="pss-label' . ( $at == 0 ? ' pss-first' : '' ) . '" x="' . $num ( $x ( $beat ) ) . '" y="' . ( $height - 8 ) . '" text-anchor="middle">'
                 . ( count ( $throws ) > 1 ? '[' . implode ( '', array_column ( $throws, 3 ) ) . ']' : ( $throws ? $throws [0] [3] : '0' ) ) . '</text>';
      elseif ( $beat % 2 == 0 )
        $labels .= '<text class="pss-label' . ( $at == 0 ? ' pss-first' : '' ) . '" x="' . $num ( $x ( $beat ) + $step / 2 ) . '" y="' . ( $height - 8 ) . '" text-anchor="middle">'
                 . padNotationAttr ( padSiteswapText ( TRUE, [ $pattern [$at], $pattern [ $at + 1 ] ] ) ) . '</text>';

    }

    return '<svg xmlns="http://www.w3.org/2000/svg" class="pad-siteswap" role="img" aria-labelledby="' . "$id-title $id-desc" . '"'
         . ' width="' . $width . '" height="' . $height . '" viewBox="0 0 ' . $width . ' ' . $height . '">'
         . "<title id=\"$id-title\">" . padNotationAttr ( $title ) . '</title>'
         . "<desc id=\"$id-desc\">" . padNotationAttr ( $desc ) . '</desc>'
         . $svg . '</g>' . $dots . $labels . '</svg>';

  }

?>
