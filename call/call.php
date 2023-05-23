<?php

  $padCallPHP = $padCallOB = '';

  if ( ! padExists ( $padCall ) )
    return;

  ob_start();

  $padCallPHP = include $padCall;
  $padCallOB  = ob_get_clean();

  if ( is_object   ( $padCallPHP ) ) $padCallPHP = padToArray( $padCallPHP );
  if ( is_resource ( $padCallPHP ) ) $padCallPHP = padToArray( $padCallPHP );

  if ( isset ($GLOBALS ['padPageInsideFunction'] ) )
    foreach ( get_defined_vars() as $padK => $padV )
      if ( padValidStore ($padK)  and ! isset ($GLOBALS [$padK] ) )
        $GLOBALS [$padK] = $padV;

  if ( $padCallPHP === 1 )
    $padCallPHP = '';

?>