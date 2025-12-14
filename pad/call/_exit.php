<?php

  $padCallOB = ob_get_clean();

  if     ( is_object   ( $padCallPHP ) ) $padCallPHP = padToArray( $padCallPHP );
  elseif ( is_resource ( $padCallPHP ) ) $padCallPHP = padToArray( $padCallPHP );
  elseif ( $padCallPHP === INF         ) $padCallPHP = NULL;
  elseif ( is_float($padCallPHP) && is_nan($padCallPHP) ) $padCallPHP = NULL;

 ?>