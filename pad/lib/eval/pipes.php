<?php

  // Stage three of the evaluator: cuts the token stream at every 'pipe' token and returns
  // the segments in &$pipes, numbered from 0 and keeping the original token keys. The
  // caller (eval/eval.php) then runs padEvalResult over the segments in turn, feeding each
  // result in as the next segment's input value - which is what makes {echo $x | trim |
  // upper} chain. An expression without pipes simply yields one segment.
  //
  // Only a pipe outside brackets cuts the expression: one inside ( ) or [ ] belongs to that
  // group - ($n | + 1) * 2, upper($s | trim) - and padEvalPiped runs it where the group is
  // reduced. Cut here, the group was torn in two and reported never closed.

  function padEvalPipes ( $result, &$pipes ) {

    $pipe  = 0;
    $depth = 0;
    $pipes = [];

    foreach ( $result as $key => $val ) {

      if     ( $val [1] == 'open'  or $val [1] == 'a-open'  ) $depth++;
      elseif ( $val [1] == 'close' or $val [1] == 'a-close' ) $depth--;

      if ( $val [1] == 'pipe' and $depth <= 0 )
        $pipe++;
      else
        $pipes [$pipe] [$key] = $val;

    }

  }

  // The pipes inside one group being reduced - the range $start..$end of a ( ) or [ ], whose
  // own groups are reduced already - each argument or item on its own: the part before the
  // first pipe takes the segment's value $myself, as the head of an expression does, and
  // each part after it the answer of the one before, as a pipe segment does; the argument
  // becomes that one value, in the place of its first token. The @ of a part after a pipe
  // is the value piped into it (padEvalValue leaves it for here).

  function padEvalPiped ( &$result, $myself, $start, $end ) {

    // The arguments, and the pipes, of this group's own level: a group inside it is left
    // whole when it stands after one of its pipes (padEvalPipePart), and its own commas and
    // pipes are its own.

    $args  = [];
    $arg   = -1;
    $piped = FALSE;
    $depth = 0;

    foreach ( $result as $key => $val ) {

      if ( $key < $start ) continue;
      if ( $key > $end   ) break;

      if ( $val [1] == 'close' or $val [1] == 'a-close' )
        $depth--;

      if ( $arg < 0 or ( ! $depth and ! empty ( $val ['arg'] ) ) )
        $arg++;

      if ( $val [1] == 'open' or $val [1] == 'a-open' )
        $depth++;

      if ( ! $depth and $val [1] == 'pipe' ) {
        $val ['own'] = TRUE;
        $piped       = TRUE;
      }

      $args [$arg] [$key] = $val;

    }

    if ( ! $piped )
      return;

    $headWas = $GLOBALS ['padEvalHead'] ?? FALSE;

    foreach ( $args as $tokens ) {

      if ( ! in_array ( TRUE, array_column ( $tokens, 'own' ), TRUE ) )
        continue;

      $parts = [ 0 => [] ];
      $part  = 0;

      foreach ( $tokens as $key => $val )
        if ( ! empty ( $val ['own'] ) )
          $parts [++$part] = [];
        else
          $parts [$part] [$key] = $val;

      $value = $myself;

      foreach ( $parts as $n => $one ) {

        $GLOBALS ['padEvalHead'] = ( $n == 0 ) ? $headWas : FALSE;

        if ( $one )
          $value = padEvalResult ( $one, $value, '' );
        elseif ( $GLOBALS ['padCheckSyntax'] )
          padError ( "Expression error: an empty pipe segment inside brackets" );

      }

      $first = array_key_first ( $tokens );

      foreach ( array_keys ( $tokens ) as $key )
        unset ( $result [$key] );

      $result [$first] = [ $value, 'VAL' ];

      if ( ! empty ( $tokens [$first] ['arg'] ) )
        $result [$first] ['arg'] = TRUE;

    }

    $GLOBALS ['padEvalHead'] = $headWas;

    ksort ( $result );

  }

?>
