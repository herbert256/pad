<?php

  // Turning an array into the one value an operator needs, for eval/go/doubleArrArr.php and its
  // two mixed siblings.
  //
  // padEvalNumeric is the test the mixed pair uses: an array meets a scalar only where every
  // element of it, and the scalar, are numbers, and the array is then replaced by their sum.
  // Anything else - a string among them, or the nested rows a data name resolves to - has no
  // sum against a number and the operator is an error. An empty array passes, array_sum()
  // making it 0, that being the sum of nothing.
  //
  // padEvalReduce is the wider form, for two arrays, where there is no scalar to disagree with:
  // an array of numbers still becomes their sum, and anything else becomes its leaves run
  // together, which is what concatenating two arrays has to mean. Nested rows are walked to
  // their leaves, so the three rows a data name resolves to reduce the same way a flat list of
  // the same three values would.

  function padEvalNumeric ( $array ) {

    foreach ( $array as $one )
      if ( ! is_numeric ( $one ) )
        return FALSE;

    return TRUE;

  }

  function padEvalReduce ( $array ) {

    if ( padEvalNumeric ( $array ) )
      return array_sum ( $array );

    $flat = '';

    array_walk_recursive ( $array, function ( $one ) use ( &$flat ) { $flat .= $one; } );

    return $flat;

  }

?>