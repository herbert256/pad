<?php

  // splice='pos|len' or 'pos|len|seq' - array_splice on the sequence: removes len entries
  // from position pos, optionally putting the values of a named store in their place.
  // One parameter removes everything from pos onwards; with two, a numeric second
  // parameter is the length, while a non-numeric one names the replacement store and
  // everything from pos onwards makes way for it.
  //
  // A replacement store that was never pushed leaves the sequence alone. Splicing it in
  // regardless passed nothing as both the length and the replacement, which quietly took
  // out every entry from pos onwards - a mistyped store name cost the data.

  // The offset is a number: splice='abc' ended the request on array_splice's TypeError.
  // Strict mode names it, and the sequence is left as it was.

  // The offset and a length are whole numbers - splice=2.5 or '1|1.5' ended the request on
  // PHP's deprecation of the fraction - and only the store comes in third or in second place.

  if ( ! pqBoolWhole ( $pqActionList [0] ?? '' )
       or ( count ( $pqActionList ) > 1 and is_numeric ( $pqActionList [1] ) and ! pqBoolWhole ( $pqActionList [1] ) )
       or ( count ( $pqActionList ) > 2 and ! pqBoolWhole ( $pqActionList [1] ) ) ) {
    if ( $GLOBALS ['padCheckSyntax'] ?? FALSE )
      padError ( "splice= takes an offset, not '" . implode ( '|', $pqActionList ) . "'" );
    return;
  }

  if ( count ( $pqActionList ) == 1 )

    array_splice (
      $pqResult,
      $pqActionList [0]
    );

  elseif ( count ( $pqActionList ) == 2 ) {

    if ( is_numeric ( $pqActionList [1] ) )

      array_splice (
        $pqResult,
        $pqActionList [0],
        $pqActionList [1]
      );

    elseif ( isset ( $pqStore [ $pqActionList [1] ] ) )

      array_splice (
        $pqResult,
        $pqActionList [0],
        null,
        $pqStore [ $pqActionList [1] ]
      );

  }

  elseif ( count ( $pqActionList ) == 3 and isset ( $pqStore [ $pqActionList [2] ] ) )

    array_splice (
      $pqResult,
      $pqActionList [0],
      $pqActionList [1],
      $pqStore [ $pqActionList [2] ]
    );

?>
