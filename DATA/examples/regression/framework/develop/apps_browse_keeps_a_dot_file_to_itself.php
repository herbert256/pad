<?php

  // The apps browser shows the sources of every application to anyone - only those its
  // listing shows. A name starting with a dot is never listed, and .env in the application's
  // root is where padEnv finds its passwords and keys: typed into the address, it was shown.

  $curl   = padCurl ( $padHost . 'apps/?browse&app=regression/env&file=.env&padInclude' );
  $answer = $curl ['result'] . ': ' . ( str_contains ( $curl ['data'], 's3cret-from-the-file' ) ? 'the password shown' : 'no password' );

?>
