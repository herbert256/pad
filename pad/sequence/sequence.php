<?php

  include 'sequence/inits/inits.php';
  include 'sequence/build/build.php'; 
  include 'sequence/exits/exits.php';

  if     ( $pqContinue               ) $pqReturn = NULL;
  elseif ( count ( $padData [$pad] ) ) $pqReturn = TRUE; 
  else                                 $pqReturn = FALSE;

  return $pqReturn;

?>