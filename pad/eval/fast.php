<?php

  global $padInfo;

  if ( $padInfo )
    include PAD . 'events/functionsFast.php';

  return include PAD . "functions/$eval.php";

?>
