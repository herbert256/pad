<?php

  $padLib_directory = new RecursiveDirectoryIterator ('/home/herbert/pad');
  $padLib_iterator  = new RecursiveIteratorIterator  ($padLib_directory);

  foreach ( $padLib_iterator as $padLib_one ) {

    $padLib_file = $padLib_one->getPathname();

    if ( substr($padLib_file, -4) == '.php' ) {

      $array = file ($padLib_file);

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

  $padLib_directory = new RecursiveDirectoryIterator ('/home/herbert/pad');
  $padLib_iterator  = new RecursiveIteratorIterator  ($padLib_directory);

  foreach ( $padLib_iterator as $padLib_one ) {

    $padLib_file = $padLib_one->getPathname();

    if ( substr($padLib_file, -4) == '.php' ) {

      $data = file_get_contents( $padLib_file);

      foreach ($functions as $old => $new )
        $data = str_replace($old, $new, $data ) ;

      $data = file_put_contents( $padLib_file, $data);

    }

  }

  dump();
 
?>