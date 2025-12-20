<?php

  if  ( padTagParm ( 'notOk' ) )
    return include PAD . 'options/notOk.php';

  padErrorGo (
    'CATCH: ' .
    $padTryException->getMessage(),
    $padTryException->getFile(),
    $padTryException->getLine()
  );

?>
