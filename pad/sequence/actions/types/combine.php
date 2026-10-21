<?php

  // combine='seq', and merge='seq' which delegates here - folds each named store into the
  // current sequence in value order: both are put in order first, then walked in step, the
  // smaller value taken first. combine keeps duplicates; merge (any $pqAction other than
  // 'combine') skips a value that is already in the result. The result is renumbered.
  //
  // Walked as they came, two lists not in order came out in no order - merge of [3, 1] and
  // [2, 0] gave 2 0 3 1 - and a FALSE among the values ended the walk, the end of a list and
  // a value being the one answer of reset () and next ().
  //
  // merge keeps a set of the values in the result, $pqMergeSeen: looked up in the result
  // itself, every value was compared with all the values before it, and two stores of
  // 80,000 and 40,000 values took 2.7 seconds to merge, of 160,000 and 80,000 longer than
  // the request could run.
  //
  // A name that is not a store is skipped, as actions/merge.php does for the merge family:
  // read as one, it was an undefined key that ended the request.

  foreach ( $pqActionList as $pqMergeKey ) {

    if ( ! isset ( $pqStore [$pqMergeKey] ) )
      continue;

    $pqMerge1 = array_values ( $pqResult );
    $pqMerge2 = array_values ( $pqStore [$pqMergeKey] );

    sort ( $pqMerge1 );
    sort ( $pqMerge2 );

    $pqResult    = [];
    $pqMergeSeen = [];

    $pqMergeOne = 0;
    $pqMergeTwo = 0;
    $pqMergeEnd1 = count ( $pqMerge1 );
    $pqMergeEnd2 = count ( $pqMerge2 );

    while ( $pqMergeOne < $pqMergeEnd1 or $pqMergeTwo < $pqMergeEnd2 ) {

      if ( $pqMergeTwo >= $pqMergeEnd2 or ( $pqMergeOne < $pqMergeEnd1 and $pqMerge1 [$pqMergeOne] < $pqMerge2 [$pqMergeTwo] ) )
        $pqMergeVal = $pqMerge1 [$pqMergeOne++];
      else
        $pqMergeVal = $pqMerge2 [$pqMergeTwo++];

      if ( $pqAction != 'combine' ) {
        $pqMergeAt = pqValueKey ( $pqMergeVal );
        if ( isset ( $pqMergeSeen [$pqMergeAt] ) )
          continue;
        $pqMergeSeen [$pqMergeAt] = TRUE;
      }

      $pqResult [] = $pqMergeVal;

    }

  }

?>
