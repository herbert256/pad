<?php

  $padPrmsTag [$pad] ['url']   = $padLocation . trim($padPrm [$pad]);
  $padPrmsTag [$pad] ['cache'] = FALSE;

  $padPrmsTag [$pad] ['cookies'] ['PADSESSID'] = $GLOBALS ['PADSESSID'];   
  $padPrmsTag [$pad] ['cookies'] ['PADREQID']  = $GLOBALS ['PADREQID'];    

  if ( ! pTag_parm ('complete') )
    $padPrmsTag [$pad] ['get'] ['pInclude'] = 1;

  return include PAD . 'tags/curl.php';

?>