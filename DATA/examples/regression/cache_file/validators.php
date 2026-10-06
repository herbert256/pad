<?php

  // The cache's validators belong to one URL. The probe's own ETag earns a 304 from the
  // probe and a full answer from any other URL, and a date just before the probe was
  // cached is older than the cached copy, so it gets the page, not a 304.

  $validUrl = $padHost . 'regression/cache_file/?probe&padInclude&validators';

  $validWarm = padCurl ( $validUrl );
  $validEtag = $validWarm ['headers'] ['Etag'] ?? '';
  $validTime = filemtime ( DATA . 'cache/etag/' . trim ( $validEtag, '"' ) );
  $validDate = gmdate ( 'D, d M Y H:i:s', $validTime - 1 ) . ' GMT';

  $validOwn   = padCurl ( [ 'url' => $validUrl,            'headers' => [ 'If-None-Match'     => $validEtag ] ] );
  $validOther = padCurl ( [ 'url' => $validUrl . '&other', 'headers' => [ 'If-None-Match'     => $validEtag ] ] );
  $validOlder = padCurl ( [ 'url' => $validUrl,            'headers' => [ 'If-Modified-Since' => $validDate ] ] );

  $validResult = 'own etag: '    . $validOwn   ['result']
               . ', other url: ' . $validOther ['result']
               . ', older date: ' . $validOlder ['result'];

?>
