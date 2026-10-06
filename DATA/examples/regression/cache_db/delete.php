<?php

  // A deleted entry takes the URL's entry with it: cache/inits.php answers a client's
  // If-None-Match with 304 from the ETag the URL names, so a URL still naming a deleted
  // ETag answered 304 for a page that was no longer stored. The backend is asked directly,
  // under a key of its own, so the answer does not depend on what the crawl left behind.

  $deleteUrl  = padMD5 ( 'delete ' . hrtime ( TRUE ) );
  $deleteEtag = padMD5 ( 'delete etag ' . hrtime ( TRUE ) );

  padCacheStore ( $deleteUrl, $deleteEtag, 'the body' );

  $deleteBefore = padCacheUrl ( $deleteUrl );

  padCacheDelete ( $deleteUrl, $deleteEtag );

  $deleteAfter = padCacheUrl ( $deleteUrl );

  $deleteNamed = ( ( $deleteBefore ['etag'] ?? $deleteBefore [1] ?? '' ) === $deleteEtag ) ? 'yes' : 'NO';
  $deleteGone  = ( ( $deleteAfter  ['etag'] ?? $deleteAfter  [1] ?? '' ) === ''          ) ? 'yes' : 'NO';

?>
