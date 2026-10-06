<?php

  // distinct - collapses the sequence to the number of different values it holds, as a
  // single entry under $pqActionKey. dedup keeps those values themselves instead.
  //
  // Values are told apart by their string form: array_count_values takes only strings and
  // integers, and a sequence of floats - add=0.5 - ended the request.

  $pqResult = [ $pqActionKey => count ( array_unique ( array_map ( 'strval', $pqResult ) ) ) ];

?>
