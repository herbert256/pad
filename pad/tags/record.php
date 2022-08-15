<?php

  if ($padTag [$pad] == 'check' ) 
    return db ( $padTag [$pad] . ' ' . $padPrm[$pad] ) ? TRUE : FALSE;
  else                  
    return db ( $padTag [$pad] . ' ' . $padPrm[$pad] );

?>