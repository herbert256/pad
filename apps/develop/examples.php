<?php

  if ( ! isset ( $go ) )
    return;

  padCurl ( [ 'url' => $padHost . "examples/?build&go=1", 'options' => [ 'TIMEOUT' => 3600 ] ] );

?>