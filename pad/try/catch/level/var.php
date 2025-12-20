<?php

  padErrorGo (
    'CATCH: ' .
    $padTryException->getMessage(),
    $padTryException->getFile(),
    $padTryException->getLine()
  );

  padLevel ( '' );

?>
