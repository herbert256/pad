<?php

  if ( $pqRows ) 
    return;
  
  if     ( $pqTo <> PHP_INT_MAX   ) $pqRows = PHP_INT_MAX ;
  elseif ( $pqTry <> 10000        ) $pqRows = PHP_INT_MAX ;
  elseif ( $pqStop <> PHP_INT_MAX ) $pqRows = PHP_INT_MAX ;
  elseif ( $pqFixed !== FALSE     ) $pqRows = PHP_INT_MAX ;
  elseif ( pqStore ( $pqBuild )   ) $pqRows = PHP_INT_MAX ;
  else                              $pqRows = 25;

?>