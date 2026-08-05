<?php

  // Entry point for the callback= option. With the before option the whole data set is put
  // through the callback up front (callback/before.php); otherwise only the 'init' pass
  // runs here and callback/row.php fires per occurrence, callback/exit.php at level end.

  if ( isset($padPrm [$pad] ['before']) )
    include PAD . 'callback/before.php';
  else
    include PAD . 'callback/init.php' ;

?>