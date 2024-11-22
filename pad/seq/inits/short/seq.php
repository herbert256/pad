<?php

  $padSeqSeq = $padTag [$pad];

  if ( file_exists ( "seq/types/$padSeqSeq/flags/parm") )
    $padSeqParm = $padParm; 
  else
    $padSeqParm = ''; 

  include 'seq/inits/seq/seq.php';

?>