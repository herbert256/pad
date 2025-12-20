<?php

  if ( isset($padPrm [$pad] ['before']) )
    include PAD . 'callback/before.php';
  else
    include PAD . 'callback/init.php' ;

?>