<?php

  foreach ( $padBuildDirs as $padCall ) {
    $padCall .= '/_inits.php';
    $padBuildHtml .= include pad . 'call/build.php';
  }

  $padCall = "$padBuildPath.php";
  include pad . 'call/call.php';

  if ( $padCallPHP === 1 ) 
    $padCallPHP = '';

  $padBuildOB = $padCallOB;

  if ( is_array ( $padCallPHP ) )
    $padBuildArray = padData ( $padCallPHP);
  else
    $padBuildArray = [];

  if ( $padCallPHP !== NULL)
    foreach ( array_reverse ($padBuildDirs) as $padCall ) {
      $padCall .= '/_exits.php';
      $padBuildHtml .= include pad . 'call/build.php';
    }

  if     ( $padCallPHP === TRUE       ) return TRUE;
  elseif ( $padCallPHP === FALSE      ) return FALSE;
  elseif ( $padCallPHP === NULL       ) return NULL;
  elseif ( ! is_array ( $padCallPHP ) ) return $padCallPHP;
  elseif ( count ( $padCallPHP )      ) return TRUE;
  else                                  return FALSE;

?>