<?php

  $curl   = padCurl ( [ 'url' => $padHost . 'playground/?render', 'post' => [ 'source' => '{php:getcwd}' ] ] );
  $answer = $curl ['result'] . ': ' . ( preg_match ( '/getcwd\S* is not allowed by/', $curl ['data'] ) ? 'refused' : 'ran' );

?>
