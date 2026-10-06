<?php

  // A 304 answers a GET or a HEAD: the same If-None-Match: * that turns a GET of the page
  // into a 304 leaves a POST its 200 and its body - the POST was done, and it was answered
  // with no body at all.

  $etagUrl = $padHost . 'regression/pages/?request/etag_target&padInclude';
  $etagGet = padCurl ( [ 'url' => $etagUrl, 'headers' => [ 'If-None-Match' => '*' ] ] );
  $etagPut = padCurl ( [ 'url' => $etagUrl, 'headers' => [ 'If-None-Match' => '*' ], 'post' => 'a=1' ] );

  $etagGot = "get {$etagGet ['result']}, post {$etagPut ['result']} " . trim ( $etagPut ['data'] );

?>
