<?php

  // Default settings for the caching subsystem, pulled in by cache/inits.php and only when
  // $padCache is on - which is why it may safely refer to DATA.
  //
  // Sets the three cache lifetimes (server side, proxy, client), selects the server-side
  // backend through $padCacheServerType, and carries the connection details for each
  // supported backend: memcached, redis, a database, or plain files under DATA/cache/.

  $padCacheServerAge = 0;

  $padCacheProxyAge  = 0;

  $padCacheClientAge = 0;

  $padCacheServerType      = 'memcached';
  $padCacheServerGzip      = TRUE;
  $padCacheServerNoData    = FALSE;

  $padCacheMemcachedHost   = 'localhost';
  $padCacheMemcachedPort   = '11211';

  $padCacheRedisHost       = 'localhost';
  $padCacheRedisPort       = 6379;

  $padCacheDbHost          = 'localhost';
  $padCacheDbDatabase      = 'cache';
  $padCacheDbUser          = 'cache';
  $padCacheDbPassword      = 'cache';

  $padCacheFile            = DATA . 'cache/';
  $padCacheFileMode        = 755;

?>