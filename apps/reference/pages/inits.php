<?php

  $join = FALSE;

  $pad_sql_host      = '127.0.0.1';
  $pad_sql_database  = 'demo';
  $pad_sql_user      = 'demo';
  $pad_sql_password  = 'demo';

  function pad_dir_list ($dir) {

    if ( substr($dir, -2) == '..' ) return [];
    if ( ! is_dir($dir)           ) return [];

    $list = [];

    $directory = new DirectoryIterator ($dir);
    $iterator  = new IteratorIterator  ($directory);

    foreach ( $iterator as $loop_info ) {

      $one   = $loop_info->getPathname();
      $file  = str_replace($dir, '', $one );
      $ext   = substr($file,    strrpos($file, '.')+1 );
      $item  = substr($file, 1, strrpos($file, '.')-1   );

      if ( substr($file, -1) == '.'         ) continue;
      if ( substr($file, -2) == '..'        ) continue;
      if ( is_dir($one)                     ) continue;
      if ( substr($item, -5) == 'inits'     ) continue;
      if ( substr($item, -5) == 'exits'     ) continue;
      if ( $ext <> 'html' and $ext <> 'php' ) continue;

      $list [$item] = $item;
      
    }

    sort($list);

    return $list;

  }

?>