<?php

  // A rule name is read in any case - Integer is integer - and so is the one that makes
  // min: and max: compare the value rather than its length: with Integer|max:10, 99999 was
  // held to five characters, not to ten, and passed.

  $validated = padValidate (
    [ 'qty'   => 'Integer|max:10',
      'price' => 'NUMERIC|min:5',
      'code'  => 'max:3' ],
    [ 'qty' => '99999', 'price' => '4.5', 'code' => '1234' ] );

  $result = '';

  foreach ( $validated as $field => $message )
    $result .= "$field: $message. ";

?>
