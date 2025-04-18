<?php

  include 'call/_init.php';

  if ( file_exists ( $padCall ) ) {

    if ( $GLOBALS ['padInfo'] ) 
      include 'events/callStart.php';

    $padCallPHP = include_once $padCall;

    if ( $GLOBALS ['padInfo'] ) 
      include 'events/callEnd.php';

  }
  
  include 'call/_exit.php';

 ?>