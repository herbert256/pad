<?php

  // average - collapses the sequence to the mean of its values, as a single entry under
  // $pqActionKey. An empty sequence is a division by zero.

  $pqResult = [ $pqActionKey => array_sum ( $pqResult ) / count ( $pqResult ) ];

?>