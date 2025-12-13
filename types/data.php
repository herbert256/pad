<?php

  if ( padTagParm ('print') ) {
    $padGetName = padTagParm ('print');
    include PAD . 'options/print.php';
  }

  return $padDataStore [$padTag [$pad]];
 
?>