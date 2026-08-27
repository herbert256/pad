<?php

  if ( ! isset ( $go ) )
    return;

  padCurl ( $padHost . "reference/?build&go=1", [ 'options' => [ 'TIMEOUT' => 3600 ] ] );

?>