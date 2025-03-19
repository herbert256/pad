<?php

  include 'seq/inits/short/parms.php';
  
  $padPrmName     = $padTag [$pad];
  $padPrmValue    = $padOpt [$pad] [2] ?? ''; 

  include "seq/one/one.php";

  $padSeqStartArray = $padSeqResult;

  include 'seq/inits/short/get.php';

?>