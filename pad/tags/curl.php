<?php

  if ( ! isset ($padPrmsTag [$pad] ['url']) )
    $padPrmsTag [$pad] ['url'] = $padPrm [$pad];

  if ( ! $padPrmsTag [$pad] ['url'] )
    return padError ("Curl: No URL given");

  $padReturn = padCurl ( $padPrmsTag [$pad]);

  return $padReturn ['data'];

?>