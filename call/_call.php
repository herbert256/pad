<?php

  $padCallPHP = $padCallOB = '';

  $padHistory [] = "Call: $padCall";

  ob_start();
  $padCallPHP = include $padCall;
  $padCallOB  = ob_get_clean();

  include pad . 'call/_clean.php';

 ?>