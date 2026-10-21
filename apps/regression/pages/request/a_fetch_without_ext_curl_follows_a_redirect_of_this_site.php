<?php

  // Without ext-curl a fetch of this site pinned to its own socket follows a redirect on
  // that same address, as curl's CONNECT_TO does: the stream wrapper, told not to follow
  // one off the pin, answered the redirect's own body - Apache's 301 page, PAD's empty
  // one - as a 200 page. A post that is redirected goes on as a GET, as curl sends it.

  // The redirect names this site with the port it was asked on, so the name is pinned with
  // the port of $padHost - as padSelfConnect pins it.

  $nocurlAt  = parse_url ( $padHost, PHP_URL_PORT ) ?? 80;
  $nocurlPin = preg_replace ( '/^(\[[^\]]*\]|[^:]*):\d*:/', "pad-nocurl.invalid:$nocurlAt:", padSelfConnect () [0] ?? '' );
  $nocurlUrl = 'http://pad-nocurl.invalid' . ( $nocurlAt == 80 ? '' : ":$nocurlAt" )
             . parse_url ( $padHost, PHP_URL_PATH ) . 'regression/pages/?request/nocurl_echo&padInclude&moved=1';

  $nocurl = padNoCurl ( padCurlBuild ( [ 'url'     => $nocurlUrl,
                                         'post'    => 'said=posted',
                                         'options' => [ 'CONNECT_TO' => [ $nocurlPin ] ] ] ) );

  $nocurlAnswer = $nocurl ['result'] . ' ' . trim ( (string) $nocurl ['data'] );

?>
