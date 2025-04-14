<?php

  include 'sequence/inits/inits.php';
  include 'sequence/build/build.php'; 
  include 'sequence/exits/exits.php';

  if ( $padSeqContinue )
    return NULL;
  else
    return $padSeqReturn;

?>