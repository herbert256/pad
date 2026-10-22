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
  // outlives the run, so leaving them there grew it by one entry per direct call. Only the
  // ones this run's actions name: every __action__ store went, so an expression's action run
  // inside a tag's build - sequence:sort([1,2]) in an eval= - dropped the lists the tag's own
  // actions were given, append=[9, 8] appended nothing and intersection=[3, 4] ended the page
  // on "'__action__7VjajPJV' is none".

  if ( ! count ( $pqActions ) )
    return;

  include PQ . 'actions/assoc.php';

  // The actions are a list in the order written, each one run once per mention: kept as a map
  // by name, {sequence '1..5', reverse, sort, reverse} ran reverse once, with its last value,
  // at the place of the first - where the strict check excuses a repeated action because it
  // applies once per mention. The columns of $pqActionsHit stay one per name.

  // The page is cut where page= stands among the actions, $pqPageCut (inits/page.php): after
  // the actions written before it, before the ones after it.

  $pqActionNth = 0;

  foreach ( $pqActions as [ $pqAction, $pqActionParm ] ) {

    if ( $pqPageCut and $pqActionNth++ == $pqPageCut ) {
      if ( include PQ . 'exits/short.php' )
        return;
      include PQ . 'exits/page.php';
    }

    $pqActionStart = $pqResult;
    $pqActionList  = padExplode ( $pqActionParm, '|' );
    $pqActionParm  = $pqActionList [0] ?? '';

    if ( str_contains ( $pqActionParm, '..' ) ) {
      pqRandomParm ( $pqActionParm );
      $pqActionList [0] = $pqActionParm;
    }

    // A count is a whole number from 0 up: first=2.5, last=-1 or pop='x' were read as 1 and
    // took one term without a word - pop took it out of the store. Strict mode names it, and
    // the sequence is left as it was, as element= and trim= leave it.

    if ( in_array ( $pqAction, [ 'first', 'last', 'shift', 'pop', 'minimum', 'maximum', 'randomize' ] )
         and (string) $pqActionParm !== '' and ! ctype_digit ( (string) $pqActionParm ) ) {

      if ( $GLOBALS ['padCheckSyntax'] ?? FALSE )
        padError ( "$pqAction= takes a count, not '$pqActionParm'" );

      $pqActionsHit [$pqAction] = array_values ( $pqResult );
      continue;

    }

    $pqActionCnt   = ( ctype_digit ( (string) $pqActionParm ) ) ? $pqActionParm : 1;
    $pqActionKey   = array_key_first ( $pqResult ) ?? 0;

    include PQ . 'actions/merge.php';
    include PA . "$pqAction.php";
    include PQ . 'actions/negative/negative.php';

    $pqActionsHit [$pqAction] = array_values ( $pqResult );

  }

  if ( $pqPageCut and $pqPageCut >= count ( $pqActions ) ) {
    if ( include PQ . 'exits/short.php' )
      return;
    include PQ . 'exits/page.php';
  }

  foreach ( $pqActions as [ $pqAction, $pqActionParm ] )
    foreach ( padExplode ( $pqActionParm, '|' ) as $pqStoreKey )
      if ( str_starts_with ( $pqStoreKey, '__action__' ) )
        unset ( $pqStore [$pqStoreKey] );

?>
