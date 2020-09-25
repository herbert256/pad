<?php

  $pad_field = pad_field_name ($pad_parm);

  if ( isset ($GLOBALS[$pad_field]) )
    $GLOBALS[$pad_field]--;
  else
    $GLOBALS[$pad_field] = -1;
  
  return TRUE;
  
?>