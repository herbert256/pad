<?php

  if ( ! isset ($pPrms_tag ['url']) )
    $pPrms_tag ['url'] = $pParm;

  if ( ! $pPrms_tag ['url'] )
    return pError ("Curl: No URL given");

  $pReturn = pCurl ( $pPrms_tag);

  return $pReturn ['data'];

?>