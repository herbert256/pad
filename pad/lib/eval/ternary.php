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
  //
  // As an argument of a call - or an item of a [ ] list - the ternary is that argument only:
  // its condition starts at the start of the argument the ? stands in and its else branch
  // ends where the next argument starts, at the tokens padEvalParse marked after a comma.
  // The ternary took every argument of the call: replace('a', $n gt 1 ? 'x' : 'y') was "the
  // condition of an inline ternary is not one value", the condition being 'a' $n gt 1, and
  // php:str_repeat($n gt 0 ? '+' : '-', 3) dropped the 3 with the branch not taken.

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

    $from = 0;
    $to   = count ( $keys );

    for ( $n = $q; $n > 0; $n-- )
      if ( padEvalTernaryArg ( $result, $keys, $n ) ) {
        $from = $n;
        break;
      }

    for ( $n = $c + 1; $n < count ( $keys ); $n++ )
      if ( padEvalTernaryArg ( $result, $keys, $n ) ) {
        $to = $n;
        break;
      }

    if ( $q == $from )

      $cond = $myself;

    else {

      padEvalOpr ( $result, $myself, $keys [$from], $keys [$q] - 1 );

      $left = [];

      foreach ( $result as $k => $t )
        if ( $k >= $keys [$from] and $k < $keys [$q] )
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

    for ( $n = $q + 1; $n < $to; $n++ )
      if ( ( $true and $n > $c ) or ( ! $true and $n < $c ) )
        unset ( $result [ $keys [$n] ] );

    if ( ! $true and $c + 1 >= $to or $true and $c == $q + 1 ) {
      if ( $padCheckSyntax )
        padError ( "a branch of an inline ternary is empty" );
      return TRUE;
    }

    // The branch taken starts the argument now, for a ternary nested in it: f(1, $a ? 2 : $b
    // ? 3 : 4) read 1 $b as the condition of the second.

    if ( $from )
      foreach ( $result as $k => $t )
        if ( $k > $keys [$q] ) {
          $result [$k] ['arg'] = TRUE;
          break;
        }

    padEvalOpr ( $result, $myself, $start, $end );

    return TRUE;

  }

  // Whether the token at $keys [$n] starts an argument of the range's own: marked after a
  // comma, and no argument of a call inside the range - that call is still a TYPE token, its
  // arguments reduced to values before it and its [3] past them.

  function padEvalTernaryArg ( $result, $keys, $n ) {

    if ( empty ( $result [ $keys [$n] ] ['arg'] ) )
      return FALSE;

    for ( $m = 0; $m < $n; $m++ )
      if ( $result [ $keys [$m] ] [1] == 'TYPE' and ( $result [ $keys [$m] ] [3] ?? 0 ) > $keys [$n] )
        return FALSE;

    return TRUE;

  }

?>
