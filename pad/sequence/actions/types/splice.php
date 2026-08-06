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