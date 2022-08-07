<?php

  $pStore_name = $pPrmsTag[$p] ['toFlag'];

  if     ( $pNull[$p]  ) $pFlag_store [$pStore_name] = FALSE;
  elseif ( $pElse[$p]  ) $pFlag_store [$pStore_name] = FALSE;
  elseif ( trim ( $pResult[$p] ) <> '' ) $pFlag_store [$pStore_name] = TRUE;
  else                                             $pFlag_store [$pStore_name] = FALSE;

  $pResult[$p] = '';
  
?>