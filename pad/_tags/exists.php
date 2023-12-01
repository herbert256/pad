<?php

  if ( padTagParm ('pad') ) $file_exists = pad    . $padOpt [$pad] [1];
  else                      $file_exists = padApp . $padOpt [$pad] [1];

  return ( file_exists ($file_exists) ) ? TRUE : FALSE;

?>