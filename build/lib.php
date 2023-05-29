<?php

  $padBuildLib = '';

  foreach ( $padBuildDirs as $padBuildDir )

    if ( is_dir ("$padBuildDir/_lib") ) {

      $padLibDirectory = new RecursiveDirectoryIterator ("$padBuildDir/_lib");
      $padLibIterator  = new RecursiveIteratorIterator  ($padLibDirectory);

      foreach ( $padLibIterator as $padLibOne ) {

        $padCall = $padLibOne->getPathname();

        if ( substr($padCall, -4) == '.php' ) 
          $padBuildLib .= include pad . 'call/string_once.php';
 
        if ( substr($padCall, -5) == '.html' )
          $padBuildLib .= padFileGetContents ( $padCall );

      }

    }
    
  return $padBuildLib;

?>