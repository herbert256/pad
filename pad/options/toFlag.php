<?php

  $pStore_name = $pPrms_tag ['toFlag'];

  if     ( $pParms [$p] ['null']   ) $pFlag_store [$pStore_name] = FALSE;
  elseif ( $pParms [$p] ['else']   ) $pFlag_store [$pStore_name] = FALSE;
  elseif ( trim ( $pResult [$p] ) <> '' ) $pFlag_store [$pStore_name] = TRUE;
  else                                             $pFlag_store [$pStore_name] = FALSE;

  $pResult [$p] = '';
  
?>