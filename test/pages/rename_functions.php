<?php

  $padLibDirectory = new RecursiveDirectoryIterator ('/home/herbert/pad');
  $padLibIterator  = new RecursiveIteratorIterator  ($padLibDirectory);

  foreach ( $padLibIterator as $padLibOne ) {

    $padLibFile = $padLibOne->getPathname();

    if ( substr($padLibFile, -4) == '.php' ) {

      $array = file ($padLibFile);

      foreach ( $array as $line)
        if ( strpos($line, 'function') ) {

          $line = str_replace('(', ' (', $line);
          
          $parts = preg_split("/[\s]+/", $line, 9, PREG_SPLIT_NO_EMPTY);
          
          if ( $parts[0] == 'function' 
             and substr ($parts[1], 0, 1) === 'p' 
             and substr ($parts[1], 0, 3) <> 'pad'  
             and substr ($parts[2], 0, 1) === '(' 
             and $parts[1] <> 'p'
           ) {

            $words = padExplode ($parts[1], '_');
            $new = 'pad' . substr ( $words [0], 1) ;

            if ( count ($words) )
              foreach ($words as $key => $value)
                if ($key)
                  $new .= ucfirst($value);

            if ( $parts[1] <> $new )
              $functions [$parts[1]] = $new;

          }
                  
        }

    }

  }

  arsort($functions);

  $padLibDirectory = new RecursiveDirectoryIterator ('/home/herbert/pad');
  $padLibIterator  = new RecursiveIteratorIterator  ($padLibDirectory);

  foreach ( $padLibIterator as $padLibOne ) {

    $padLibFile = $padLibOne->getPathname();

    if ( substr($padLibFile, -4) == '.php' ) {

      $data = file_get_contents( $padLibFile);

      foreach ($functions as $old => $new )
        $data = str_replace($old, $new, $data ) ;

      $data = file_put_contents( $padLibFile, $data);

    }

  }

  dump();
 
?>