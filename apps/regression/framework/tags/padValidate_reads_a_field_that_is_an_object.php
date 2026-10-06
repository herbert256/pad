<?php

  // A field of the data that is an object: one read like an array - an ArrayObject - is a
  // list, kept by tags[] and refused for a field of one value; one that is text - a
  // Stringable - is its text. Any object but a Stringable was cast to a text, and the
  // request ended on "could not be converted to string".

  $validated = padValidate (
    [ 'user' => 'required', 'tags[]' => 'required|in:a,b', 'name' => 'required|min:3' ],
    new ArrayObject ( [ 'user' => new ArrayObject ( [ 'age' => 1 ] ),
                        'tags' => new ArrayObject ( [ 'a', 'b' ] ),
                        'name' => new class { function __toString () { return 'Ann'; } } ] ) );

  $result = '';

  foreach ( $validated as $field => $message )
    $result .= "$field: $message. ";

?>
