<?php

  // An order: the quantity comes from <pad-stepper>, a form-associated custom element whose
  // value the {form} posts like any field; the size from an {input}. padValidate checks both
  // on the server - the stepper lets the visitor go to 12, the rule stops at 10.

  $errors  = [];
  $ordered = FALSE;

  if ( padPosted ( 'order' ) ) {
    $errors  = padValidate ( [ 'qty' => 'required|integer|min:1|max:10', 'size' => 'required|in:s,m,l' ],
                             NULL, [ 'qty.max' => 'At most :n at a time', 'qty' => 'The quantity is a number from 1 to 10' ] );
    $ordered = ! $errors;
  }

  $qty      = (int) padRequest ( 'qty', 2 );
  $size     = (string) padRequest ( 'size', '' );
  $qtyError = $errors ['qty'] ?? '';

?>
