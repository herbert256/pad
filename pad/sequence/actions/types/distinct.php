<?php

  // distinct - collapses the sequence to the number of different values it holds, as a
  // single entry under $pqActionKey. dedup keeps those values themselves instead.
  //
  // Values are told apart by pqValueKey (), as dedup, unique and merge tell them:
  // array_count_values takes only strings and integers, and a sequence of floats - add=0.5 -
  // ended the request.

  $pqResult = [ $pqActionKey => count ( array_unique ( array_map ( 'pqValueKey', $pqResult ) ) ) ];

?>
