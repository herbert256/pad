<?php

  $pGet_app     = $pPrms_val [0] ?? pTag_parm ('include');
  $pGet_page    = $pPrms_val [1] ?? pTag_parm ('page');
  $pGet_query   = $pPrms_val [2] ?? pTag_parm ('parms');
  $pGet_include = $pPrms_val [3] ?? pTag_parm ('include');

  return pad ( $pGet_app, $pGet_page, $pGet_query, $pGet_include ) 
 
?>