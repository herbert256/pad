<?php

  $title     = 'Reference';
  $reference = PAD . "reference/pages";

  $directory = new RecursiveDirectoryIterator ($reference);
  $iterator  = new RecursiveIteratorIterator  ($directory);

  $dirs = [];

  foreach ($iterator as $dir) {

    $path = $dir->getPathname();
 
    $dir = str_replace($reference, '', $path);
    $dir = substr($dir, 1);

    if (substr($dir, -2) == '/.')  $dir = substr($dir, 0, -2);
    if (substr($dir, -3) == '/..') $dir = substr($dir, 0, -3);

   if ( $dir and $dir <> '.' and is_dir($path) and count(padDirList ($path)))
      $dirs [$dir] ['dir'] = $dir;
 
  }

  ksort($dirs);

?>