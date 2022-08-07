<?php

  if ( $pWalk == 'start' and $pPrmsType == 'close' ) {
    $pWalk = 'end';
    return TRUE;
  }

  $pTag[$p]= $pPrm[$p];

  if ( isset ($pPrmsTag[$p] ['type'] ) )                     
    $pFunction_type = "function_" . $pPrmsTag[$p] ['type'];                
  else
    $pFunction_type = "function_" . pFunction_type ($pPrm[$p]);

  $pFunction_val = $pPrmsVal[$p];
  unset ( $pFunction_val [ array_key_first ($pFunction_val) ] );

  return pFunction_in_tag ( $pFunction_type, $pPrm[$p], $pContent, $pFunction_val );

?>