<?php

  $pStore_name = $pPrmsTag[$p] ['toFlag'];

  if     ( $pNull[$p]  ) $pFlagStore [$pStore_name] = FALSE;
  elseif ( $pElse[$p]  ) $pFlagStore [$pStore_name] = FALSE;
  elseif ( trim ( $pResult[$p] ) <> '' ) $pFlagStore [$pStore_name] = TRUE;
  else                                             $pFlagStore [$pStore_name] = FALSE;

  $pResult[$p] = '';
  
?>