<?php
  
  $padSeqInfo ['types']  [] = $padSeqSeq;
  $padSeqInfo ['builds'] [] = $padSeqBuild;

  include PAD . "seq/build/include.php";
  include PAD . "seq/build/types/$padSeqBuild.php";
  
?>