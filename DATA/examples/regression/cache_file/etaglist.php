<?php

  // On a cache miss the 304 decision reads the whole If-None-Match list: the stable page,
  // fetched at a fresh address each time so the cache never holds it, answers 304 to a list
  // holding its tag second, and to *, and 200 to a tag it does not have. Only the first tag
  // of the list was compared there - the cache's own path already read them all.

  function etagAsk ( $tags ) {

    global $padHost;

    $input = [ 'url' => $padHost . 'regression/cache_file/?stable&fresh=' . bin2hex ( random_bytes ( 6 ) ) ];

    if ( $tags !== '' )
      $input ['headers'] = [ 'If-None-Match' => $tags ];

    return padCurl ( $input );

  }

  $etagFirst = etagAsk ( '' );
  $etagTag   = '';

  foreach ( $etagFirst ['headers'] ?? [] as $etagName => $etagValue )
    if ( strtolower ( $etagName ) == 'etag' )
      $etagTag = trim ( $etagValue );

  $etagResult = 'tag: ' . ( $etagTag !== '' ? 'sent' : 'none' )
              . ' | list: '  . etagAsk ( '"AAAAAAAAAAAAAAAAAAAAAA", ' . $etagTag ) ['result']
              . ' | star: '  . etagAsk ( '*' ) ['result']
              . ' | other: ' . etagAsk ( '"AAAAAAAAAAAAAAAAAAAAAA"' ) ['result'];

?>
