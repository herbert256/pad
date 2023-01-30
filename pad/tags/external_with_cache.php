<?php

  if ( ! isset ($padPrm [$pad] ['url']) )
    $padPrm [$pad] ['url'] = $padPrm [$pad] [1];

  if ( ! $padPrm [$pad] ['url'] )
    return padError ("Curl: No URL given");

  $padReturn = padCurl ( $padPrm [$pad]);

  return $padReturn ['data'];

?>