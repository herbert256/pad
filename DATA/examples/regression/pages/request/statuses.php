<?php

  // A page that is not there answers 404 - it was a 500 - and a status the page's own PHP
  // sets with http_response_code() stands, where the engine answered 200 over it.

  $missing = padCurl ( $padGoExt . 'request/nosuchpage&padInclude' );
  $gone    = padCurl ( $padGoExt . 'request/gone&padInclude' );

  $statuses = 'missing: ' . $missing ['result'] . ', gone: ' . $gone ['result'] . ' ' . trim ( $gone ['data'] );

?>
