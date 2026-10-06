<?php

  // A run reached as http://127.0.0.1/pad/ reads its own host as localhost in what came
  // back - but http://127.0.0.1:8000/, which the manual's pad command page names for
  // pad serve, is another server: its port is part of it, and rewritten to localhost:8000
  // that page failed a run on 127.0.0.1 against the answer that names 127.0.0.1:8000.

  $keep     = $padHost;
  $padHost  = 'http://127.0.0.1/pad/';
  $hostless = getSuiteHostless ( 'http://127.0.0.1/pad/x http://127.0.0.1:8000/y' );
  $padHost  = $keep;

?>
