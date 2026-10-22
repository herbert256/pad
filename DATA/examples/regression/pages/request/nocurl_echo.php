<?php

  // The method and the posted value a fetch arrived with - what a_fetch_without_ext_curl_
  // keeps_the_pin_and_the_post fetches without curl.

  // Asked with moved, it sends the fetch on to itself first - the redirect
  // a_fetch_without_ext_curl_follows_a_redirect_of_this_site follows.

  if ( isset ( $_GET ['moved'] ) )
    padRedirect ( 'request/nocurl_echo', [ 'padInclude' => 1 ] );

  $echoMethod = $_SERVER ['REQUEST_METHOD'];
  $echoSaid   = (string) ( $_POST ['said'] ?? 'none' );

?>
