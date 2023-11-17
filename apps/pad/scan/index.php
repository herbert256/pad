<?php

  $tags    = [];
  $options = [];
  $zzz     = [];

  $directory = new RecursiveDirectoryIterator (padApp);
  $iterator  = new RecursiveIteratorIterator  ($directory);

  foreach ($iterator as $one ) {

    $file = padCorrectPath ( $one->getPathname() );

    if ( str_ends_with ( $file , '.pad') ) {

      $source = padFileGetContents($file);

      $file = str_replace ( padApp, '', $file );

      if ( $file [0] <> '_' and ! strpos ( $file, '/_' ) )
        while ( $source )
          include 'scan.php';

    }
    
  }
    
  ksort ($tags);
  ksort ($options);
  ksort ($zzz);

?>