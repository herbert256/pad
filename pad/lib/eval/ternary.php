<?php

  // The inline ternary, cond ? then : else, the weakest binding in an expression - padEvalOpr
  // asks for it before any operator group, over the same token range.
  //
  // The first ? of the range splits it. Its : is the first one at the same depth - a ? in
  // between opens a nested ternary that takes the next : - so a ? b : c ? d : e reads as
  // a ? b : (c ? d : e). The condition is reduced on its own; then the ?, the :, the
  // condition and the branch not taken are dropped and what is left is reduced in their
  // place. With nothing written before the ?, the condition is the piped value:
  // {$count | ? 'some' : 'none'}. The condition counts as PAD counts a value - an array when
  // it has elements, anything else by PHP's truth.
  //
  // Every field of both branches was resolved before this stage, so under the strict check
  // a missing field in the branch not taken is still reported.

  function padEvalTernary ( &$result, $myself, $start, $end ) {

    $keys = [];

    foreach ( $result as $k => $t ) {
      if ( $k < $start ) continue;
      if ( $k > $end   ) break;
      $keys [] = $k;
    }

    $q = FALSE;

    foreach ( $keys as $n => $k )
      if ( $result [$k] [1] == 'OPR' and $result [$k] [0] === '?' ) {
        $q = $n;
        break;
      }

    if ( $q === FALSE )
      return FALSE;

    $c     = FALSE;
    $depth = 0;

    for ( $n = $q + 1; $n < count ( $keys ); $n++ ) {

      $t = $result [ $keys [$n] ];

      if ( $t [1] != 'OPR' )
        continue;

      if ( $t [0] === '?' )
        $depth++;
      elseif ( $t [0] === ':' and $depth )
        $depth--;
      elseif ( $t [0] === ':' ) {
        $c = $n;
        break;
      }

    }

    global $padCheckSyntax;

    if ( $c === FALSE ) {

      if ( $padCheckSyntax )
        padError ( "the ? of an inline ternary has no : - write cond ? then : else" );

      unset ( $result [ $keys [$q] ] );

      return FALSE;

    }

    if ( $q == 0 )

      $cond = $myself;

    else {

      padEvalOpr ( $result, $myself, $keys [0], $keys [$q] - 1 );

      $left = [];

      foreach ( $result as $k => $t )
        if ( $k >= $keys [0] and $k < $keys [$q] )
          $left [$k] = $t;

      if ( count ( $left ) != 1 or reset ( $left ) [1] != 'VAL' ) {
        if ( $padCheckSyntax )
          padError ( "the condition of an inline ternary is not one value" );
        $cond = '';
      } else
        $cond = reset ( $left ) [0];

      foreach ( array_keys ( $left ) as $k )
        unset ( $result [$k] );

    }

    $true = is_array ( $cond ) ? count ( $cond ) > 0 : (bool) $cond;

    unset ( $result [ $keys [$q] ] );
    unset ( $result [ $keys [$c] ] );

    for ( $n = $q + 1; $n < count ( $keys ); $n++ )
      if ( ( $true and $n > $c ) or ( ! $true and $n < $c ) )
        unset ( $result [ $keys [$n] ] );

    if ( ! $true and $c + 1 >= count ( $keys ) or $true and $c == $q + 1 ) {
      if ( $padCheckSyntax )
        padError ( "a branch of an inline ternary is empty" );
      return TRUE;
    }

    padEvalOpr ( $result, $myself, $start, $end );

    return TRUE;

  }

?>
