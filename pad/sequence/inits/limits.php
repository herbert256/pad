<?php

  if ( $pqPull or $pqStop <> PHP_INT_MAX or $pqTo <> PHP_INT_MAX or $pqBuild == 'fixed' )
    if ( ! $pqTry  ) $pqTry  = PHP_INT_MAX ;

  if ( $pqPull or $pqStop <> PHP_INT_MAX or $pqTo <> PHP_INT_MAX )
    if ( ! $pqRows ) $pqRows = PHP_INT_MAX ;

  if ( ! $pqTry  ) $pqTry  = $padSeqDefaultTries; 
  if ( ! $pqRows ) $pqRows = $padSeqDefaultRows;

?>