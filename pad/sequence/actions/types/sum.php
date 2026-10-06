<?php

  // sum - collapses the sequence to the total of all its values, as a single entry under
  // $pqActionKey.

  // Only the numbers among the values are added up: a word - a list or a store can hold one -
  // ended the request on PHP's "Addition is not supported on type string".

  $pqResult = [ $pqActionKey => array_sum ( array_filter ( $pqResult, 'is_numeric' ) ) ];

?>
