<?php

  // combine='seq', and merge='seq' which delegates here - folds each named store into the
  // current sequence, walking the two in step and always taking the smaller value first,
  // so two ascending sequences come out ascending. combine keeps duplicates; merge (any
  // $pqAction other than 'combine') skips a value that is already in the result. The
  // result is renumbered.
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

    $pqMerge1 = $pqResult;
    $pqMerge2 = $pqStore [$pqMergeKey];

    $pqResult    = [];
    $pqMergeSeen = [];

    $pqMerge1Val = reset ($pqMerge1);
    $pqMerge2Val = reset ($pqMerge2);

    while ( $pqMerge1Val !== FALSE or $pqMerge2Val !== FALSE ) {

      if ( $pqMerge2Val === FALSE or ( $pqMerge1Val !== FALSE and $pqMerge1Val < $pqMerge2Val ) ) {
        $pqMergeVal  = $pqMerge1Val;
        $pqMerge1Val = next ( $pqMerge1 );
      } else {
        $pqMergeVal  = $pqMerge2Val;
        $pqMerge2Val = next ( $pqMerge2 );
      }

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
