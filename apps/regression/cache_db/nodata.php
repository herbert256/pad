<?php

  // With $padCacheServerNoData the backend keeps no body, but the URL still has to name the
  // ETag stored for it: cache/inits.php finds the ETag it answers a client's If-None-Match
  // with 304 for through the URL alone. The backend is asked directly, under a key of its
  // own, so the answer does not depend on what the crawl's page fetches left behind.

  $noDataKeep = $padCacheServerNoData;
  $noDataUrl  = padMD5 ( 'nodata ' . hrtime ( TRUE ) );
  $noDataEtag = padMD5 ( 'nodata etag ' . hrtime ( TRUE ) );

  $padCacheServerNoData = TRUE;

  padCacheStore ( $noDataUrl, $noDataEtag, 'the body' );

  $noDataRow  = padCacheUrl ( $noDataUrl );
  $noDataBody = padCacheBody ( $noDataEtag );

  padCacheDelete ( $noDataUrl, $noDataEtag );

  $padCacheServerNoData = $noDataKeep;

  $noDataNamed = ( is_array ( $noDataRow ) and ( $noDataRow ['etag'] ?? $noDataRow [1] ?? '' ) === $noDataEtag ) ? 'yes' : 'NO';
  $noDataKept  = $noDataBody ? 'yes' : 'no';

?>
