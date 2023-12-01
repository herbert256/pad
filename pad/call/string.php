<?php

  $padCallPHP = $padCallOB = '';

  if ( ! file_exists ( $padCall ) )
    return '';

  include pad . 'call/_call.php';

  if ( is_array($padCallPHP) )

    $padCallPHP = padMakeContent ( $padCallPHP );
  
  elseif ( $padCallPHP === TRUE )

    $padCallPHP = '1';

  elseif ($padCallPHP === FALSE or $padCallPHP === NULL )
  
    $padCallPHP = '';

  if ( $padCallOB )
    return $padCallOB . $padCallPHP;
  else
    return $padCallPHP;

?>