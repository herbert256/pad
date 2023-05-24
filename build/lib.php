<?php

  $padBuildNow = $padBuildBas;  

  foreach ($padBuildMrg as $padBuildValue) {

    $padBuildNow .= "/$padBuildValue";
    
    if ( is_dir ($padBuildNow) and is_dir ("$padBuildNow/_lib") ) {

      $padLibDirectory = new RecursiveDirectoryIterator ("$padBuildNow/_lib");
      $padLibIterator  = new RecursiveIteratorIterator  ($padLibDirectory);

      foreach ( $padLibIterator as $padLibOne ) {

        $padLibFile = $padLibOne->getPathname();

        if ( substr($padLibFile, -4) == '.php' )
          include_once $padLibFile;

        if ( substr($padLibFile, -5) == '.html' )
          $padBase [$pad] .= padFileGetContents ( $padLibFile );

      }

    }
    
  }

?>