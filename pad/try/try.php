<?php

  global $padErrorTry;

  if ( ! $padErrorTry )
    return include PAD . "$padTry.php";

  try {

    return include PAD . "$padTry.php";

  } catch (Throwable $padTryException) {

    return include PAD . "try/catch/$padTry.php";

  }

?>