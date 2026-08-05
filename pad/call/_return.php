<?php

  // Shared tail for the call wrappers that want printable content: folds a returned array
  // into content with padMakeContent, flattens TRUE / FALSE / NULL and the bare 1 that PHP
  // hands back for a file without a return statement into '', and returns the file's echoed
  // output followed by its return value.

  if     ( is_array($padCallPHP) ) $padCallPHP = padMakeContent ( $padCallPHP );
  elseif ( $padCallPHP === TRUE  ) $padCallPHP = '';
  elseif ( $padCallPHP === FALSE ) $padCallPHP = '';
  elseif ( $padCallPHP === NULL  ) $padCallPHP = '';
  elseif ( $padCallPHP === 1     ) $padCallPHP = '';

  return $padCallOB . $padCallPHP;

?>