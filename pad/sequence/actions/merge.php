<?php

  // For the merge-family actions - those flagged by a marker file in actions/merge/,
  // namely dedup, reverse and sort - any stores named in the parameter are appended to
  // $pqResult before the action runs, so it works on the union: {pull:a sort='b'} sorts
  // a and b together.
  //
  // A name that is not a store is skipped: merging one in took it as an array and ended
  // the request, so a mistyped sort='b' cost the page rather than the union.

  if ( count ( $pqActionList ) and file_exists ( PQ . "actions/merge/$pqAction" ) )
    foreach ( $pqActionList as $pqMerge )
      if ( isset ( $pqStore [$pqMerge] ) )
        $pqResult = array_merge ( $pqResult, $pqStore [$pqMerge] );

?>