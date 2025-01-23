<?php

  include 'call/_init.php';

  if ( file_exists ( $padCall ) )
    $padCallPHP = include_once $padCall;
  
  include 'call/_exit.php';

 ?>