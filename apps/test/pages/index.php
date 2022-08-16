<?php

  $padLibDirectory = new RecursiveDirectoryIterator ('/home/herbert/pad');
  $padLibIterator  = new RecursiveIteratorIterator  ($padLibDirectory);

  foreach ( $padLibIterator as $padLibOne ) {

    $padLibFile = $padLibOne->getPathname();

    if ( substr($padLibFile, -4) == '.php' ) {

      $array = file ($padLibFile);

      foreach ( $array as $line)
        if ( strpos($line, 'function') !== FALSE ) {

          $line = str_replace('function', ' function', $line);
          $line = str_replace('(', ' (', $line);
         
          $parts = preg_split("/[\s]+/", $line, 9, PREG_SPLIT_NO_EMPTY);

          if ( $parts[0] == 'function' and substr ($parts[2], 0, 1) === '(' and  substr($parts[1], 0, 11) == 'padSequence') 

              $functions [$parts[1]] = 'padSeq' . substr($parts[1], 11);
                  
        }

    }

  }

  arsort($functions);
  #dump();

  $padLibDirectory = new RecursiveDirectoryIterator ('/home/herbert/pad');
  $padLibIterator  = new RecursiveIteratorIterator  ($padLibDirectory);

  foreach ( $padLibIterator as $padLibOne ) {

    $padLibFile = $padLibOne->getPathname();

    if ( substr($padLibFile, -4) == '.php' ) {

      $data = file_get_contents( $padLibFile);

      foreach ($functions as $old => $new ) {

        $data = str_replace($old, $new, $data ) ;

      }

      $data = file_put_contents( $padLibFile, $data);

    }

  }

  dump();
 
?>