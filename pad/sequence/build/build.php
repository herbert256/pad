<?php
  
  $padSeqInfo ['sequences'] [] = $padSeqSeq;
  $padSeqInfo ['builds']    [] = $padSeqBuild;

  include "sequence/build/include.php";
  include "sequence/build/types/$padSeqBuild.php";
  
?>