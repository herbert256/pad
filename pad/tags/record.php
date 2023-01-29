<?php

  if ($padTag [$pad] == 'check' ) 
    return db ( $padTag [$pad] . ' ' . $padPrm [$pad] [0] ) ? TRUE : FALSE;
  else                  
    return db ( $padTag [$pad] . ' ' . $padPrm [$pad] [0] );

?>