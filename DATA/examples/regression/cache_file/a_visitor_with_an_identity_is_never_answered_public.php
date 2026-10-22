<?php

  // A page a shared cache may keep ($padCacheProxyAge) is public to a guest only. Asked with
  // a cookie of the visitor's own, or with an Authorization header, it is that visitor's -
  // private, as the page cache already treats such a request: it went out public with an
  // s-maxage, and a proxy kept "Hello Alice" for everyone after her (RFC 9111 3.5 lets a
  // shared cache reuse an answer to an Authorization request that says public). So is the
  // answer whose locale a padLang cookie chose: a shared cache keys it on the address and the
  // Accept-Language alone, and handed the Dutch page to the next English visitor.

  function proxyType ( $extra ) {

    global $padHost;

    $answer = padCurl ( [ 'url' => $padHost . 'regression/cache_file/?proxied&padInclude&fresh=' . bin2hex ( random_bytes ( 6 ) ) ] + $extra );

    $control = '';

    foreach ( $answer ['headers'] ?? [] as $name => $value )
      if ( strtolower ( $name ) == 'cache-control' )
        $control = $value;

    return trim ( $answer ['data'] ) . ' ' . ( preg_match ( '/^(public|private)\b/', $control, $match ) ? $match [1] : "'$control'" );

  }

  $proxyResult = 'guest: '           . proxyType ( [] )
               . ', cookie: '        . proxyType ( [ 'cookies' => [ 'viewer' => 'Alice' ] ] )
               . ', authorization: ' . proxyType ( [ 'headers' => [ 'Authorization' => 'Basic ' . base64_encode ( 'Alice:secret' ) ] ] )
               . ', language cookie: ' . proxyType ( [ 'cookies' => [ 'padLang' => 'nl' ], 'headers' => [ 'Accept-Language' => 'en' ] ] );

?>
