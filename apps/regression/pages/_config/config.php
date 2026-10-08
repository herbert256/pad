<?php

  $padCommon = FALSE;

  $padSqlHost      = padEnv ( 'padSqlHost' );
  $padSqlDatabase  = padEnv ( 'padSqlDatabase' );
  $padSqlUser      = padEnv ( 'padSqlUser' );
  $padSqlPassword  = padEnv ( 'padSqlPassword' );

  // The live region tests need pages that check CSRF tokens - the check runs before the
  // page's PHP, so only the configuration can switch it on for a post.

  if ( in_array ( $padPage, [ 'live/counter', 'request/livecsrf' ] ) )
    $padCsrf = TRUE;

?>
