<?php

  if ( $GLOBALS ['padInfo'] )
    include PAD . 'events/functionsFast.php';

  return include PAD . "functions/$eval.php";

?>