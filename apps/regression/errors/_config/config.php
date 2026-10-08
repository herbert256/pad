<?php

  $padCommon = FALSE;

  $padSqlHost      = padEnv ( 'padSqlHost' );
  $padSqlDatabase  = padEnv ( 'padSqlDatabase' );
  $padSqlUser      = padEnv ( 'padSqlUser' );
  $padSqlPassword  = padEnv ( 'padSqlPassword' );

  // The boot action: an expected error answers its 500 as the lean JSON dump and writes
  // nothing under DATA/dumps - these cases fail on purpose dozens of times per run, and
  // under the pad action each failure was a heavy render and a dump on disk. Declared, so
  // every requester gets the same shape whatever user agent the trigger propagated.

  $padErrorAction = 'boot';

?>
