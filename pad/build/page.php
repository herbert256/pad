<?php

  $padBuildPad = '';
 
  foreach ( $padBuildDirs as $padCall ) {
    $padCall .= '/_inits.php';
    $padBuildPad .= include pad . 'call/stringNoOne.php';
  }

  $padCall = padApp . "$padPage.php";
  $padBuildPad .= include pad . 'call/callNoOne.php';

  if ( $padCallPHP === NULL )
    return '';

  if     ( is_array ( $padCallPHP ) ) $padBuild = padData ( $padCallPHP );
  elseif ( $padCallPHP === FALSE    ) $padBuild = [];
  else                                $padBuild = padDefaultData();
 
  if ( ! is_array ($padCallPHP) and $padCallPHP !== TRUE and $padCallPHP !== FALSE )
    $padBuildPad .= $padCallPHP;

  $padBuildPad .= padFileGetContents ( padApp . "$padPage.pad" );

  foreach ( array_reverse ($padBuildDirs) as $padCall ) {
    $padCall .= '/_exits.php';
    $padBuildPad .= include pad . 'call/stringNoOne.php';
  }

  if ( str_contains ($padBuildPad, '@else@') )
    return "{padBuild for=\"$padPage\"}$padBuildPad{/padBuild}";    
  elseif ( padIsDefaultData ( $padBuild) )
    return $padBuildPad;
  else
    return "{padBuild for=\"$padPage\"}$padBuildPad{/padBuild}";
  
?>