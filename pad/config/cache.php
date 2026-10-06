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

  $padCacheServerAge = $padCacheServerAge ?? 0;

  $padCacheProxyAge  = $padCacheProxyAge  ?? 0;

  $padCacheClientAge = $padCacheClientAge ?? 0;

  $padCacheServerType      = $padCacheServerType   ?? 'memcached';
  $padCacheServerGzip      = $padCacheServerGzip   ?? TRUE;
  $padCacheServerNoData    = $padCacheServerNoData ?? FALSE;

  $padCacheMemcachedHost   = $padCacheMemcachedHost ?? 'localhost';
  $padCacheMemcachedPort   = $padCacheMemcachedPort ?? '11211';

  $padCacheRedisHost       = $padCacheRedisHost ?? 'localhost';
  $padCacheRedisPort       = $padCacheRedisPort ?? 6379;

  $padCacheDbHost          = $padCacheDbHost     ?? '127.0.0.1';
  $padCacheDbDatabase      = $padCacheDbDatabase ?? 'cache';
  $padCacheDbUser          = $padCacheDbUser     ?? 'cache';
  $padCacheDbPassword      = $padCacheDbPassword ?? 'cache';

  $padCacheFile            = $padCacheFile ?? DATA . 'cache/';

?>
