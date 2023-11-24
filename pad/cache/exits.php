<?php
  
  if ( $padEtag == $padCacheEtag )
  
    padCacheUpdate ($padCacheUrl, $padEtag);

  else {

    if ($padCacheEtag)
      padCacheDelete ($padCacheUrl, $padCacheEtag);

    if ( $padCacheServerGzip and ! $padClientGzip )
      padCacheStore ($padCacheUrl, $padEtag, padZip($padOutput));
    else
      padCacheStore ($padCacheUrl, $padEtag, $padOutput);
    
  }

?>