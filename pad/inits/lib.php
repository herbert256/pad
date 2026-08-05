<?php

  // Loads the framework's own function library: every .php file under pad/lib/, recursively,
  // include_once so a restart cannot redeclare them.
  //
  // This is what makes padError(), padFileGet(), db(), padTagParm() and the rest available
  // to the engine, to application _lib/ code and to templates. It runs second in
  // inits/inits.php, immediately after the constants, because everything later calls into it.

  $padLibDirectory = new RecursiveDirectoryIterator ( PAD . 'lib' );
  $padLibIterator  = new RecursiveIteratorIterator  ( $padLibDirectory );

  foreach ( $padLibIterator as $padLibOne ) {

    $padLibFile = str_replace ('\\', '/' , $padLibOne->getPathname() );

    if ( substr($padLibFile, -4) == '.php' )
      include_once $padLibFile;

  }

?>