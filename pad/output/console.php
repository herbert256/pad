<?php

  if ( ! $padOutput or $padSent )
    return;

  if ( $padCacheStop and $padCacheServerGzip )
    echo padUnzip ( $padOutput );
  else 
    echo $padOutput;

  $padSent = TRUE;

  padExit ();
  
?>