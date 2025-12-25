<?php

  function sequenceDir ( $dir )  {

    $out = [];

    foreach ( array_diff ( scandir ( $dir ), [ '.', '..' ] ) as $file ) {
      $key = str_replace( '.pad', '', str_replace( '.php', '', $file ) );
      $out [$key] = $key;
    }

    return array_values ( $out );

  }

  function getExtra ( $base ) {

    $file = "$base.extra";

    if ( ! file_exists ( $file ) )
      return [];

    $data = fileGet ($file);
    $data = str_replace ( "\n", ',', $data);
    $array = padExplode ( $data , ',');

    return $array;

  }

  function getExtraFiles ($dir) {

    if ( ! is_dir ( $dir) )
      return [];

    $files = [];

    $dir = new RecursiveDirectoryIterator ( $dir );
    $dir = new RecursiveIteratorIterator  ( $dir );

    foreach ( $dir as $one ) {

      $path = padCorrectPath ( $one->getPathname() );
      $file = str_replace ( APP, '', $path );

      if ( substr ( $path, -1   ) == '.'        ) continue;
      if (  str_ends_with ( $file, '.DS_Store') ) continue;

      $files [] = $file;

    }

    return $files;

  }


?>