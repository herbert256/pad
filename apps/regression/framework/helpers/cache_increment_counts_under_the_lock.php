<?php

  // padCacheIncrement adds to the number kept under a key, under the application's lock, so
  // requests that come at once each count - a get and a put beside each other lost the hits
  // between them. A key holding no number starts from 0.

  padCacheForget ( 'incrementCase' );
  padCachePut ( 'incrementText', 'abc' );

  $incShown = implode ( ' ', [ padCacheIncrement ( 'incrementCase' ), padCacheIncrement ( 'incrementCase', 2 ),
                               padCacheGet ( 'incrementCase' ), padCacheIncrement ( 'incrementText' ) ] );

  padCacheForget ( 'incrementCase' );
  padCacheForget ( 'incrementText' );

?>
