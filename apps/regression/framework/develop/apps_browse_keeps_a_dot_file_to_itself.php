<?php

  // The apps browser shows the sources of every application to anyone - only those its
  // listing shows. A name starting with a dot is never listed, and _config/.env is where
  // padEnv finds an application's passwords and keys: typed into the address, it was shown.

  $curl   = padCurl ( $padHost . 'apps/?browse&app=regression/env&file=_config/.env&padInclude' );
  $answer = $curl ['result'] . ': ' . ( str_contains ( $curl ['data'], 's3cret-from-the-file' ) ? 'the password shown' : 'no password' );

?>
