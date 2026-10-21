<?php

  // Without ext-curl padNoCurl read the URL as it stood: the pin of a fetch of this site to
  // the socket the request came in on (padSelfConnect, CONNECT_TO) was dropped - the URL's
  // host is made of the visitor's Host header, so Host: 169.254.169.254 had every SELF://,
  // {get} and {page app=} fetch from there - and the post, the cookies and the headers with
  // it, a {curl ..., post=} sent as a GET. Here the URL names a host that does not resolve,
  // and only the pin reaches this server.

  $nocurlPin = preg_replace ( '/^(\[[^\]]*\]|[^:]*):\d*:/', 'pad-nocurl.invalid:80:', padSelfConnect () [0] ?? '' );
  $nocurlUrl = 'http://pad-nocurl.invalid' . parse_url ( $padHost, PHP_URL_PATH ) . 'regression/pages/?request/nocurl_echo&padInclude';

  $nocurl = padNoCurl ( padCurlBuild ( [ 'url'     => $nocurlUrl,
                                         'post'    => 'said=posted',
                                         'options' => [ 'CONNECT_TO' => [ $nocurlPin ] ] ] ) );

  $nocurlAnswer = $nocurl ['result'] . ' ' . trim ( (string) $nocurl ['data'] );

?>
