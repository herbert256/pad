<?php

  $padField = padFieldName ( $padOpt [$pad] [0] );
  
  if ( isset ( $GLOBALS [$padField] ) )
    $GLOBALS [$padField]++;
  else
    $GLOBALS [$padField] = 1;
  
  return TRUE;
  
?>