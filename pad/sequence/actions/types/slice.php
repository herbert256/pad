<?php

  // slice='pos|len' - the array_slice of the sequence, offset and optional length taken
  // from the parameter list and handed to actions/function.php. The offset is 0-based and
  // may be negative to count back from the end.

  // An offset and a length, whole numbers both: a store name went to array_slice as the array
  // its name stands for and ended the request, as did slice='1|2|st' with the store as its
  // third argument, and slice=2.5 on PHP's deprecation of the fraction. Strict mode names
  // it, and the sequence is left as it was.

  foreach ( $pqActionList as $pqSliceAt => $pqSliceOne )
    if ( $pqSliceAt > 1 or ! pqBoolWhole ( $pqSliceOne ) ) {
      if ( $GLOBALS ['padCheckSyntax'] ?? FALSE )
        padError ( "slice= takes whole numbers, not '$pqSliceOne'" );
      return;
    }

  $pqFunction = 'array_slice';

  $pqResult = include PQ . 'actions/function.php';

?>
