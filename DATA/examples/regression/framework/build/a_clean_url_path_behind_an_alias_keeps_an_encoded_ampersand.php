<?php

  // The path of a clean URL when the request URI does not show the entry point's directory -
  // an Apache Alias or RewriteRule /shop/... -> /pad/shop/index.php/..., a proxy's prefix: the
  // PATH_INFO the server hands over is decoded, and products/a%26b came out as products/a
  // with a value b. The raw end of the request URI that decodes to PATH_INFO is read instead,
  // so an encoded & stays in its segment and a real one still starts the tail.

  $aliasServer = $_SERVER;
  $aliasGet    = $_GET;

  $_SERVER ['SCRIPT_NAME'] = '/pad/shop/index.php';

  $_SERVER ['REQUEST_URI'] = '/shop/products/a%26b?x=1';
  $_SERVER ['PATH_INFO']   = '/products/a&b';
  $_GET                    = [];
  $aliasOne                = padRequestPath () . ' b=' . ( isset ( $_GET ['b'] ) ? 'set' : 'none' );

  $_SERVER ['REQUEST_URI'] = '/shop/products/c%26d&sort=price';
  $_SERVER ['PATH_INFO']   = '/products/c&d&sort=price';
  $_GET                    = [];
  $aliasTwo                = padRequestPath () . ' sort=' . ( $_GET ['sort'] ?? 'none' );

  $_SERVER = $aliasServer;
  $_GET    = $aliasGet;

?>
