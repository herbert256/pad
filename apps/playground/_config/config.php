<?php

  // The playground runs templates it is given, so it runs them with as little reach as the
  // engine can give: no PHP functions from a template ($padPhpFunctions), no request value
  // turned into a variable ($padRequestVars - the posted template and data are read by
  // render.php itself), no _common, and no database of its own - the db tags reach the
  // engine's default credentials, which name no real database. _inits.php turns every request
  // away that is not this machine's own.

  $padCommon       = FALSE;
  $padPhpFunctions = [];
  $padRequestVars  = [];
  $padTidy         = FALSE;

  $padSqlHost      = '127.0.0.1';
  $padSqlDatabase  = 'playground_none';
  $padSqlUser      = 'playground_none';
  $padSqlPassword  = '';

?>
