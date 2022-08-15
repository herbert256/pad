<?php

  $staff = [
    [ 'name' => 'joe',   'salary' => 1000, 'bonus' =>  500, 'phone' => '555-1111'],
    [ 'name' => 'jim',   'salary' => 2000, 'bonus' =>  400, 'phone' => '555-2222'],
    [ 'name' => 'john',  'salary' => 3000, 'bonus' =>  300, 'phone' => '555-3333'],
    [ 'name' => 'jack',  'salary' => 4000, 'bonus' =>  200, 'phone' => '555-4444'],
    [ 'name' => 'jerry', 'salary' => 5000, 'bonus' =>  100, 'phone' => '555-5555']
  ];

  $join = TRUE;

  function padDirList ($dir) {

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