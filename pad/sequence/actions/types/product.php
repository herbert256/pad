<?php

  // product - collapses the sequence to the product of all its values, as a single entry
  // under $pqActionKey.

  // Only the numbers among the values are multiplied: a word - a list or a store can hold one
  // - ended the request on PHP's "Multiplication is not supported on type string".

  $pqResult = [ $pqActionKey => array_product ( array_filter ( $pqResult, 'is_numeric' ) ) ];

?>
