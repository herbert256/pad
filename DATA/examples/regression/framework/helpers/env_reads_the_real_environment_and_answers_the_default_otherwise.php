<?php

  $method  = padEnv ( 'REQUEST_METHOD' );
  $header  = json_encode ( padEnv ( 'HTTP_HOST' ) );
  $missing = json_encode ( padEnv ( 'ENVTEST_SET_NOWHERE' ) );
  $default = padEnv ( 'ENVTEST_SET_NOWHERE', 'the default' );
  $closure = padEnv ( 'ENVTEST_SET_NOWHERE', function () { return 'from a closure'; } );
  $false   = json_encode ( padEnv ( 'ENVTEST_SET_NOWHERE', FALSE ) );
  $zero    = json_encode ( padEnv ( 'ENVTEST_SET_NOWHERE', 0 ) );

?>
