<?php

  // Parallel fetching: the remote sources of a page asked for at once, from its PHP,
  // and read as named data in the template.
  //
  //   padPrefetch ( [
  //     'rates'   => 'https://example.com/rates.json',
  //     'weather' => 'https://example.com/weather.json',
  //   ], 600 );
  //
  //   {rates}{$code}: {$rate}{/rates}
  //
  // Each source is fetched through padCurlMulti, all of them on the wire together, so the
  // page waits as long as the slowest answer, where data= and {curl} on one page fetched
  // one after another and the wait was the sum of them all. A source is a URL - SELF://
  // stands for $padHost - or an input array as padCurl takes it (url, headers, post ...).
  //
  // Each answer is parsed as data= parses a remote document (padCurlData) and kept in the
  // data store under its name, so {name} iterates it like a {data 'name'} block; the sets
  // are returned too, under the same names. The optional ttl keeps the answers as
  // {curl ..., ttl=} does (lib/curlCache.php): a fresh copy is not fetched at all, and a
  // failing source is answered with its last good copy. A source that fails without one is
  // an error, as a failing data= is.

  function padPrefetch ( $sources, $ttl = 0 ) {

    global $padDataStore, $padHost;

    $inputs = [];

    foreach ( $sources as $name => $source ) {

      if ( ! is_string ( $name ) or ! padValidName ( $name ) ) {
        padError ( "padPrefetch: a source needs a name a tag can have, not '" . padMakeSafe ( (string) $name, 40 ) . "'" );
        continue;
      }

      if ( is_array ( $source ) )
        $source ['url'] = str_replace ( 'SELF://', $padHost, $source ['url'] ?? '' );
      else
        $source = str_replace ( 'SELF://', $padHost, (string) $source );

      $inputs [$name] = $source;

    }

    $outputs = ( $ttl ) ? padCurlCachedMulti ( $inputs, $ttl ) : padCurlMulti ( $inputs );
    $sets    = [];

    foreach ( $inputs as $name => $input )
      $sets [$name] = $padDataStore [$name] = padCurlData ( $outputs [$name], $name );

    return $sets;

  }

  // A fetched answer as PAD data, for data/curl.php and padPrefetch: a status but 2xx is an
  // error, and under $padProtectValues so is a body that reads as PAD's list syntax, whose
  // elements would run as expressions - a fetched body is data.

  function padCurlData ( $curl, $name ) {

    if ( ! str_starts_with ( (string) $curl ['result'], '2' ) ) {
      padError ( "Curl failed: " . $curl ['result'] . ' ' . $curl ['url'] );
      return [];
    }

    if ( $GLOBALS ['padProtectValues'] and is_string ( $curl ['data'] ) )
      if ( ( $curl ['type'] ?: padContentType ( $curl ['data'] ) ) == 'list' ) {
        padError ( "the document fetched from " . $curl ['url'] . " reads as a PAD list, whose elements would run as expressions" );
        return [];
      }

    return padData ( $curl ['data'], $curl ['type'], $name );

  }

?>
