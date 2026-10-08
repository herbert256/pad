<?php

  // Fetches ?up three ways: all checks passing - 200 with the JSON of every check -, the
  // application's own check failing with its reason, and the database not answering - 503
  // with the check named but neither the reason nor anything of the credentials. Each with
  // its content type and caching.

  $base   = $padHost . 'regression/health/';
  $probes = [];

  foreach ( [ 'ok' => '?up', 'behind' => '?up&behind', 'nodb' => '?up&nodb' ] as $name => $ask ) {

    $curl = padCurl ( $base . $ask );

    $probes [] = [
      'name'   => $name,
      'status' => $curl ['result'],
      'type'   => $curl ['headers'] ['Content-Type']  ?? '-',
      'cache'  => $curl ['headers'] ['Cache-Control'] ?? '-',
      'body'   => trim ( $curl ['data'] ),
      'leak'   => ( str_contains ( $curl ['data'], 'secret-password' ) or str_contains ( $curl ['data'], 'nobody' ) ) ? 'yes' : 'no'
    ];

  }

?>
