<?php

  if ( $pad_walk == 'start' and $pPrmsType == 'close' ) {
    $pad_walk = 'end';
    return TRUE;
  }

  $pTag = $pParm [$p];

  if ( isset ($pPrmsTag [$p] ['type'] ) )                     
    $pFunction_type = "function_" . $pPrmsTag [$p] ['type'];                
  else
    $pFunction_type = "function_" . pFunction_type ($pParm [$p]);

  $pFunction_val = $pPrmsVal [$p];
  unset ( $pFunction_val [ array_key_first ($pFunction_val) ] );

  return pFunction_in_tag ( $pFunction_type, $pParm [$p], $pContent, $pFunction_val );

?>