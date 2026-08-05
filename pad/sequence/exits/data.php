<?php

  // Publishes the finished terms as a named PAD data set when the tag asked for toData=,
  // so later tags can address them as data. Reads $pqToData and $pqResult, writes
  // $padDataStore[$pqToData]. When a pop/shift action ate from a pulled store, what is
  // left of that store ($pqStore[$pqPull]) is published instead of the returned terms.

  if ( ! $pqToData )
    return;

  if ( $pqPull and ( isset ($pqPop) or isset ($pqShift) ) )
    $padDataStore [$pqToData] = padData ( $pqStore [$pqPull], '', $pqToData );
  else
    $padDataStore [$pqToData] = padData ( $pqResult,              '', $pqToData );

?>