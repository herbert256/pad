<?php

  $padCommon = FALSE;

  $padSqlHost      = padEnv ( 'padSqlHost' );
  $padSqlDatabase  = padEnv ( 'padSqlDatabase' );
  $padSqlUser      = padEnv ( 'padSqlUser' );
  $padSqlPassword  = padEnv ( 'padSqlPassword' );

  // The live region tests need pages that check CSRF tokens - the check runs before the
  // page's PHP, so only the configuration can switch it on for a post.

  if ( in_array ( $padPage, [ 'live/counter', 'live/form', 'request/livecsrf' ] ) )
    $padCsrf = TRUE;

  // The Secure cookie test asks itself as a site behind a proxy that ends TLS would be.

  if ( $padPage == 'request/every_engine_cookie_of_an_https_site_is_secure' and isset ( $_GET ['behind'] ) )
    $padHostBase = 'https://shop.example.com/';

  // The remember-me test writes a form before its {auth}: its token checked on the post.

  if ( $padPage == 'request/rememberform' ) {
    $padCsrf         = TRUE;
    $padAuthRemember = 'pagesRememberFind';
  }

?>
