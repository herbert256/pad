<?php

  // ?sample/orders&padSample=capture renders the real page and writes what its PHP made,
  // and the named database answer, to DATA/samples/<app>/<page>.json.

  padCurl ( [ 'url' => $padGoExt . 'sample/orders&padSample=capture&padInclude' ] );

  $captured = padFileGet ( "samples/$padApp/sample/orders.json" );

?>
