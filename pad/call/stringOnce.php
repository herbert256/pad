<?php

  $padCallPHP = $padCallOB = '';

  if ( ! file_exists ( $padCall ) )
    return '';

  include '/pad/call/_once.php';

  if     ( is_array($padCallPHP) ) $padCallPHP = padMakeContent ( $padCallPHP );
  elseif ( $padCallPHP === TRUE  ) $padCallPHP = '';
  elseif ( $padCallPHP === FALSE ) $padCallPHP = '';
  elseif ( $padCallPHP === NULL  ) $padCallPHP = '';
  elseif ( $padCallPHP === 1     ) $padCallPHP = '';

  return $padCallOB . $padCallPHP;

?>