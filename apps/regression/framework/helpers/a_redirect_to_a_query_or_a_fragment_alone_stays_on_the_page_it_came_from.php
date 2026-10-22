<?php

  // Without ext-curl padNoCurl follows a redirect itself (padNoCurlLocation): a Location of a
  // query alone stays on the path of the address it answered, one of a fragment alone on its
  // path and query, as a browser and curl resolve them (RFC 3986 5.2.2). Both were read as a
  // relative path and went to the directory - a clean-URL page /shop/products/42 redirecting
  // to ?tab=2 sent the fetch to the directory's index.

  $nclResult = padNoCurlLocation ( 'http://localhost/pad/hello/page', '?x=1' )
             . ' ' . padNoCurlLocation ( 'http://localhost/a/b?q=1', '#top' )
             . ' ' . padNoCurlLocation ( 'http://localhost:8080/a/b?q=1', 'c?d=2' )
             . ' ' . padNoCurlLocation ( 'http://localhost/a/b', '/e' );

?>
