<?php

  include 'isolate.php';

  if ( $padWalk [$pad] == 'start' )
    return padQuarantine ( $padContent, $padSet [$pad] );
  else
    return TRUE;

?>