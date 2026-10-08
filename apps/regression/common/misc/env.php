<?php

  // An application that runs with _common reads _common's .env after its own and before
  // the PAD home's: padEnv answers _common's database name, demo, where the PAD home's .env
  // says app - and the setting _common's configuration read with it is the same.

  $fromEnv    = padEnv ( 'padSqlDatabase' );
  $fromConfig = $GLOBALS ['padSqlDatabase'];

?>
