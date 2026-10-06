<?php

  // Reads a parenthesised literal list - ( 'a', $b, 2 * 3 ) - and returns it as a PAD data
  // array. Drops the outer parentheses, splits on commas and runs every element through
  // padEval, so entries may be expressions rather than plain literals. Included by
  // padData() as data/<type>.php; padContentType picks 'list' when the text both opens
  // with ( and closes with ).
  //
  // The elements are split at the commas outside quotes and brackets, as the values of a
  // {when} are: split at every comma, ( 'a, b', 'c' ) was cut inside its string and the
  // halves were no expressions, and ( max(1, 2), 3 ) inside its call.

  $result = [];

  foreach ( padParseOptions ( substr ( $data, 1, -1 ) ) as $value ) {

    $value = trim ( str_replace ( '&comma;', ',', $value ) );

    if ( $value !== '' )
      $result [] = padEval ( $value );

  }

  return $result;

?>
