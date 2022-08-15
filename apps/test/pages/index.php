<?php

  $padLibDirectory = new RecursiveDirectoryIterator ('/home/herbert/pad');
  $padLibIterator  = new RecursiveIteratorIterator  ($padLibDirectory);

  foreach ( $padLibIterator as $padLibOne ) {
    $padLibFile = $padLibOne->getPathname();
    if ( substr($padLibFile, -4) == '.php' ) 
      $files [] = $padLibFile;
  }

  foreach ( $files as $file ) {

    $data = file_get_contents ( $file );

    $pos =  strpos($data, '$pad');

    while ($pos !== FALSE) {

      $check01 = strpos($data, ' ',  $pos);  
      $check02 = strpos($data, '=',  $pos);  
      $check03 = strpos($data, ',',  $pos);  
      $check04 = strpos($data, ';',  $pos);  
      $check05 = strpos($data, ')',  $pos);  
      $check06 = strpos($data, "\t", $pos);  
      $check07 = strpos($data, "\r", $pos);  
      $check08 = strpos($data, "\n", $pos);  
      $check09 = strpos($data, ']',  $pos);  
      $check10 = strpos($data, '[',  $pos);  
      $check11 = strpos($data, "'",  $pos);  
      $check12 = strpos($data, '"',  $pos);  
      $check13 = strpos($data, '\\',  $pos);  
      $check14 = strpos($data, '/',  $pos);  
      $check15 = strpos($data, '-',  $pos);  
      $check16 = strpos($data, '.',  $pos);  
      $check17 = strpos($data, '+',  $pos);  
      $check18 = strpos($data, '<',  $pos);  
      $check19 = strpos($data, '>',  $pos);  
      $check20 = strpos($data, '{',  $pos);  
      $check21 = strpos($data, '}',  $pos);  
      $check22 = strpos($data, '?',  $pos);  
      $check23 = strpos($data, '#',  $pos);  
      $check24 = strpos($data, '*',  $pos);  

      $found = PHP_INT_MAX;

      if ($check01 !== FALSE and $check01 < $found) $found = $check01;
      if ($check02 !== FALSE and $check02 < $found) $found = $check02;
      if ($check03 !== FALSE and $check03 < $found) $found = $check03;
      if ($check04 !== FALSE and $check04 < $found) $found = $check04;
      if ($check05 !== FALSE and $check05 < $found) $found = $check05;
      if ($check06 !== FALSE and $check06 < $found) $found = $check06;
      if ($check07 !== FALSE and $check07 < $found) $found = $check07;
      if ($check08 !== FALSE and $check08 < $found) $found = $check08;
      if ($check09 !== FALSE and $check09 < $found) $found = $check09;
      if ($check10 !== FALSE and $check10 < $found) $found = $check10;
      if ($check11 !== FALSE and $check11 < $found) $found = $check11;
      if ($check12 !== FALSE and $check12 < $found) $found = $check12;
      if ($check13 !== FALSE and $check13 < $found) $found = $check13;
      if ($check14 !== FALSE and $check14 < $found) $found = $check14;
      if ($check15 !== FALSE and $check15 < $found) $found = $check15;
      if ($check16 !== FALSE and $check16 < $found) $found = $check16;
      if ($check17 !== FALSE and $check17 < $found) $found = $check17;
      if ($check18 !== FALSE and $check18 < $found) $found = $check18;
      if ($check19 !== FALSE and $check19 < $found) $found = $check19;
      if ($check20 !== FALSE and $check20 < $found) $found = $check20;
      if ($check21 !== FALSE and $check21 < $found) $found = $check21;
      if ($check22 !== FALSE and $check22 < $found) $found = $check22;
      if ($check23 !== FALSE and $check23 < $found) $found = $check23;
      if ($check24 !== FALSE and $check24 < $found) $found = $check24;

      $old = substr($data, $pos, $found - $pos);

      $words = padExplode ($old, '_');
      $new = $words [0];

      foreach ($words as $key => $value)
        if ($key)
          $new .= ucfirst($value);

      if ( $old <> $new )
        $vars [$old] = $new;

      $pos =  strpos($data, '$pad', $pos+1);

    }

  }

  arsort($vars);

  foreach ( $files as $file ) {

    $data = file_get_contents ( $file );

    foreach ($vars as $old => $new ) {

      $data = str_replace( $old, $new, $data ) ;

      $from = "'" . substr($old, 1) . "'";
      $to   = "'" . substr($new, 1) . "'";
      $data = str_replace( $from, $to, $data ) ;

      $from = '"' . substr($old, 1) . '"';
      $to   = '"' . substr($new, 1) . '"';
      $data = str_replace( $from, $to, $data ) ;
 
    }

    $data = file_put_contents( $file, $data);

  }

  padDump();
 
?>