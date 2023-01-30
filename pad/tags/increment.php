<?php

  $padField = padFieldName ($padPrm [$pad] [1]);
  
  if ( isset ($GLOBALS[$padField]) )
    $GLOBALS[$padField]++;
  else
    $GLOBALS[$padField] = 1;
  
  return TRUE;
  
?>