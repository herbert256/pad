<?php

  if ( $padInfo ) {
    $pqInfo ['sequences'] [] = $pqSeq;
    $pqInfo ['builds']    [] = $pqBuild;
  }

  $pqOrgName = $pqSeq;

  $pqParmStore = ( $pqParm and isset ( $pqStore [$pqParm] ) ) ? $pqParm : '';

?>