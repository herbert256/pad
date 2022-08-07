<?php

  if ( $pad_walk == 'start' and $pPrms_type == 'close' ) {
    $pad_walk = 'end';
    return TRUE;
  }

  $pTag = $pParm;

  if ( isset ($pPrms_tag ['type'] ) )                     
    $pFunction_type = "function_" . $pPrms_tag ['type'];                
  else
    $pFunction_type = "function_" . pFunction_type ($pParm);

  $pFunction_val = $pPrms_val;
  unset ( $pFunction_val [ array_key_first ($pFunction_val) ] );

  return pFunction_in_tag ( $pFunction_type, $pParm, $pContent, $pFunction_val );

?>