<?php

  // Fetches the probe twice. The probe's body embeds its build moment in nanoseconds, so
  // two equal bodies mean the second fetch was answered from the memcached cache.

  $one = padCurl ( $padHost . 'regression/cache_memcached/?probe&padInclude' );
  $two = padCurl ( $padHost . 'regression/cache_memcached/?probe&padInclude' );

  $verdict = ( trim ( $one ['data'] ) != '' and $one ['data'] === $two ['data'] ) ? 'yes' : 'NO';

  $backend = $padCacheServerType;

?>