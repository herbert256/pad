<?php

  // A fetch made for a local request gets the {debug} box of the page it fetches; kept for
  // the ttl, that copy was served to every remote visitor after it. The local request still
  // sees its box, but the answer is not kept.

  $debugUrl = $padHost . 'regression/remote/?debugged&padInclude&k=nodebug';

  padCurlForget ( $debugUrl );

  $fetched = padCurlCached ( $debugUrl, 600 );

  $shown  = str_contains ( (string) $fetched ['data'], 'pad-debug' ) ? 'shown' : 'not shown';
  $stored = padCurlCacheGet ( padCurlCacheKey ( $debugUrl ) ) ? 'stored' : 'not stored';

?>
