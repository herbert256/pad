<?php

  $title     = 'All directories fromt the reference applicaton';
  $reference = APPS . "reference/pages";

  $directory = new RecursiveDirectoryIterator ($reference);
  $iterator  = new RecursiveIteratorIterator  ($directory);

  $dirs = [];

  foreach ($iterator as $dir) {

    $padath = $dir->getPathname();
 
    $dir = str_replace($reference, '', $padath);
    $dir = substr($dir, 1);

    if (substr($dir, -2) == '/.')  $dir = substr($dir, 0, -2);
    if (substr($dir, -3) == '/..') $dir = substr($dir, 0, -3);

   if ( $dir and $dir <> '.' and is_dir($padath) and count(padDirList ($padath)))
      $dirs [$dir] ['dir'] = $dir;
 
  }

  ksort($dirs);

?>