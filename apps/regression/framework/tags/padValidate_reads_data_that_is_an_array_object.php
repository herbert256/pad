<?php

  // The data padValidate checks can be an object read like an array - an ArrayObject, a
  // model with ArrayAccess - its fields found by name, nested ones by their bracketed name
  // too. Since the fields are looked for where PHP files a posted name, only arrays were
  // read: every field of such data was empty, required failed and every other rule passed.

  $validated = padValidate (
    [ 'email' => 'required|email', 'user[age]' => 'integer', 'name' => 'required' ],
    new ArrayObject ( [ 'email' => 'x', 'user' => new ArrayObject ( [ 'age' => 'old' ] ), 'name' => 'Ann' ] ) );

  $result = '';

  foreach ( $validated as $field => $message )
    $result .= "$field: $message. ";

?>
