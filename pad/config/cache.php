<?php

  // Default settings for the caching subsystem, pulled in by cache/inits.php and only when
  // $padCache is on - which is why it may safely refer to DATA.
  //
  // Sets the three cache lifetimes (server side, proxy, client), selects the server-side
  // backend through $padCacheServerType, and carries the connection details for each
  // supported backend: memcached, redis, a database, or plain files under DATA/cache/.
  //
  // Every setting is a default, not a decision: the application's _config/config.php ran
  // before this file, and what it chose stands - an unconditional assignment here zeroed
  // the age an application had just configured, which is why no application could ever
  // switch caching on.

  // How long, in seconds, the page cache keeps an answer and serves it without building
  // the page again (cache/inits.php). 0 keeps nothing, whatever $padCache says; a page's
  // {meta cache=} sets its own.

  $padCacheServerAge = $padCacheServerAge ?? 0;

  // The s-maxage of the Cache-Control: public an anonymous visitor's answer carries
  // (lib/output.php): how long a shared cache - a proxy, a CDN - may serve it. An answer
  // that sets a cookie, or one with an identity - a session, an Authorization header, a
  // cookie of the visitor's own, a locale chosen by cookie - is private and never gets it.

  $padCacheProxyAge  = $padCacheProxyAge  ?? 0;

  // The max-age of Cache-Control: how long the visitor's browser may use its copy without
  // asking again.

  $padCacheClientAge = $padCacheClientAge ?? 0;

  // Where the page cache keeps its answers: 'memcached', 'redis', 'apcu', 'db' or 'file'
  // (cache/types/<type>.php). $padCacheServerGzip stores the bodies gzipped - sent as they
  // are to a client that accepts gzip, unzipped for one that does not. $padCacheServerNoData
  // stores only the ETags, so a hit is answered 304 to a client that holds the page and
  // built afresh for one that does not.

  $padCacheServerType      = $padCacheServerType   ?? 'memcached';
  $padCacheServerGzip      = $padCacheServerGzip   ?? TRUE;
  $padCacheServerNoData    = $padCacheServerNoData ?? FALSE;

  // The memcached server of the 'memcached' backend.

  $padCacheMemcachedHost   = $padCacheMemcachedHost ?? 'localhost';
  $padCacheMemcachedPort   = $padCacheMemcachedPort ?? '11211';

  // The redis server of the 'redis' backend.

  $padCacheRedisHost       = $padCacheRedisHost ?? 'localhost';
  $padCacheRedisPort       = $padCacheRedisPort ?? 6379;

  // The database of the 'db' backend - a connection of its own, never the
  // application's: its tables etag, url and data hold the ages, the address keys and the
  // bodies.

  $padCacheDbHost          = $padCacheDbHost     ?? '127.0.0.1';
  $padCacheDbDatabase      = $padCacheDbDatabase ?? 'cache';
  $padCacheDbUser          = $padCacheDbUser     ?? 'cache';
  $padCacheDbPassword      = $padCacheDbPassword ?? 'cache';

  // The directory of the 'file' backend: url/<hash> holds the ETag of an address, etag/<etag>
  // the body, and a file's time is the entry's age.

  $padCacheFile            = $padCacheFile ?? DATA . 'cache/';

?>
