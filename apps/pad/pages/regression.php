 <?php

  $bench = ['php', 'user', 'app', 'read', 'write', 'curl', 'sql', 'cache'];

  $files = [];

  $path      = PAD_APPS . 'manual/pages/';
  $directory = new RecursiveDirectoryIterator ($path);
  $iterator  = new RecursiveIteratorIterator  ($directory);

  foreach ($iterator as $loop_info) {

    $file  = str_replace($path, '', $loop_info->getPathname() );
    $ext   = substr($file,    strrpos($file, '.')+1 );
    $item  = substr($file, 0, strrpos($file, '.')   );

    if ( strpos($item, '/') === FALSE     ) continue; 
    if ( substr($item, -4)  ==  'info'    ) continue;
    if ( substr($item, -5)  ==  'index'   ) continue;
    if ( substr($item, -5)  ==  'inits'   ) continue;
    if ( substr($item, -5)  ==  'exits'   ) continue;
    if ( $ext <> 'html' and $ext <> 'php' ) continue;

    $files [$item] ['item'] = $item;
    
  }

  ksort($files);

  foreach ($files as $item => $one) {

    $store = PAD_DATA . "regression/" . $item;

    $one ['url'] = "$pad_host$pad_script?app=manual&page=$item";

    pad_curl ($one, $curl);

    $timings = isset ($curl ['headers'] ['X-PAD-Timings']) ? json_decode ($curl ['headers'] ['X-PAD-Timings'], TRUE) : [];
      
    foreach ($bench as $wrk)
      $one [$wrk] = ($one[$wrk]??0) + ($timings[$wrk]??0);

    if ( $curl ['result_code'] <> 200 )
      $one ['status'] = $curl ['result_code'] ;
    elseif ( ! file_exists ("$store.html") ) {
      $one ['status'] = 'new';
      pad_check_file ("$store.html");
      file_put_contents ("$store.html", $curl ['data'] );
    } elseif ( file_get_contents("$store.html") == $curl ['data'] )
      $one ['status'] = 'ok';
    else
      $one ['status'] = 'error';

    $files [$item] = $one;

  }


?>