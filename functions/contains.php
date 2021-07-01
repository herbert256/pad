<?php

#$GLOBALS['xxxparm'] = $parm[0];
#$GLOBALS['xxxvalue'] = $value;

#dxx();

  if ( strpos($value, $parm[0]) !== FALSE )
    return TRUE;
  else
    return FALSE;

?>