<?php

  // Every key of the fixture, read with padEnv - a key that is not there answers the
  // default - and the password the configuration file read with it.

  $keys = [ 'ENVTEST_BARE', 'ENVTEST_HASH_INSIDE', 'ENVTEST_EXPORTED', 'ENVTEST_INDENTED',
            'ENVTEST_DOUBLE', 'ENVTEST_SINGLE', 'ENVTEST_EXPANDED', 'ENVTEST_EXPANDED_BARE',
            'ENVTEST_MULTI', 'ENVTEST_TRUE', 'ENVTEST_TRUE_PAREN', 'ENVTEST_FALSE',
            'ENVTEST_NULL', 'ENVTEST_EMPTY_WORD', 'ENVTEST_EMPTY', 'ENVTEST_QUOTED_FALSE',
            'ENVTEST_ZERO', 'ENVTEST_TWICE', 'ENVTEST_UNICODE', 'ENVTEST_NOT_SET',
            'REQUEST_METHOD', 'HTTP_HOST' ];

  $read = [];

  foreach ( $keys as $key )
    $read [$key] = padEnv ( $key, 'the default' );

  $values   = json_encode ( $read, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
  $password = $padSqlPassword;

?>
