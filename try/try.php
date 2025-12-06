<?php 

  if ( ! $GLOBALS ['padErrorTry'] )
    return include PAD . "$padTry.php";

  try {

    return include PAD . "$padTry.php";

  } catch (Throwable $padTryException) {

    padErrorGo ( 
      'CATCH: ' .
      $padTryException->getMessage(),
      $padTryException->getFile(),
      $padTryException->getLine()
    );

    return include PAD . "try/catch/$padTry.php";

  }

?>