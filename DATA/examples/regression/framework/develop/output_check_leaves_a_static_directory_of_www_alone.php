<?php

  // A link to a directory of www/ that is no application's entry point - the browser build
  // in www/wasm/, the index.html of www/regression/ - is served by the web server as it is,
  // and the output check took it for an application and reported it broken.

  $checked = [];

  foreach ( [ $padRoot . 'wasm/', $padRoot . 'regression/', $padRoot . 'nosuchapp/', $padRoot . 'demo/?nosuchpage' ] as $url )
    $checked [] = $url . ' ' . ( padOutputCheckLink ( $url ) ?: 'fine' );

  $checked = implode ( ' | ', $checked );

?>
