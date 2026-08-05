<?php

  // element=N - reduces the sequence to its Nth entry, counted from 1 in the current
  // order whatever the real keys are, and re-keyed to $pqActionKey. Without a parameter
  // the sequence is left untouched.

  if ( $pqActionParm )
  $pqResult = [ $pqActionKey => array_combine (
    range (1, count($pqResult)),
    array_values($pqResult)) [$pqActionParm] ];

?>