<?php

  // The same request from elsewhere - a forwarded one - is not local, so $padSample =
  // 'local' ignores the flag and the real page answers.

  $remoteResult = padCurl ( [ 'url'     => $padGoExt . 'sample/orders&padSample&padInclude',
                              'headers' => [ 'X-Forwarded-For' => '203.0.113.9' ] ] ) ['data'];

?>
