<?php

  $padCommon = FALSE;

  $padSqlHost      = '127.0.0.1';
  $padSqlDatabase  = 'demo';
  $padSqlUser      = 'demo';
  $padSqlPassword  = 'demo';

  // The boot action: an expected error answers its 500 as the lean JSON dump and writes
  // nothing under DATA/dumps - these cases fail on purpose dozens of times per run, and
  // under the pad action each failure was a heavy render and a dump on disk. Declared, so
  // every requester gets the same shape whatever user agent the trigger propagated.

  $padErrorAction = 'boot';

?>