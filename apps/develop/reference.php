<?php

  if ( ! isset ( $go ) )
    return;

  padCurl ( [ 'url' => $padHost . "reference/?build&go=1", 'options' => [ 'TIMEOUT' => 3600 ] ] );

?>
