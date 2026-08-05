<?php

  // Closing half of a call: moves the echoed output into $padCallOB and normalises the
  // included file's return value in $padCallPHP, so callers only ever see a string, an array
  // or a plain scalar - objects and resources are flattened with padToArray, INF and NAN
  // become NULL.

  $padCallOB = ob_get_clean();

  if     ( is_object   ( $padCallPHP ) ) $padCallPHP = padToArray( $padCallPHP );
  elseif ( is_resource ( $padCallPHP ) ) $padCallPHP = padToArray( $padCallPHP );
  elseif ( $padCallPHP === INF         ) $padCallPHP = NULL;
  elseif ( is_float($padCallPHP) && is_nan($padCallPHP) ) $padCallPHP = NULL;

 ?>