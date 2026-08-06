<?php

  // Applies every requested action to the finished sequence, in the order the options
  // were given; included from exits/actions.php once build/ has filled $pqResult.
  //
  // Per action it sets up the contract the actions/types/ handlers read: $pqActionStart
  // (snapshot of $pqResult before this action), $pqActionList (the parameter split on
  // '|'), $pqActionParm (the first parameter, with an 'a..b' form resolved to a random
  // number), $pqActionCnt (that parameter as a count, defaulting to 1) and $pqActionKey
  // (the first key, the one the aggregates collapse onto, 0 when there is no first key
  // because the sequence is empty). assoc.php marks the keys up
  // front so key survival can be detected afterwards, merge.php folds in named stores
  // for the merge-family actions, negative/ inverts the selection when asked. Each
  // action's result is kept in $pqActionsHit for {info} and the extra data columns.
  //
  // The __action__ stores actions/set.php parks array parameters in are dropped afterwards:
  // they are named only in the parameter of the action that has now run, and $pqStore
  // outlives the run, so leaving them there grew it by one entry per direct call.

  if ( ! count ( $pqActions ) )
    return;

  include PQ . 'actions/assoc.php';

  foreach ( $pqActions as $pqAction => $pqActionParm ) {

    $pqActionStart = $pqResult;
    $pqActionList  = padExplode ( $pqActionParm, '|' );
    $pqActionParm  = $pqActionList [0] ?? '';

    if ( str_contains ( $pqActionParm, '..' ) ) {
      pqRandomParm ( $pqActionParm );
      $pqActionList [0] = $pqActionParm;
    }

    $pqActionCnt   = ( ctype_digit ( (string) $pqActionParm ) ) ? $pqActionParm : 1;
    $pqActionKey   = array_key_first ( $pqResult ) ?? 0;

    include PQ . 'actions/merge.php';
    include PA . "$pqAction.php";
    include PQ . 'actions/negative/negative.php';

    $pqActionsHit [$pqAction] = array_values ( $pqResult );

  }

  foreach ( $pqStore as $pqStoreKey => $pqStoreOne )
    if ( str_starts_with ( $pqStoreKey, '__action__' ) )
      unset ( $pqStore [$pqStoreKey] );

?>