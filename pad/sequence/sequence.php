<?php

  include 'sequence/inits/inits.php';
  include 'sequence/build/build.php'; 
  include 'sequence/exits/exits.php';

  if     ( $pqContinue               ) return NULL;
  elseif ( count ( $padData [$pad] ) ) return TRUE; 
  else                                 return FALSE;

?>