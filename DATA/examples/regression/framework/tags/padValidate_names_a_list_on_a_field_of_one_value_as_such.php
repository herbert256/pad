<?php

  // A list on a field of one value - a key without [] - is refused with a message that says
  // so, "must be a single value", where the field's first rule answered for it: a list of
  // two good choices read "Tags is required". A key named for a list keeps required and in:
  // per item, and its message names the field without the brackets: "Picks", not "Picks[]".

  $validated = padValidate (
    [ 'tags' => 'required|in:a,b', 'picks[]' => 'required|in:a,b', 'name' => 'email', 'ok[]' => 'in:a,b' ],
    [ 'tags' => [ 'a', 'b' ], 'picks' => [ 'a', 'z' ], 'name' => [ 'x' ], 'ok' => [ 'a', 'b' ] ] );

  $result = '';

  foreach ( $validated as $field => $message )
    $result .= "$field: $message. ";

?>
