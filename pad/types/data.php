<?php

  if ( padTagParm ('print') )
    include PAD . 'options/print.php';

  return $padDataStore [$padTag [$pad]];
 
?>