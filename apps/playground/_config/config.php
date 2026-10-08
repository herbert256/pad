<?php

  // The playground runs templates it is given, so it runs them with as little reach as the
  // engine can give: no PHP functions from a template ($padPhpFunctions), no request value
  // turned into a variable ($padRequestVars - the posted template and data are read by
  // render.php itself), no _common, and no database of its own - the db tags reach the
  // engine's default credentials, which name no real database. _inits.php turns every request
  // away that is not this machine's own.
  //
  // And a post carries the CSRF token of the playground's own form, which the form gets on
  // its own: a page on any other site could make this machine's browser post the playground
  // a template - from loopback, local like any other - and it ran here, its {curl} free to
  // fetch from this machine and send what it read, the fields the engine answers among it,
  // to that site.

  $padCommon       = FALSE;
  $padCsrf         = TRUE;
  $padPhpFunctions = [];
  $padRequestVars  = [];
  $padTidy         = FALSE;

  $padSqlHost      = padEnv ( 'padSqlHost' );
  $padSqlDatabase  = padEnv ( 'padSqlDatabase' );
  $padSqlUser      = padEnv ( 'padSqlUser' );
  $padSqlPassword  = padEnv ( 'padSqlPassword' );

?>
