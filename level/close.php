<?php

  if ( ! str_contains( $padOpt [$pad] [0], '}' ) )
    return;
 
  $padClosePad = padCode ( $padOpt [$pad] [0] );

  if ( $padClosePad == $padOpt [$pad] [0] )
    return;

  $padBetween = $padTag [$pad] . ' ' . $padClosePad;

  include '/pad/level/between.php';

?>