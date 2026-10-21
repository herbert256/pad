<?php

  // A 304 carries the ETag the 200 to the same request carries. The stable page, fetched at
  // a fresh address: the miss goes out as it was made, the hit gzipped as the cache keeps it,
  // with the tag of the gzip body ("...-gzip"), and asked again with that tag the answer is
  // a 304 with the same one - it said the identity tag, which a cache holding the gzip copy
  // may not take for the one it holds.

  function tagOf ( $answer ) {

    foreach ( $answer ['headers'] ?? [] as $name => $value )
      if ( strtolower ( $name ) == 'etag' )
        return trim ( $value );

    return '';

  }

  $tagUrl  = $padHost . 'regression/cache_file/?stable&fresh=' . bin2hex ( random_bytes ( 6 ) );
  $tagMiss = padCurl ( $tagUrl );
  $tagHit  = padCurl ( $tagUrl );
  $tagAsk  = padCurl ( [ 'url' => $tagUrl, 'headers' => [ 'If-None-Match' => tagOf ( $tagHit ) ] ] );

  $tagResult = 'miss: '  . ( str_ends_with ( tagOf ( $tagMiss ), '-gzip"' ) ? 'gzip tag' : 'plain tag' )
             . ', hit: ' . ( str_ends_with ( tagOf ( $tagHit ),  '-gzip"' ) ? 'gzip tag' : 'plain tag' )
             . ', asked with it: ' . $tagAsk ['result'] . ' ' . ( tagOf ( $tagAsk ) === tagOf ( $tagHit ) ? 'the same tag' : 'another tag' );

?>
