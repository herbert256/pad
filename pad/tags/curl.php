<?php

  if ( ! isset ($pPrmsTag [$p] ['url']) )
    $pPrmsTag [$p] ['url'] = $pParm [$p];

  if ( ! $pPrmsTag [$p] ['url'] )
    return pError ("Curl: No URL given");

  $pReturn = pCurl ( $pPrmsTag [$p]);

  return $pReturn ['data'];

?>