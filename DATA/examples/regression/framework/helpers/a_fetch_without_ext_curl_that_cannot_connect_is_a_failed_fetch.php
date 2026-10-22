<?php

  // Without ext-curl a fetch that cannot connect is a failed fetch, result 999 and an error
  // that says so: fopen answered FALSE under the silenced warning, and stream_get_contents
  // threw a TypeError on it - 'stream_get_contents(): Argument #1 ($stream) must be of type
  // resource, false given', with the engine's path, went to padCurlError, the log and the
  // application's _events/curl.php.

  $ncfOut    = padNoCurl ( padCurlBuild ( 'http://127.0.0.1:1/x' ) );
  $ncfResult = $ncfOut ['result'] . ' ' . $ncfOut ['ERROR'];

?>
