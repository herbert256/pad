<?php

  $padPrm [$pad] ['url']   = $padPageExternal . trim($padPrm [$pad] [1]);
  $padPrm [$pad] ['cache'] = FALSE;

  $padPrm [$pad] ['cookies'] ['PADSESSID'] = $GLOBALS ['PADSESSID'];   
  $padPrm [$pad] ['cookies'] ['PADREQID']  = $GLOBALS ['PADREQID'];    

  if ( ! padTagParm ('complete') )
    $padPrm [$pad] ['get'] ['padInclude'] = 1;

  return include 'curl.php';

?>