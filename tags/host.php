<?php

  $padPrm [$pad] ['url']   = $padGoPageExternal . trim($padOpt [$pad] [1]);
  $padPrm [$pad] ['cache'] = FALSE;

  $padPrm [$pad] ['cookies'] ['padSesID'] = $GLOBALS ['padSesID'];   
  $padPrm [$pad] ['cookies'] ['padReqID'] = $GLOBALS ['padReqID'];    

  if ( ! padTagParm ('complete') )
    $padPrm [$pad] ['get'] ['padInclude'] = 1;

  return include 'curl.php';

?>