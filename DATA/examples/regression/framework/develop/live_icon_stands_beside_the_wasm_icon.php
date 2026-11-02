<?php

  // Beside the wasm's icon an example has the live application's, whose link carries
  // the same boxes; an application whose examples need what only a page of this server has -
  // classicModels, its database and the tables its _inits.php declares - has the live icon
  // alone, its link carrying those declarations as the example's PHP.

  $links = function ( $url ) {

    preg_match_all ( '#<a class="pad-play([^"]*)" href="[^"]*/(wasm|live)/\#s=([A-Za-z0-9+/=]+)"#', padCurl ( $url ) ['data'], $all, PREG_SET_ORDER );

    $found = [];

    foreach ( $all as $one )
      $found [ $one [2] ] ??= json_decode ( base64_decode ( $one [3] ), TRUE );

    return $found;

  };

  $manual  = $links ( $padHost . 'manual/?pages/arrays&padInclude' );
  $classic = $links ( $padHost . 'classicModels/' );

  $answer = 'manual: ' . implode ( ' + ', array_keys ( $manual ) )
          . ( ( ( $manual ['wasm'] ?? 1 ) == ( $manual ['live'] ?? 2 ) ) ? ', the same boxes' : ', other boxes' )
          . ' // classicModels: ' . implode ( ' + ', array_keys ( $classic ) )
          . ( str_contains ( $classic ['live'] ['php'] ?? '', "\$padSelect ['offices']" ) ? ', with its declarations' : ', without them' );

?>
