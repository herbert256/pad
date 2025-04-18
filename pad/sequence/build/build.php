<?php

  if ( $pqBuildName and $pqBuildName !== TRUE )
    include 'sequence/build/given.php';
    
  $pqInfo ['sequences'] [] = $pqSeq;
  $pqInfo ['builds']    [] = $pqBuild;

  $pqParmStore = ( $pqParm and isset ( $pqStore [$pqParm] ) ) ? $pqParm : '';

  include "sequence/build/include.php";
  include "sequence/build/types/$pqBuild.php";
  
?>
