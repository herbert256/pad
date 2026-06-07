<?php

  $curl = padCurl ( "$padHost/$app?$item&include" );

  padFilePut ( DAT . "regression/$app/$item.html", $curl ['data'] );
  padFilePut ( DAT . "regression/$app/$item.txt", 'ok' );

  padRedirect ( 'index' );

?>