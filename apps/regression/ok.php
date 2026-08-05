<?php

  $curl = padCurl ( "$padHost$app/?$item&include" );

  padFilePut ( DATA . "regression/$app/$item.html", $curl ['data'] );
  padFilePut ( DATA . "regression/$app/$item.txt", 'ok' );

  padRedirect ( 'index' );

?>