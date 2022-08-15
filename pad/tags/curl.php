<?php

  if ( ! isset ($padPrmsTag [$pad] ['url']) )
    $padPrmsTag [$pad] ['url'] = $padPrm [$pad];

  if ( ! $padPrmsTag [$pad] ['url'] )
    return pError ("Curl: No URL given");

  $padReturn = pCurl ( $padPrmsTag [$pad]);

  return $padReturn ['data'];

?>