<?php

  // ?sample/rows&padSample=capture keeps what the page returned as @return.

  padCurl ( [ 'url' => $padGoExt . 'sample/rows&padSample=capture&padInclude' ] );

  $capturedRows = padFileGet ( "samples/$padApp/sample/rows.json" );

?>
