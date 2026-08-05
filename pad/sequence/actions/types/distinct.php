<?php

  // distinct - collapses the sequence to the number of different values it holds, as a
  // single entry under $pqActionKey. dedup keeps those values themselves instead.

  $pqResult = [ $pqActionKey => count ( array_count_values ( $pqResult ) ) ];

?>