<?php

  $padBuildHtml = '';
 
  foreach ( $padBuildDirs as $padCall ) {
    $padCall .= '/_inits.php';
    $padBuildHtml .= include pad . 'call/stringNoOne.php';
  }

  $padCall = padApp . "$padPage.php";
  $padBuildHtml .= include pad . 'call/callNoOne.php';

  if ( $padCallPHP === NULL )
    return '';

  if     ( is_array ( $padCallPHP ) ) $padBuild = padData ( $padCallPHP );
  elseif ( $padCallPHP === FALSE    ) $padBuild = [];
  else                                $padBuild = padDefaultData();
 
  if ( ! is_array ($padCallPHP) and $padCallPHP !== TRUE and $padCallPHP !== FALSE )
    $padBuildHtml .= $padCallPHP;

  $padBuildHtml .= padFileGetContents ( padApp . "$padPage.pad" );

  foreach ( array_reverse ($padBuildDirs) as $padCall ) {
    $padCall .= '/_exits.php';
    $padBuildHtml .= include pad . 'call/stringNoOne.php';
  }

  return "{padBuild}$padBuildHtml{/padBuild}";

?>