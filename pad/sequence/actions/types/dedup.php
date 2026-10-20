<?php

  // dedup (alias unique) - drops repeated values, keeping the first occurrence of each
  // with its key. Flagged in actions/merge/, so a parameter naming stores dedups those
  // together with the current sequence.
  //
  // Values are told apart by pqValueKey (), as unique and merge tell them: array_unique ()
  // compared them as text, so 1 and '1.0' stayed two values that unique takes for one.

  $pqDedupSeen = [];

  foreach ( $pqResult as $padK => $padV ) {

    $pqDedupKey = pqValueKey ( $padV );

    if ( isset ( $pqDedupSeen [$pqDedupKey] ) )
      unset ( $pqResult [$padK] );
    else
      $pqDedupSeen [$pqDedupKey] = TRUE;

  }

?>
