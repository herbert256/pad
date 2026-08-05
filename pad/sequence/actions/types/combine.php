<?php

  // combine='seq', and merge='seq' which delegates here - folds each named store into the
  // current sequence, walking the two in step and always taking the smaller value first,
  // so two ascending sequences come out ascending. combine keeps duplicates; merge (any
  // $pqAction other than 'combine') skips a value that is already in the result. The
  // result is renumbered.

  foreach ( $pqActionList as $pqMergeKey ) {

    $pqMerge1 = $pqResult;
    $pqMerge2 = $pqStore [$pqMergeKey];

    $pqResult = [];

    $pqMerge1Val = reset ($pqMerge1);
    $pqMerge2Val = reset ($pqMerge2);

    while ( $pqMerge1Val !== FALSE or $pqMerge2Val !== FALSE) {

      if ( $pqMerge1Val !== FALSE and $pqMerge2Val === FALSE ) {
        if ($pqAction == 'combine' or ! in_array($pqMerge1Val, $pqResult) )
          $pqResult [] = $pqMerge1Val;
        $pqMerge1Val = next ($pqMerge1);
      } elseif ( $pqMerge1Val === FALSE and $pqMerge2Val !== FALSE ) {
        if ($pqAction == 'combine' or ! in_array($pqMerge2Val, $pqResult) )
          $pqResult [] = $pqMerge2Val;
        $pqMerge2Val = next ($pqMerge2);
      } elseif ( $pqMerge1Val < $pqMerge2Val ) {
        if ($pqAction == 'combine' or ! in_array($pqMerge1Val, $pqResult) )
          $pqResult [] = $pqMerge1Val;
        $pqMerge1Val = next ($pqMerge1);
      } else {
        if ($pqAction == 'combine' or ! in_array($pqMerge2Val, $pqResult) )
          $pqResult [] = $pqMerge2Val;
        $pqMerge2Val = next ($pqMerge2);
      }

    }

  }

?>