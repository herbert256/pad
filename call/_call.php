<?php

  ob_start();
  $padCallPHP = include $padCall;
  $padCallOB  = ob_get_clean();

  if     ( is_object   ( $padCallPHP ) ) $padCallPHP = padToArray( $padCallPHP );
  elseif ( is_resource ( $padCallPHP ) ) $padCallPHP = padToArray( $padCallPHP );
  elseif ( $padCallPHP === INF         ) $padCallPHP = NULL;
  elseif ( $padCallPHP === NAN         ) $padCallPHP = NULL;

 ?>