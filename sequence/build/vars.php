<?php

  if ( $padInfo ) {
    $pqInfo ['sequences'] [] = $pqSeq;
    $pqInfo ['builds']    [] = $pqBuild;
  }

  if ( str_contains ( $pqParm, '..' ) and $pqSeq <> 'range' ) {
    $pqRandomParm = $pqParm;
    pqRandomParm ( $pqParm );
  } else
    $pqRandomParm = FALSE;

  $pqOrgName = $pqSeq;

  $pqParmStore = ( $pqParm and isset ( $pqStore [$pqParm] ) ) ? $pqParm : '';

?>