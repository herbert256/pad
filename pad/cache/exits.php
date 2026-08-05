<?php

  // Stores the finished page in the server cache, run from exits/exits.php.
  //
  // When the page hashes to the ETag already on file only that entry's timestamp is
  // refreshed; otherwise the stale entry is dropped and the new body stored under the new
  // ETag, gzipped when $padCacheServerGzip is on. The padCache* functions come from the
  // backend that cache/inits.php loaded.

  if ( $padEtag == $padCacheEtag )

    padCacheUpdate ($padCacheUrl, $padEtag);

  else {

    if ($padCacheEtag)
      padCacheDelete ($padCacheUrl, $padCacheEtag);

    if ( $padCacheServerGzip )
      padCacheStore ($padCacheUrl, $padEtag, padZip($padOutput));
    else
      padCacheStore ($padCacheUrl, $padEtag, $padOutput);

  }

?>