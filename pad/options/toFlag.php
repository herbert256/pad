<?php

  $pStore_name = $pPrms_tag ['toFlag'];

  if     ( $pParms [$pad] ['null']   ) $pFlag_store [$pStore_name] = FALSE;
  elseif ( $pParms [$pad] ['else']   ) $pFlag_store [$pStore_name] = FALSE;
  elseif ( trim ( $pResult [$pad] ) <> '' ) $pFlag_store [$pStore_name] = TRUE;
  else                                             $pFlag_store [$pStore_name] = FALSE;

  $pResult [$pad] = '';
  
?>