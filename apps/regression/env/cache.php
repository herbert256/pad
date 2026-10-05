<?php

  // The flush: every entry of this application goes, its rate limits too. The entries are
  // files in this application's own directory under DATA/cache/app/.

  padCacheFlush ();

  padCachePut  ( 'one', 1 );
  padCachePut  ( 'two', [ 2, 'two' ] );
  padRateLimit ( 'hits', 5 );

  $dir    = DATA . 'cache/app/regression/env/';
  $before = json_encode ( [ padCacheHas ( 'one' ), padCacheHas ( 'two' ),
                            padRateLimitRemaining ( 'hits', 5 ), count ( glob ( "$dir*.cache" ) ) ] );

  $flushed = json_encode ( padCacheFlush () );

  $after = json_encode ( [ padCacheHas ( 'one' ), padCacheHas ( 'two' ),
                           padRateLimitRemaining ( 'hits', 5 ), count ( glob ( "$dir*.cache" ) ) ] );

?>
