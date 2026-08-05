<?php

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