<?php

  $padBuildLib = '';

  foreach ( $padBuildDirs as $padBuildDir )

    if ( is_dir ("$padBuildDir/_lib") ) {

      $padLibDirectory = new RecursiveDirectoryIterator ("$padBuildDir/_lib");
      $padLibIterator  = new RecursiveIteratorIterator  ($padLibDirectory);

      foreach ( $padLibIterator as $padLibOne ) {

        $padCall = $padLibOne->getPathname();

        if ( substr($padCall, -4) == '.php' ) 
          $padBuildLib .= include PAD . 'call/once.php';
 
        if ( substr($padCall, -4) == '.pad' )
          $padBuildLib .= padFileGet ( $padCall );

      }

    }
    
  return $padBuildLib;

?>