<?php

  foreach ( $padBuildDirs as $padCall ) {
    $padCall .= '/_inits.php';
    $padBuildHtml .= include pad . 'call/stringNoOne.php';
  }

  $padCall = "$padBuildPath.php";
  include pad . 'call/callNoOne.php';

  if ( is_scalar ( $padCallPHP ) and ! in_array ($padCallPHP, [TRUE,FALSE,NULL]) ) {

    $padCallOB .= $padCallPHP;
    $padCallPHP = ''; 

  }

  if ( is_array ( $padCallPHP ) )
    $padBuildArray = padData ( $padCallPHP);

  if ( $padCallPHP !== NULL)
    foreach ( array_reverse ($padBuildDirs) as $padCall ) {
      $padCall .= '/_exits.php';
      $padBuildHtml .= include pad . 'call/stringNoOne.php';
    }

  if     ( $padCallPHP === TRUE       ) return TRUE;
  elseif ( $padCallPHP === FALSE      ) return FALSE;
  elseif ( $padCallPHP === NULL       ) return NULL;
  elseif ( ! is_array ( $padCallPHP ) ) return TRUE;
  elseif ( count ( $padCallPHP )      ) return TRUE;
  else                                  return FALSE;

?>