<?php

  if ($padTag [$pad] == 'check' ) 
    return db ( $padTag [$pad] . ' ' . $padOpt [$pad] [1] ) ? TRUE : FALSE;
  else                  
    return db ( $padTag [$pad] . ' ' . $padOpt [$pad] [1] );

?>