<?php
  
  $padSeqInfo ['types']  [] = $padSeqSeq;
  $padSeqInfo ['builds'] [] = $padSeqBuild;

  padDone ( $padSeqSeq );

  include "/pad/sequence/build/include.php";
  include "/pad/sequence/build/types/$padSeqBuild.php";

  $padSeqDone [] = $padSeqSeq;
 
?>