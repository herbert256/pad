<?php

  $padCommon = FALSE;

  $padSqlHost      = '127.0.0.1';
  $padSqlDatabase  = 'demo';
  $padSqlUser      = 'demo';
  $padSqlPassword  = 'demo';

  // The live region test needs a page that checks CSRF tokens (live/csrf).

  if ( $padPage == 'live/counter' )
    $padCsrf = TRUE;

?>