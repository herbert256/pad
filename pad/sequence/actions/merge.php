<?php

  // For the merge-family actions - those flagged by a marker file in actions/merge/,
  // namely dedup, reverse and sort - any stores named in the parameter are appended to
  // $pqResult before the action runs, so it works on the union: {pull:a sort='b'} sorts
  // a and b together.

  if ( count ( $pqActionList ) and file_exists ( PQ . "actions/merge/$pqAction" ) )
    foreach ( $pqActionList as $pqMerge )
      $pqResult = array_merge ( $pqResult, $pqStore [$pqMerge] );

?>