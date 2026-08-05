<?php

  // Includes every .php file under the application's _database/ directory, recursively, the
  // same way inits/lib.php loads pad/lib/ - the intended home for an application's schema or
  // connection code.
  //
  // Nothing includes this file and no application ships a _database/ directory, so it is
  // currently dead; note it would fatal on the RecursiveDirectoryIterator if the directory
  // were missing, so a caller would have to guard it.

  $padLibDirectory = new RecursiveDirectoryIterator ( APP . '_database' );
  $padLibIterator  = new RecursiveIteratorIterator  ( $padLibDirectory );

  foreach ( $padLibIterator as $padLibOne ) {

    $padLibFile = str_replace ('\\', '/' , $padLibOne->getPathname() );

    if ( substr($padLibFile, -4) == '.php' )
      include_once $padLibFile;

  }

?>