<?php

  if ( ! isset ( $go ) )
    return;

  padCurl ( $padHost . "examples/?build&go=1", [ 'options' => [ 'TIMEOUT' => 3600 ] ] );

?>