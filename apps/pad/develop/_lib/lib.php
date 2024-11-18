<?php


  function padSeqTypes () {
  
    return array_diff ( scandir ( PAD . 'seq/types' ), [ '.', '..' ] ) ;

  }
  
  
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


  function padList ( $filter = 1 ) {

    $directory = new RecursiveDirectoryIterator (APP);
    $iterator  = new RecursiveIteratorIterator  ($directory);

    foreach ($iterator as $one ) {

      $path  = padCorrectPath ( $one->getPathname() );
      $file  = str_replace(APP, '', $path );
      $ext   = substr($file,    strrpos($file, '.')+1 );
      $item  = substr($file, 0, strrpos($file, '.')   );
      $dir   = substr($item, 0, strrpos($item, '/')   );
      $file  = substr($item,    strrpos($item, '/')+1 );

      if ( strpos($path, '/_')             ) continue;
      if ( $ext <> 'pad' and $ext <> 'php' ) continue;
      if ( strpos($path, 'develop')        ) continue;     

      if ( $filter ) {
        if ( strpos($path, 'error')    ) continue;      
        if ( strpos($path, 'test')     ) continue;      
        if ( strpos($path, 'redirect') ) continue;      
        if ( strpos($path, 'deep')     ) continue;  
      }    

      $files [$item] ['path'] = $path;
      $files [$item] ['item'] = $item;
      $files [$item] ['dir']  = $dir;
      $files [$item] ['file'] = $file;
    
    }

    ksort ($files);

    return $files;

  }


  function padListFiltered () {

    foreach ( padList () as $one ) {

      if ( str_contains ( $one ['path'], 'develop')     ) continue;
      if ( str_contains ( $one ['path'], 'manual')      ) continue;
      if ( str_contains ( $one ['path'], 'xref')        ) continue;

      if ( padFileContains ( $one ['path'], '{example') ) continue;
      if ( padFileContains ( $one ['path'], '{page')     ) continue;
      if ( padFileContains ( $one ['path'], '{ajax')    ) continue;

      $list [] = $one;

    }

    return $list;
    
  }


  function deleteDir ( $dir ) {

    if ( ! file_exists ($dir) )
      return;
    
    foreach ( array_diff ( scandir ( $dir ), [ '.', '..' ] ) as $file )
      ( is_dir ( "$dir/$file" ) ) ? deleteDir ( "$dir/$file" ) : unlink ( "$dir/$file" );

    rmdir ( $dir );

  }


?>