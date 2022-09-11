<?php

  $padPrmsTag [$pad] ['url']   = $padLocation . trim($padPrm [$pad]);
  $padPrmsTag [$pad] ['cache'] = FALSE;

  $padPrmsTag [$pad] ['cookies'] ['PADSESSID'] = $GLOBALS ['PADSESSID'];   
  $padPrmsTag [$pad] ['cookies'] ['PADREQID']  = $GLOBALS ['PADREQID'];    

  if ( ! padTagParm ('complete') )
    $padPrmsTag [$pad] ['get'] ['padInclude'] = 1;

  return include 'curl.php';

?>