<?php

  include PAD . 'call/_init.php';
  
  if ( file_exists ( $padCall ) ) {

    if ( $GLOBALS ['padInfo'] ) 
      include PAD . 'events/callStart.php';

    $padTry = 'call/_try';
    $padCallPHP = include PAD . 'try/try.php';

    if ( $GLOBALS ['padInfo'] ) 
      include PAD . 'events/callEnd.php';

  }

  include PAD . 'call/_exit.php';

 ?>