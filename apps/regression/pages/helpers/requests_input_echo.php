<?php

  // The fixture of requests_input: the method and the input of whatever request reaches it,
  // padInclude - the regression fetch's own value - left out.

  $echoInput = padRequestMethod () . ' ' . json_encode ( padRequestExcept ( 'padInclude' ) );

?>
