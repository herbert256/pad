<?php
  
  // Cache settings
  
  $padCacheServerAge = 0;   //  Seconds to keep the cache at pad server side, 
                            //  0 to turn of server-side caching

  $padCacheProxyAge  = 0;   //  How long a proxy is allowed to cache. 
                            //  0 to turn of proxy-side caching

  $padCacheClientAge = 0;   //  How long the client is allowed to cache.
                            //  0 to turn of client-side caching

  // Server-side cache settings ( used when $padCacheServerAge <> 0 )

  $padCacheServerType      = 'memory';            //  The implementation of the server-side cache: file/db/memory
  $padCacheServerGzip      = TRUE;                //  Store the cache zipped
  $padCacheServerNoData    = FALSE;               //  Do not store the data itself, only the etag and timestamp,
                                                  //  caching based on the client 'etag' & 'modified' HTTP headers.

  $padCacheMemoryHost      = 'localhost';         //  Used when $padCacheServerType is 'memory'
  $padCacheMemoryPort      = '11211';

  $padCacheDbHost          = 'localhost';         //  Used when $padCacheServerType is 'db'
  $padCacheDbDatabase      = 'cache';
  $padCacheDbUser          = 'cache';
  $padCacheDbPassword      = 'cache';

  $padCacheFile            = '/data/' . 'cache/';  //  Used when $padCacheServerType is 'file'
  $padCacheFileMode        = 755;

?>