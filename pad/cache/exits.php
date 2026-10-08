<?php

  // Stores the finished page in the server cache, run from exits/exits.php.
  //
  // When the page hashes to the ETag already on file only that entry's timestamp is
  // refreshed; otherwise the stale entry is dropped and the new body stored under the new
  // ETag, gzipped when $padCacheServerGzip is on. The padCache* functions come from the
  // backend that cache/inits.php loaded.
  //
  // The store holds the body and nothing about it, and a hit sends the configured content
  // type and status. A page a hit could not reproduce is therefore not stored, and an
  // older copy of it is dropped (padCacheStorable): it was stored, and a JSON page went
  // out as application/json the first time and as text/html from the first hit on - the
  // same bytes, the same ETag.

  // A page-cache hit that rendered the page's {nocache} parts leaves the entry as it is:
  // stored again, its age would start over on every hit and it would never go stale.

  if ( $padNocacheHit )
    return;

  // A page with {nocache} parts is stored as its text with the parts as their source, under
  // an ETag no response carries (lib/nocache.php); any other page as it went out.

  if ( $padNocacheKept ) {
    $padCacheBody     = padNocachePack ( $padNocacheBody, $padNocacheKept );
    $padCacheBodyEtag = padNocacheEtag ( $padCacheBody );
  } else {
    $padCacheBody     = $padOutput;
    $padCacheBodyEtag = $padEtag;
  }

  if ( ! padCacheStorable ( $padNocacheKept ? $padNocacheBody : $padOutput ) ) {

    if ( $padCacheEtag )
      padCacheDelete ( $padCacheUrl, $padCacheEtag );

    return;

  }

  if ( $padCacheBodyEtag == $padCacheEtag )

    padCacheUpdate ($padCacheUrl, $padCacheBodyEtag);

  else {

    if ($padCacheEtag)
      padCacheDelete ($padCacheUrl, $padCacheEtag);

    if ( $padCacheServerGzip )
      padCacheStore ($padCacheUrl, $padCacheBodyEtag, padZip($padCacheBody));
    else
      padCacheStore ($padCacheUrl, $padCacheBodyEtag, $padCacheBody);

  }

  // The file and database backends keep what they store until it is deleted; memcached,
  // redis and APCu expire it themselves. Once per age window the stale entries go.

  if ( function_exists ( 'padCachePurge' ) )
    padCachePurge ( $padCacheMax );

?>
