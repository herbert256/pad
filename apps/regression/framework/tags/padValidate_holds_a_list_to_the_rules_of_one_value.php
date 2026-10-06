<?php

  // A field posted as a list - color[]=evil where the form has one color field - passed
  // every rule but required: in, email, integer and accepted let it through, and the page
  // went on with a list its rules never looked at. A list now breaks every rule of a field
  // named for one value; a field named for a list - tags[], a multiple choice - holds every
  // item to in:.

  $validated = padValidate (
    [ 'color' => 'required|in:red,green',
      'email' => 'email',
      'qty'   => 'integer|max:5',
      'terms' => 'accepted',
      'tags[]'  => 'in:a,b,c',
      'picks[]' => 'required|in:a,b' ],
    [ 'color' => [ 'evil' ],
      'email' => [ 'x' ],
      'qty'   => [ '999' ],
      'terms' => [ 'yes' ],
      'tags'  => [ 'a', ' c ' ],
      'picks' => [ 'a', 'z' ] ] );

  $result = '';

  foreach ( $validated as $field => $message )
    $result .= "$field: $message. ";

?>
