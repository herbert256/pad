<?php

  $padBuildLib = '';

  foreach ( $padBuildDirs as $padBuildDir )

    if ( is_dir ("$padBuildDir/_lib") ) {

      $padLibDirectory = new RecursiveDirectoryIterator ("$padBuildDir/_lib");
      $padLibIterator  = new RecursiveIteratorIterator  ($padLibDirectory);

      foreach ( $padLibIterator as $padLibOne ) {

        $padCall = $padLibOne->getPathname();

        if ( substr($padCall, -4) == '.php' ) 
          $padBuildLib .= include '/pad/call/stringOnce.php';
 
        if ( substr($padCall, -4) == '.pad' )
          $padBuildLib .= padFileGetContents ( $padCall );

      }

    }
    
  return $padBuildLib;

?>