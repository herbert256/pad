<?php

  $padSeqSeq = $padTag [$pad];

  if ( file_exists ( PAD . "seq/types/$padSeqSeq/flags/parm") )
    $padSeqParm = $padParm; 
  else
    $padSeqParm = ''; 

  include 'seq/inits/seq/seq.php';

?>