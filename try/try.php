<?php 

  if ( ! $GLOBALS ['padErrorTry'] )
    return include "$padTry.php";

  try {

    return include "$padTry.php";

  } catch (Throwable $padTryException) {

    padErrorGo ( 
      'CATCH: ' .
      $padTryException->getMessage(),
      $padTryException->getFile(),
      $padTryException->getLine()
    );

    return include "try/catch/$padTry.php";

  }

?>