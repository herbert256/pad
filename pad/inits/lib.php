<?php

  // Loads the framework's own function library: every .php file under pad/lib/, its
  // subdirectories too, include_once so a restart cannot redeclare them.
  //
  // This is what makes padError(), padFileGet(), db(), padTagParm() and the rest available
  // to the engine, to application _lib/ code and to templates. It runs second in
  // inits/inits.php, immediately after the constants, because everything later calls into it.
  //
  // The directories are read with scandir: the RecursiveDirectoryIterator it walked them
  // with took a quarter of a small page's time, the files themselves far less.
  //
  // The code of one built-in tag is not here but in tags/_lib/<tag>.php, which types/pad.php
  // includes when a page uses the tag.

  $padLibDirs = [ PAD . 'lib' ];

  while ( $padLibDir = array_shift ( $padLibDirs ) )
    foreach ( scandir ( $padLibDir ) as $padLibOne )
      if ( str_ends_with ( $padLibOne, '.php' ) )
        include_once "$padLibDir/$padLibOne";
      elseif ( $padLibOne [0] != '.' and is_dir ( "$padLibDir/$padLibOne" ) )
        $padLibDirs [] = "$padLibDir/$padLibOne";

?>
