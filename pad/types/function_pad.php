<?php

  if ( $pad_walk == 'start' and $pPrms_type == 'close' ) {
    $pad_walk = 'end';
    return TRUE;
  }
   
  return pFunction_in_tag ( $pType, $pTag, $pContent, $pPrms_val );

?>