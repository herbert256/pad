<?php

  // The path of a clean URL as the engine reads it. Apache's FallbackResource hands
  // /pad/shop/products/42 to /pad/shop/index.php with no PATH_INFO, so the path is what the
  // request URI has beyond the entry point's directory, decoded; the entry point itself, and
  // its directory with a query string, have none. A PATH_INFO tail of &name=value pairs -
  // a {$padGo}page&x=1 link in the clean form - becomes request values.

  $routeServer = $_SERVER;
  $routeGet    = $_GET;

  unset ( $_SERVER ['PATH_INFO'] );

  $_SERVER ['SCRIPT_NAME'] = '/pad/shop/index.php';

  $_SERVER ['REQUEST_URI'] = '/pad/shop/products/4%202?x=1';  $one   = padRequestPath ();
  $_SERVER ['REQUEST_URI'] = '/pad/shop/index.php?about';     $two   = padRequestPath ();
  $_SERVER ['REQUEST_URI'] = '/pad/shop/?about';              $three = padRequestPath ();

  $_SERVER ['PATH_INFO']   = '/products/42/&sort=price';      $four  = padRequestPath ();

  $sorted = $_GET ['sort'] ?? 'none';

  $_SERVER = $routeServer;
  $_GET    = $routeGet;

?>
