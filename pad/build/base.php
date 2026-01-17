<?php

  $padBuildBase = '@page@';

  if ( $padInclude )
    return $padBuildBase;

  foreach ( $padBuildDirs as $padBuildDir ) {

    $padBuildInit = padFileGet ( "$padBuildDir/_inits.pad" );
    $padBuildExit = padFileGet ( "$padBuildDir/_exits.pad" );

    if ( strpos($padBuildInit, '@page@') === FALSE and strpos($padBuildExit, '@page@') === FALSE  )
      $padBuildInit .= '@page@';

    if ( strpos($padBuildInit, '@page@') !== FALSE )
      $padBuildBaseNow = str_replace ( '@page@', "@page@$padBuildExit", $padBuildInit );
    else
      $padBuildBaseNow = str_replace ( '@page@', "$padBuildInit@page@", $padBuildExit );

    $padBuildBase = str_replace ( '@page@', $padBuildBaseNow, $padBuildBase );

  }

  return str_replace ( '@page@', $padBuildBase, padFileGet ( COMMON . '_inits.pad' ) );

?>