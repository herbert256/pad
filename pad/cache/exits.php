<?php

  // Stores the finished page in the server cache, run from exits/exits.php.
  //
  // When the page hashes to the ETag already on file only that entry's timestamp is
  // refreshed; otherwise the stale entry is dropped and the new body stored under the new
  // ETag, gzipped when $padCacheServerGzip is on. The padCache* functions come from the
  // backend that cache/inits.php loaded.
  //
  // The store holds the body and nothing about it, and a hit sends the configured content
  // type. A page that chose its own type is therefore not stored, and an older copy of it
  // is dropped: it was stored, and a JSON page went out as application/json the first time
  // and as text/html from the first hit on - the same bytes, the same ETag.

  if ( $padContentType !== ( $padCacheContentType ?? $padContentType ) ) {

    if ( $padCacheEtag )
      padCacheDelete ( $padCacheUrl, $padCacheEtag );

    return;

  }

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