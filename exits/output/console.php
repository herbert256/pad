<?php

  if ( ! $padOutput or isset ( $padSent ) )
    return;

  echo $padOutput;

  $padSent = TRUE;

  padExit ();
  
?>