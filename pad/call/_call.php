<?php

  include 'call/_init.php';
  
  if ( file_exists ( $padCall ) )
    $padCallPHP = include $padCall;

  include 'call/_exit.php';

 ?>