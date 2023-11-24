<?php

  if ( padTagParm ('pad') ) $padExists = pad    . $padOpt [$pad] [1];
  else                      $padExists = padApp . $padOpt [$pad] [1];

  return ( padExists ($padExists) ) ? TRUE : FALSE;

?>