<?php

  function get_reference_files () {

    $files     = [];
    $path      = APP . 'pages/reference/';
    $directory = new RecursiveDirectoryIterator ($path);
    $iterator  = new RecursiveIteratorIterator  ($directory);

    foreach ($iterator as $loop_info) {

      $file  = str_replace($path, '', $loop_info->getPathname() );
      $ext   = substr($file,    strrpos($file, '.')+1 );
      $item  = substr($file, 0, strrpos($file, '.')   );

      if ( ! strpos($item, '/')             ) continue;
      if ( strpos($item, 'error') !== FALSE ) continue;
      if ( substr($item,  -5) == 'inits'    ) continue;
      if ( substr($item,  -5) == 'exits'    ) continue;
      if ( $ext <> 'html' and $ext <> 'php' ) continue;

      if ( ! in_array($item, $files) )
        $files [] = $item;

    }

    sort ($files);

    return $files;

  }

?>