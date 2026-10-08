<?php

  // The fixtures are www/regression/framework/assets/case.css, case.js and case.mjs. The
  // version is the start of the contents' hash; a policy that asks for a nonce gets it on
  // the element. The addresses are shown from the application on: the mount prefix before
  // it is the server's.

  $same = ( padAsset ( 'assets/case.css' ) == $padRoot . 'regression/framework/assets/case.css?v='
            . substr ( hash_file ( 'xxh128', dirname ( APPS ) . '/www/regression/framework/assets/case.css' ), 0, 10 ) )
        ? 'the hash of the contents' : 'another hash';

  $cspWas = $padCsp;
  $padCsp = "script-src 'nonce'";
  $nonced = str_replace ( [ padNonce (), $padRoot ], [ 'NONCE', '/' ], padAssetTag ( 'assets/case.mjs' ) );
  $padCsp = $cspWas;

?>
