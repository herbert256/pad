<?php

  if ( $pWalk [$p] == 'start' and $pPrmsType [$p] == 'close' ) {
    $pWalk [$p] = 'end';
    return TRUE;
  }
   
  return pFunction_in_tag ( $pType, $pTag [$p], $pContent, $pPrmsVal [$p] );

?>