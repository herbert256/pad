<?php

  if ( $pWalk == 'start' and $pPrmsType == 'close' ) {
    $pWalk = 'end';
    return TRUE;
  }
   
  return pFunction_in_tag ( $pType, $pTag[$p], $pContent, $pPrmsVal[$p] );

?>