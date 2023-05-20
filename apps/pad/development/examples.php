<?php

  set_time_limit(30);

  $title = "All PAD pages with the example tag";

  $dirs = [];

  $source = padApp;

  $directory = new RecursiveDirectoryIterator ( $source    );
  $iterator  = new RecursiveIteratorIterator  ( $directory );

  foreach ($iterator as $one) {

    $path = $one->getPathname();
    $item = str_replace ($source, '', $path);
    $file = substr($item,    strrpos($item, '/')+1 );
    $dir  = substr($item, 0, strrpos($item, '/') );

    if ( ! $dir                           ) continue;
    if ( isset ($dirs [$dir])             ) continue;
    if ( strpos($path, '/_')              ) continue;
    if ( strpos($path, '/development/')   ) continue;
    if ( strpos($path, '/todo/')          ) continue;
    if ( substr($path, -1) == '.'         ) continue;
    
    $dirs [$dir] ['dir']   = $dir;
    $dirs [$dir] ['files'] = one ($dir);
 
  }

  ksort($dirs);

  function one ($dir) {

    $files = [];

    $directory = new DirectoryIterator ( padApp . $dir );
    $iterator  = new IteratorIterator  ( $directory );

    foreach ( $iterator as $one ) {

      $path = $one->getPathname();
      $item = substr($path, strrpos($path, '/')+1 );
      $ext  = substr($item,    strrpos($item, '.')+1);
      $file = substr($item, 0, strrpos($item, '.') );

      if ( $file == 'index')
        return [ $file => ['file' => $file] ];

      if ( $one->isDir()                    ) continue;
      if ( isset ($files [$file])           ) continue;
      if ( strpos($path, '/_')              ) continue;
      if ( strpos($path, 'restart')         ) continue;
      if ( strpos($path, 'redirect')        ) continue;
      if ( $ext <> 'html' and $ext <> 'php' ) continue;

      $files [$file] ['file'] = $file;      

    }

    return $files;

  }

  
?>