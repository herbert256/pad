<?php

  function padCut (&$content, $start, $end) {

    $cut = '';

    $p1 = strpos($content, $start);
    $p2 = strpos($content, $end);
  
    if ( $p1 !== FALSE and $p2 !== FALSE and $p1 < $p2 ) {

      $part1 = substr ($content, 0, $p1);
      $part2 = substr ($content, $p2+strlen($end) );

      $p1 += strlen($start);

      $cut     = substr ($content, $p1, $p2-$p1);      
      $content = $part1 . $part2;

      return $cut;

    } 

    $content = '';
    return '';

  }

  function padPages () {

    $directory = new RecursiveDirectoryIterator (padApp);
    $iterator  = new RecursiveIteratorIterator  ($directory);

    foreach ($iterator as $one ) {

      $path  = padCorrectPath ( $one->getPathname() );
      $file  = str_replace(padApp, '', $path );
      $ext   = substr($file,    strrpos($file, '.')+1 );
      $item  = substr($file, 0, strrpos($file, '.')   );
      $dir   = substr($item, 0, strrpos($item, '/')   );
      $file  = substr($item,    strrpos($item, '/')+1 );
 
      if ( ! $dir                           ) continue;
      if ( strpos($path, 'error')           ) continue;
      if ( strpos($path, 'restart')         ) continue;
      if ( strpos($path, 'redirect')        ) continue;
      if ( strpos($path, '/deep/')        ) continue;
      if ( strpos($path, '.settings.')      ) continue;
      if ( strpos($path, '/_')              ) continue;
      if ( $ext <> 'pad' and $ext <> 'php'  ) continue;
      if ( $item == 'hello/pad'             ) continue;
      if ( go ( $item )                     ) continue;

      $files [$item] ['path'] = $path;
      $files [$item] ['item'] = $item;
      $files [$item] ['dir']  = $dir;
      $files [$item] ['file'] = $file;
    
    }

    ksort ($files);

    return $files;

  }


  function padPagesFiltered () {

    $work = $result = padPages ();

    foreach ( $work as $one ) {

      if ( $one ['file'] == 'index' )
        foreach ($result as $key => $value )
          if ( $value ['dir'] == $one ['dir'] and $value ['file'] <> 'index')
            unset ( $result [$key] );

      if ( strpos ( file_get_contents ( $one ['path'] ), '<!-- PAD: NO ALL -->') ) 
        unset ( $result [$one['item']] );
 
    }

    return $result;

  }


?>