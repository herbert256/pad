<?php

  // The runner's own rule for Test, Build and record: this machine's own request - loopback,
  // nothing forwarded, no other site behind it - naming this machine in Host: localhost, a
  // loopback address, or a name $padHosts lists for the server, which a local vhost is. It
  // does not ask $padDiagnostics, which only says whether error reports show, and a page
  // rebound to 127.0.0.1 still names its own site, which is refused. Each answer is asked
  // of getSuiteLocal with this request's Host and the settings put as they would stand.

  $keepHost  = $_SERVER ['HTTP_HOST'] ?? NULL;
  $keepDiag  = $padDiagnostics ?? TRUE;
  $keepHosts = $padHosts ?? [];
  $localSeen = [];

  foreach ( [ [ 'localhost:8080',  FALSE, []                       ],
              [ '127.0.0.1',       TRUE,  []                       ],
              [ 'pad.test',        TRUE,  [ 'pad.test' ]           ],
              [ 'pad.test:8080',   TRUE,  [ 'other.test', 'pad.test' ] ],
              [ 'pad.test',        TRUE,  []                       ],
              [ 'rebound.example', TRUE,  [ 'pad.test' ]           ] ] as [ $asHost, $asDiag, $asHosts ] ) {
    $_SERVER ['HTTP_HOST'] = $asHost;
    $padDiagnostics        = $asDiag;
    $padHosts              = $asHosts;
    $localSeen []          = getSuiteLocal () ? 'yes' : 'no';
  }

  $_SERVER ['HTTP_HOST'] = $keepHost;
  $padDiagnostics        = $keepDiag;
  $padHosts              = $keepHosts;

  $localAnswer = implode ( ' ', $localSeen );

?>
