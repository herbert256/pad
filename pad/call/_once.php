<?php

  include 'call/_init.php';

  if ( file_exists ( $padCall ) ) {

    if ( $GLOBALS ['padInfo'] ) 
      include 'events/callStart.php';

    $padTry = 'call/_tryOnce';
    $padCallPHP = include 'try/try.php';

    if ( $GLOBALS ['padInfo'] ) 
      include 'events/callEnd.php';

  }
  
  include 'call/_exit.php';

 ?>