<?php

  $padBuildBase = '@pad@';

  if ( $padInclude ) 
    return $padBuildBase;

  foreach ( $padBuildDirs as $padBuildDir ) {

    $padBuildInit = str_replace ( '@content@', '@pad@', padFileGetContents ( "$padBuildDir/_inits.pad" ) );
    $padBuildExit = str_replace ( '@content@', '@pad@', padFileGetContents ( "$padBuildDir/_exits.pad" ) );

    if ( strpos($padBuildInit, '@pad@') === FALSE and strpos($padBuildExit, '@pad@') === FALSE  )
      $padBuildInit .= '@pad@';

    if ( strpos($padBuildInit, '@pad@') !== FALSE )
      $padBuildBaseNow = str_replace ( '@pad@', "@pad@$padBuildExit", $padBuildInit );
    else
      $padBuildBaseNow = str_replace ( '@pad@', "$padBuildInit@pad@", $padBuildExit );

    $padBuildBase = str_replace ( '@pad@', $padBuildBaseNow, $padBuildBase );

  } 
   
  return $padBuildBase;
  
?>