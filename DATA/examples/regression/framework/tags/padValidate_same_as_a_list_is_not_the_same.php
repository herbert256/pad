<?php

  // same: compares with another field of the data, and that field can arrive as a list -
  // password[]=p1 - which is not the same as any text: the comparison read the list as a
  // text, and the Array to string conversion ended the request.

  $validated = padValidate ( [ 'again' => 'same:password' ], [ 'password' => [ 'p1' ], 'again' => 'p1' ] );

  $result = '';

  foreach ( $validated as $field => $message )
    $result .= "$field: $message. ";

?>
