<?php

  if ( padTagParm ('print') )
    include PAD . 'pad/options/print.php';

  return $padDataStore [$padTag [$pad]];
 
?>