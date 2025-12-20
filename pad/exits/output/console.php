<?php

  if ( ! $padOutput or isset ( $padSent ) )
    return;

  echo trim($padOutput) . "\n";

  $padSent = TRUE;

  padExit ();

?>
