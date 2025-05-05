<?php

  if ( $padInfo ) {
    $pqInfo ['sequences'] [] = $pqSeq;
    $pqInfo ['build']     [] = $pqBuild;
  }

  $pqOrgName = $pqSeq;

  $pqParmStore = ( $pqParm and isset ( $pqStore [$pqParm] ) ) ? $pqParm : '';

?>