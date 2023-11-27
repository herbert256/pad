<?php

  if ( ! $padOutput or $padSent )
    return;

  echo $padOutput;

  $padSent = TRUE;

  padExit ();
  
?>