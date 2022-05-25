<?php

  $title = "Regression test";
  $bench = ['all', 'app', 'read', 'write', 'curl', 'sql', 'cache'];

  $files     = [];
  $path      = APPS . 'reference/pages/';
  $directory = new RecursiveDirectoryIterator ($path);
  $iterator  = new RecursiveIteratorIterator  ($directory);

  foreach ($iterator as $loop_info) {

    $file  = str_replace($path, '', $loop_info->getPathname() );
    $ext   = substr($file,    strrpos($file, '.')+1 );
    $item  = substr($file, 0, strrpos($file, '.')   );

    if ( ! strpos($item, '/')             ) continue;
    if ( substr($item,  -5) == 'inits'    ) continue;
    if ( substr($item,  -5) == 'exits'    ) continue;
    if ( $ext <> 'html' and $ext <> 'php' ) continue;

    if ( isset ( $files [$item] ) )
      continue;

    $files [$item] ['item'] = $item;

    $store_write = "regression/$item.html";
    $store_read  = DATA . $store_write;

    $curl = pad_complete ('reference', $item);

    $timings = isset ($curl ['headers'] ['X-PAD-Timings']) ? json_decode ($curl ['headers'] ['X-PAD-Timings'], TRUE) : [];
      
    foreach ($bench as $wrk)
      $files [$item] [$wrk] = $timings[$wrk] ?? 0;

    if ( $curl ['result_code'] <> 200 )                         $status = $curl ['result_code'] ;
    elseif ( strrpos($store_write, 'random') )                  $status = 'random' ;
    elseif ( ! file_exists ($store_read) )                      $status = 'new';
    elseif ( file_get_contents($store_read) == $curl ['data'] ) $status = 'ok';
    else                                                        $status = 'error';

    if ( $status == 'new' )
      pad_file_put_contents ($store_write, $curl ['data'] );

    $files [$item] ['status'] = $status;

  }

 ksort ($files);


?>