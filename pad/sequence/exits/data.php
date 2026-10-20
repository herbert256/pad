<?php

  // Publishes the finished terms as a named PAD data set when the tag asked for toData=,
  // so later tags can address them as data. Reads $pqToData and $pqResult, writes
  // $padDataStore[$pqToData].

  if ( ! $pqToData )
    return;

  // The terms the tag shows, after a shift or pop as well - push= and name= keep those, and the
  // store taken from keeps the rest: the rest was published, {pull:a shift=2, toData='d'} over
  // 1..6 iterated 1 2 and published 3 4 5 6.
  //
  // The terms renumbered: after an action they still had the 'x' keys actions/assoc.php marks
  // them with for order/ and negative/, and the data set's keys read x2 x1 x0.

  $padDataStore [$pqToData] = padData ( array_values ( $pqResult ), '', $pqToData );

?>
