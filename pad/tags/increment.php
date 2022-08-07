<?php

  $pField = pField_name ($pPrms);
  
  if ( isset ($GLOBALS[$pField]) )
    $GLOBALS[$pField]++;
  else
    $GLOBALS[$pField] = 1;
  
  return TRUE;
  
?>