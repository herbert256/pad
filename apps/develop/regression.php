<?php

  if ( ! isset ( $go ) )
    return;

  padCurl ( [ 'url' => $padHost . "regression/main/?build&go=1", 'options' => [ 'TIMEOUT' => 3600 ] ] );

?>