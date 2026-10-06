<?php

  // Who may use develop's tools: this machine, asking from one of its own pages or from an
  // address typed in - _guard.php asks it for every page but the index.
  //
  // - the command line, or loopback with nothing forwarded (padLocal)
  // - naming this machine in the Host header: a page on another site that has its own name
  //   resolve to 127.0.0.1 (DNS rebinding) reaches the loopback address too, but says
  //   Host: that-site.example - the name $padHost is made of, which the builds crawl
  // - not sent by a page of another site, which a browser says in Sec-Fetch-Site: an
  //   <img src="http://localhost/pad/develop/?clean&go=1"> on any page the developer opens
  //   comes from this machine's own browser, loopback and all

  function developLocal () {

    if ( PHP_SAPI === 'cli' )
      return TRUE;

    $host = strtolower ( preg_replace ( '/:\d+$/', '', trim ( (string) ( $_SERVER ['HTTP_HOST'] ?? '' ) ) ) );
    $site = strtolower ( trim ( (string) ( $_SERVER ['HTTP_SEC_FETCH_SITE'] ?? '' ) ) );

    return padLocal ()
       and in_array ( $host, [ 'localhost', '127.0.0.1', '[::1]' ], TRUE )
       and $site !== 'cross-site' and $site !== 'same-site';

  }

?>
