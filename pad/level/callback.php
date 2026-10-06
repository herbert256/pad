<?php

  // Entry point for the callback= option. With the before option the whole data set is put
  // through the callback up front (callback/before.php); otherwise only the 'init' pass
  // runs here and callback/row.php fires per occurrence, callback/exit.php at level end.

  // Inside a pass that runs in a PHP function the init phase goes through the same lift as
  // the row phase - see occurrence/occurrence.php.

  if ( isset($padPrm [$pad] ['before']) )
    include PAD . 'callback/before.php';
  elseif ( $GLOBALS ['padStrFunCnt'] ?? 0 )
    padCallbackBeforeXxx ( 'init' );
  else
    include PAD . 'callback/init.php' ;

?>
