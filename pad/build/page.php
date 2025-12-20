<?php

  $padBuildTrue = '';

  foreach ( $padBuildDirs as $padCall ) {
    $padCall .= '/_inits.php';
    $padBuildTrue .= include PAD . 'call/noOne.php';
  }

  $padCall = APP . "$padPage.php";
  $padBuildTrue .= include PAD . 'call/obNoOne.php';
  $padBuildCall = $padCallPHP;

  if ( $padCallPHP === NULL )
    return '';

  if ( is_array ( $padCallPHP ) ) $padBuild = padData ( $padCallPHP );
  else                            $padBuild = padDefaultData();

  if ( ! is_array ($padCallPHP) and $padCallPHP !== TRUE and $padCallPHP !== FALSE )
    $padBuildTrue .= $padCallPHP;

  $padBuildTrue .= padFileGet ( APP . "$padPage.pad" );

  foreach ( array_reverse ($padBuildDirs) as $padCall ) {
    $padCall .= '/_exits.php';
    $padBuildTrue .= include PAD . 'call/noOne.php';
  }

  include PAD . 'build/split.php';

  if ( $padBuildCall === FALSE or ! count ($padBuild) )
    return $padBuildFalse;
  elseif ( padIsDefaultData ( $padBuild) )
    return $padBuildTrue;
  else
    return "{padBuild for=\"$padPage\"}$padBuildTrue{/padBuild}";

?>