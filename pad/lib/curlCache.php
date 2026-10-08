<?php

  // Remote data with a cache: a fetch kept for ttl seconds, so a page built on an external
  // API asks it once per ttl instead of once per request - where {curl} and remote data
  // fetched again every time.
  //
  //   {curl 'https://api.example.com/rates.json', ttl=600}
  //   {pad data='https://api.example.com/rates.json', ttl=600} ... {/pad}
  //   _data/rates.curl    <curl><url>https://...</url><ttl>600</ttl></curl>
  //
  // padCurlCached answers in padCurl's own shape, plus ['cache']: 'hit' for a copy younger
  // than ttl, 'miss' for a fresh fetch, 'stale' for an older copy served because the fetch
  // failed. Only a 2xx answer is kept. When the source is down - a failed transfer or any
  // other status - the last good copy is served for as long as $padCurlStale allows beyond
  // its ttl, and the failure goes to the error log; the application's _events/curl.php
  // hears it as well. Without a copy the failure is the caller's, as without a ttl.
  //
  // $padCurlCache picks the store: 'file' (DATA/cache/curl/, the default), 'apcu',
  // 'redis' or 'memcached' - the latter two on the page cache's connection settings
  // $padCacheRedisHost/Port and $padCacheMemcachedHost/Port - or FALSE to fetch every
  // time. The page cache's 'db' backend is not one of them: its tables are shaped for
  // pages by ETag, and its purge would take the copies kept for a failing source.
  //
  // While a test fakes the fetches (lib/curlFake.php) the cache is passed by, read and
  // write: a copy kept of a fake answer would be served to the real requests after it.
  //
  // The key is the application and the padCurl input as given, so two pages asking the
  // same thing share one copy. An entry is the time it was fetched and the output as JSON
  // - a fetched document is text, and JSON keeps the store free of unserialize.

  function padCurlCached ( $input, $ttl ) {

    global $padCurlCache, $padCurlLast;

    $ttl = max ( 0, (int) $ttl );

    if ( ! $padCurlCache or ! $ttl or padCurlFaking () )
      return padCurl ( $input ) + [ 'cache' => 'miss' ];

    $key   = padCurlCacheKey ( $input );
    $entry = padCurlCacheGet ( $key );

    if ( $entry and $entry ['time'] + $ttl > time () )
      return $padCurlLast = $entry ['output'] + [ 'cache' => 'hit' ];

    return $padCurlLast = padCurlCacheAfter ( $key, padCurl ( $input ), $entry, $ttl );

  }

  // The concurrent form, for padPrefetch: the inputs with a fresh copy are answered from
  // it, the others fetched together through padCurlMulti, each kept or replaced by its
  // last good copy as padCurlCached does. The outputs come back under the inputs' keys.

  function padCurlCachedMulti ( $inputs, $ttl ) {

    global $padCurlCache;

    $ttl     = max ( 0, (int) $ttl );
    $results = [];

    if ( ! $padCurlCache or ! $ttl or padCurlFaking () ) {
      foreach ( padCurlMulti ( $inputs ) as $name => $output )
        $results [$name] = $output + [ 'cache' => 'miss' ];
      return $results;
    }

    $keys    = [];
    $entries = [];
    $fetch   = [];

    foreach ( $inputs as $name => $input ) {

      $keys    [$name] = padCurlCacheKey ( $input );
      $entries [$name] = padCurlCacheGet ( $keys [$name] );

      if ( $entries [$name] and $entries [$name] ['time'] + $ttl > time () )
        $results [$name] = $entries [$name] ['output'] + [ 'cache' => 'hit' ];
      else
        $fetch [$name] = $input;

    }

    foreach ( padCurlMulti ( $fetch ) as $name => $output )
      $results [$name] = padCurlCacheAfter ( $keys [$name], $output, $entries [$name], $ttl );

    return array_replace ( array_intersect_key ( $inputs, $results ), $results );

  }

  // A fetch has come back: a good answer is kept, a failure is answered with the copy
  // there is, if there is one still within $padCurlStale beyond its ttl, and logged. The
  // window is held here because a file entry outlives it until the hourly purge, a day
  // later: the copy of any age was served, where the other stores had expired it.

  function padCurlCacheAfter ( $key, $output, $entry, $ttl ) {

    global $padCurlStale;

    // An answer that carries a {debug} box was rendered for a local request alone - a
    // self-fetch made for one - and is not kept: kept for the ttl, every visitor after it
    // was served the box, as the page cache and the fragment cache refuse to (padCacheLocalOnly).

    if ( str_starts_with ( (string) $output ['result'], '2' ) ) {
      if ( ! is_string ( $output ['data'] ) or ! padCacheLocalOnly ( $output ['data'] ) )
        padCurlCachePut ( $key, $output, $ttl );
      return $output + [ 'cache' => 'miss' ];
    }

    if ( ! $entry or $entry ['time'] + $ttl + max ( 0, (int) $padCurlStale ) <= time () )
      return $output + [ 'cache' => 'miss' ];

    padLogError ( 'curl: ' . $output ['url'] . ' answered ' . $output ['result']
                . ( isset ( $output ['ERROR'] ) ? ' (' . $output ['ERROR'] . ')' : '' )
                . ' - serving the copy of ' . ( time () - $entry ['time'] ) . ' seconds ago' );

    return $entry ['output'] + [ 'cache' => 'stale' ];

  }

  function padCurlCacheKey ( $input ) {

    global $padApp;

    return padMD5 ( "$padApp " . serialize ( $input ) );

  }

  // What is kept of an output: what a reader of the data uses. The transfer details and
  // the cookies belong to the one fetch.

  function padCurlCachePut ( $key, $output, $ttl ) {

    global $padCurlCache, $padCurlStale;

    $keep = [];

    foreach ( [ 'url', 'input', 'result', 'type', 'headers', 'data' ] as $name )
      $keep [$name] = $output [$name] ?? '';

    $life  = $ttl + max ( 0, (int) $padCurlStale );
    $entry = time () . "\n" . json_encode ( $keep, JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR );

    if ( $padCurlCache == 'apcu' )
      return function_exists ( 'apcu_store' ) and apcu_store ( "padCurl:$key", $entry, $life );

    if ( $padCurlCache == 'redis' or $padCurlCache == 'memcached' ) {
      $store = padCurlCacheStore ();
      if ( ! $store ) return FALSE;
      return ( $padCurlCache == 'redis' ) ? $store->setex ( "padCurl:$key", $life, $entry )
                                          : $store->set   ( "padCurl:$key", $entry, $life );
    }

    padCurlCachePurge ();

    return padFilePut ( "cache/curl/$key", $entry );

  }

  // The entry as [ time, output ], or FALSE. A file entry past its life is left for the
  // purge; the other stores expire their own.

  function padCurlCacheGet ( $key ) {

    global $padCurlCache, $padCurlStale;

    $entry = FALSE;

    if ( $padCurlCache == 'apcu' ) {
      if ( function_exists ( 'apcu_fetch' ) )
        $entry = apcu_fetch ( "padCurl:$key" );
    } elseif ( $padCurlCache == 'redis' or $padCurlCache == 'memcached' ) {
      $store = padCurlCacheStore ();
      if ( $store )
        $entry = $store->get ( "padCurl:$key" );
    } elseif ( is_file ( DATA . "cache/curl/$key" ) )
      $entry = file_get_contents ( DATA . "cache/curl/$key" );

    if ( ! is_string ( $entry ) or ! str_contains ( $entry, "\n" ) )
      return FALSE;

    list ( $time, $json ) = explode ( "\n", $entry, 2 );

    $output = json_decode ( $json, TRUE );

    if ( ! is_array ( $output ) )
      return FALSE;

    return [ 'time' => (int) $time, 'output' => $output ];

  }

  // One connection per request to the store the page cache's settings name - those are
  // only read when the page cache is on, hence the defaults here.

  function padCurlCacheStore () {

    global $padCurlCache;

    static $store = NULL;

    if ( $store !== NULL )
      return $store;

    $store = FALSE;

    try {

      if ( $padCurlCache == 'redis' and class_exists ( 'Redis' ) ) {
        $redis = new Redis ();
        if ( $redis->connect ( $GLOBALS ['padCacheRedisHost'] ?? 'localhost',
                               (int) ( $GLOBALS ['padCacheRedisPort'] ?? 6379 ) ) )
          $store = $redis;
      }

      if ( $padCurlCache == 'memcached' and class_exists ( 'Memcached' ) ) {
        $memcached = new Memcached ();
        $memcached->addServer ( $GLOBALS ['padCacheMemcachedHost'] ?? 'localhost',
                                (int) ( $GLOBALS ['padCacheMemcachedPort'] ?? 11211 ) );
        $store = $memcached;
      }

    } catch ( Throwable $e ) {

      padLogError ( "curl cache: the $padCurlCache store is not reachable - " . $e->getMessage () );

    }

    return $store;

  }

  // File entries do not expire by themselves: at most once an hour the ones past their
  // life - the longest ttl in use is not known here, so a day beyond $padCurlStale - are
  // deleted.

  function padCurlCachePurge () {

    global $padCurlStale;

    $marker = DATA . 'cache/curl/.purged';

    if ( is_file ( $marker ) and filemtime ( $marker ) > time () - 3600 )
      return;

    padFilePut ( 'cache/curl/.purged', '' );

    $before = time () - max ( 0, (int) $padCurlStale ) - 86400;

    foreach ( glob ( DATA . 'cache/curl/*' ) ?: [] as $file )
      if ( (int) @file_get_contents ( $file, FALSE, NULL, 0, 20 ) < $before )
        @unlink ( $file );

  }

  // Drops the kept copy of one input - after the source changed and the page must show it
  // now - or, without one, every copy in the 'file' or 'apcu' store; redis and memcached
  // entries end with their life.

  function padCurlForget ( $input = NULL ) {

    global $padCurlCache;

    if ( $input !== NULL ) {

      $key = padCurlCacheKey ( $input );

      if ( $padCurlCache == 'apcu' )
        return function_exists ( 'apcu_delete' ) and apcu_delete ( "padCurl:$key" );

      if ( $padCurlCache == 'redis' or $padCurlCache == 'memcached' ) {
        $store = padCurlCacheStore ();
        return $store and $store->delete ( "padCurl:$key" );
      }

      return @unlink ( DATA . "cache/curl/$key" );

    }

    if ( $padCurlCache == 'apcu' )
      return function_exists ( 'apcu_delete' ) and apcu_delete ( new APCUIterator ( '/^padCurl:/' ) );

    foreach ( glob ( DATA . 'cache/curl/*' ) ?: [] as $file )
      @unlink ( $file );

    return TRUE;

  }

?>
