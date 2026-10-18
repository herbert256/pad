<?php

  // Collapses ( ... ) groups, the same shape as padEvalArray: find the first close, pair it
  // with the nearest preceding open, drop both bracket tokens, evaluate the enclosed range
  // with padEvalOpr, then recurse for the next pair and return.
  //
  // The token immediately before the open is remembered as well: if it is a TYPE token the
  // close position is stored in its [3], marking where that function call's parameter list
  // ends so eval/type/parms.php knows how far to read.
  //
  // A group that stands after a pipe of a group around it - the (@ | + 1) of ($n | (@ | + 1))
  // - is left to be reduced with that pipe's part (padEvalPipePart), where its @ is the value
  // piped in; reduced first, it read the value of the segment.

  function padEvalOpnCls ( &$result, $myself, $start=0, $end=PHP_INT_MAX ) {

    $prev  = FALSE;
    $opens = [];
    $piped = FALSE;

    foreach ( $result as $key => $value ) {

      if ( $key < $start ) continue;
      if ( $key > $end   ) break;

      if ( $value [1] == 'open' ) {

        $opens [] = [ $key, $prev ];

      } elseif ( $value [1] == 'close' ) {

        list ( $open, $type ) = array_pop ( $opens ) ?? [ FALSE, FALSE ];

        if ( $piped and $open !== FALSE and padEvalPipePart ( $result, $start, $open ) )
          continue;

        unset ( $result [$open] );
        unset ( $result [$key]  );

        if ( $type )
          $result [$type] [3] = $key;

                                                          padEvalTrace ( 'opncls9', $result );
        padEvalPiped ( $result, $myself, $open, $key );   padEvalTrace ( 'piped2', $result );
        padEvalOpr ( $result, $myself, $open, $key );     padEvalTrace ( 'opr2', $result );
        padEvalOpnCls ( $result, $myself, $start, $end ); padEvalTrace ( 'opncls3', $result );
        return;

      } else {

        $prev = $key;

        if ( $value [1] == 'pipe' )
          $piped = TRUE;

      }

    }

  }

  // Whether the token at $at stands in the part after a pipe of a group around it, within the
  // range from $start - the range's own level counted as a group, as padEvalArray and
  // padEvalOpnCls hand over the inside of one: a pipe before it in the same argument, at
  // that level or any level between.

  function padEvalPipePart ( $result, $start, $at ) {

    $levels = [ FALSE ];

    foreach ( $result as $key => $val ) {

      if ( $key < $start ) continue;
      if ( $key >= $at   ) break;

      if ( ! empty ( $val ['arg'] ) )
        $levels [ array_key_last ( $levels ) ] = FALSE;

      if ( $val [1] == 'open' or $val [1] == 'a-open' )
        $levels [] = FALSE;
      elseif ( ( $val [1] == 'close' or $val [1] == 'a-close' ) and count ( $levels ) > 1 )
        array_pop ( $levels );
      elseif ( $val [1] == 'pipe' )
        $levels [ array_key_last ( $levels ) ] = TRUE;

    }

    return in_array ( TRUE, $levels, TRUE );

  }

?>
