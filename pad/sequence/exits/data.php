<?php

  // Publishes the finished terms as a named PAD data set when the tag asked for toData=,
  // so later tags can address them as data. Reads $pqToData and $pqResult, writes
  // $padDataStore[$pqToData]. When a pop/shift action ate from a pulled store, what is
  // left of that store ($pqStore[$pqPull]) is published instead of the returned terms.

  if ( ! $pqToData )
    return;

  if ( $pqPull and $pqStoreUpdated )
    $padDataStore [$pqToData] = padData ( $pqStore [$pqPull], '', $pqToData );
  else
    $padDataStore [$pqToData] = padData ( array_values ( $pqResult ), '', $pqToData );

  // The terms renumbered: after an action they still had the 'x' keys actions/assoc.php marks
  // them with for order/ and negative/, and the data set's keys read x2 x1 x0.

?>
