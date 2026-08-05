<?php

  // Works out which name this run stores under, from name=, push= and pull=:
  //
  //   $pqStoreName  the name the sequence is known by, falling back to 'default'
  //   $padLastPush  the store key actually written, and what a bare {pull} or {resume}
  //                 later picks up - it outlives the run (it is not a $pq* global, so
  //                 inits/clear.php leaves it alone)
  //
  // A bare push (push with no value) reuses the pulled name, so pull/push round-trips
  // stay on the same store.

  if     ( ! $pqNameGiven and ! $pqPull and ! $pqPush        ) $pqStoreName = 'default';
  elseif ( ! $pqNameGiven and ! $pqPull and $pqPush === TRUE ) $pqStoreName = 'default';
  elseif ( $pqNameGiven                                      ) $pqStoreName = $pqNameGiven;
  elseif ( $pqPush and $pqPush !== TRUE                      ) $pqStoreName = $pqPush;
  elseif ( $pqPull and $pqPull !== TRUE                      ) $pqStoreName = $pqPull;
  else                                                         $pqStoreName = $pqName;

  if ( $pqPush )
    if ( $pqPush === TRUE )
      if ( $pqPull )    $padLastPush = $pqPull;
      else              $padLastPush = $pqStoreName;
    else                $padLastPush = $pqPush;
  elseif ( $pqPull )    $padLastPush = $pqPull;
  else                  $padLastPush = $pqStoreName;

?>