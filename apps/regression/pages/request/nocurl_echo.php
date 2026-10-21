<?php

  // The method and the posted value a fetch arrived with - what a_fetch_without_ext_curl_
  // keeps_the_pin_and_the_post fetches without curl.

  $echoMethod = $_SERVER ['REQUEST_METHOD'];
  $echoSaid   = (string) ( $_POST ['said'] ?? 'none' );

?>
