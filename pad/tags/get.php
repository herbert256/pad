<?php

  $pGet_app     = $pPrmsVal [$p] [0] ?? pTag_parm ('include');
  $pGet_page    = $pPrmsVal [$p] [1] ?? pTag_parm ('page');
  $pGet_query   = $pPrmsVal [$p] [2] ?? pTag_parm ('parms');
  $pGet_include = $pPrmsVal [$p] [3] ?? pTag_parm ('include');

  return pad ( $pGet_app, $pGet_page, $pGet_query, $pGet_include ) 
 
?>