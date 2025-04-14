<?php

  if ( $padSeqBuildName and  $padSeqBuildName !== TRUE )
    include 'sequence/build/given.php';
    
  $padSeqInfo ['sequences'] [] = $padSeqSeq;
  $padSeqInfo ['builds']    [] = $padSeqBuild;

  $padSeqParmStore = ( $padSeqParm and isset ( $padSeqStore [$padSeqParm] ) ) ? $padSeqParm : '';

  include "sequence/build/include.php";
  include "sequence/build/types/$padSeqBuild.php";
  
?>
