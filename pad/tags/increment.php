<?php

  $padField = pField_name ($padPrms[$pad]);
  
  if ( isset ($GLOBALS[$padField]) )
    $GLOBALS[$padField]++;
  else
    $GLOBALS[$padField] = 1;
  
  return TRUE;
  
?>