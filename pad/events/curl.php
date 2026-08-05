<?php

  // Fires at the end of padCurl() (pad/lib/curl.php), once a fetch has completed and the
  // response content type has been settled, just before the result is cached in
  // $padCurlLast and returned. Traces the requested $url when $padInfoTraceCurl is set.

  global $padInfoTrace, $padInfoTraceCurl;

  if ( ! $padInfoTrace )
    return;

  if ( ! $padInfoTraceCurl )
    return;

  padInfoTrace ( 'curl', 'file', $url );

?>