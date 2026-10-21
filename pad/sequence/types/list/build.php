<?php

  // Build strategy 'build' for the list sequence: the whole term list is the parameter split
  // on semicolons, so {list '5;2;8;1;9'} iterates those five values in that order.
  // padGetList() makes the numeric entries the numbers they spell and leaves anything else a string;
  // build/types/build.php then walks the result like any other ready-made list.

  // A list given as a list - {sequence [3, 1, 2]}, list=$values - is taken as it is, its
  // values in order; one of them that is no single value has no place in a sequence.
  //
  // FALSE is how the walk says a candidate makes no term, so a FALSE in the list is lost; the
  // strict check names it, where [1, false, 2] counted 2 in silence.

  if ( is_array ( $pqParm ) ) {

    foreach ( $pqParm as $pqListOne )
      if ( ! is_scalar ( $pqListOne ) and ( $GLOBALS ['padCheckSyntax'] ?? FALSE ) )
        padError ( "A list sequence holds values, and one of them is a " . gettype ( $pqListOne ) );
      elseif ( $pqListOne === FALSE and ( $GLOBALS ['padCheckSyntax'] ?? FALSE ) )
        padError ( "A list sequence holds values, and FALSE is none: the walk reads it as no term" );

    return array_values ( array_filter ( $pqParm, 'is_scalar' ) );

  }

  return padGetList ( $pqParm );

?>
