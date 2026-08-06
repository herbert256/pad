<?php

  // average - collapses the sequence to the mean of its values, as a single entry under
  // $pqActionKey. There is no mean of nothing, so an empty sequence is left empty rather
  // than divided by its own count.

  if ( ! count ( $pqResult ) )
    return;

  $pqResult = [ $pqActionKey => array_sum ( $pqResult ) / count ( $pqResult ) ];

?>