<?php

  // Collapses [ ... ] bracket groups into a single VAL token holding a real PHP array.
  //
  // Scans for the first a-close and pairs it with the nearest preceding a-open, so nested
  // brackets are handled innermost first. The enclosed range is evaluated in place -
  // parentheses then operators - and every value left inside becomes one array element, a
  // key => value pair one keyed element (padEvalArrayItems).
  // Then it recurses to pick up the next pair, which is why it returns immediately after
  // handling one. $start/$end bound the scan when called from within another group.

  function padEvalArray ( &$result, $myself, $start=0, $end=PHP_INT_MAX ) {

    $open = FALSE;

    foreach ( $result as $key => $value ) {

      if ( $key < $start ) continue;
      if ( $key > $end   ) break;

      if ( $value [1] == 'a-open' ) {

        $open = $key;

      } elseif ( $value [1] == 'a-close' ) {

        $result [$open] [0] = [];
        $result [$open] [1] = 'VAL';
        unset ( $result [$key]  );

                                                           padEvalTrace ( 'array3',  $result );
        padEvalOpnCls ( $result, $myself, $open+1, $key ); padEvalTrace ( 'opncls2', $result );
        padEvalOpr    ( $result, $myself, $open+1, $key ); padEvalTrace ( 'opr1',    $result );

        $items = [];

        foreach ( $result as $key2 => $value )
          if ( $key2 > $open and $key2 < $key ) {
            $items [] = $value;
            unset ( $result [$key2] );
          }

        $result [$open] [0] = padEvalArrayItems ( $items );

                                                         padEvalTrace ( 'array4', $result );
        padEvalArray ( $result, $myself, $start, $end ); padEvalTrace ( 'array5', $result );
        return;

      }

    }

  }

  // The list the reduced items of one [ ] make: each value an element, and a value followed
  // by => and another value a key with its value - [ 'title' => 'Minutes', 'n' => 2 ], mixed
  // with plain items as PHP mixes them. A key is one value; a => with no key before it, or no
  // value after it, is named under the strict check and left out.

  function padEvalArrayItems ( $items ) {

    global $padCheckSyntax;

    $list  = [];
    $count = count ( $items );

    for ( $n = 0; $n < $count; $n++ ) {

      if ( $items [$n] [1] == 'arrow' ) {
        if ( $padCheckSyntax )
          padError ( "Expression error: a => in a list with no key before it" );
        continue;
      }

      if ( ( $items [$n+1] [1] ?? '' ) != 'arrow' ) {
        $list [] = $items [$n] [0];
        continue;
      }

      $pairKey = $items [$n] [0];
      $n      += 2;

      if ( $n >= $count or $items [$n] [1] == 'arrow' ) {
        if ( $padCheckSyntax )
          padError ( "Expression error: the key '" . padMakeSafe ( is_scalar ( $pairKey ) ? (string) $pairKey : gettype ( $pairKey ), 40 ) . "' of a list has no value after its =>" );
        $n--;
        continue;
      }

      if ( $pairKey !== NULL and ! is_scalar ( $pairKey ) ) {
        if ( $padCheckSyntax )
          padError ( "Expression error: a key of a list is one value, not " . ( is_array ( $pairKey ) ? 'a list' : gettype ( $pairKey ) ) );
        continue;
      }

      $list [ is_bool ( $pairKey ) ? (int) $pairKey : (string) $pairKey ] = $items [$n] [0];

    }

    return $list;

  }

?>
