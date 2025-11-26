<?php

  function padRegression ( $sequence=0 ) {

    set_time_limit ( 15 );

    foreach ( padList ( 0 ) as $one ) {

      $item = $one ['item'];

      if ( ! file_exists ( APP . "$item.pad" ) )
        continue;

      if (   $sequence and ! str_contains ( $item, 'sequence' ) ) continue;
      if ( ! $sequence and   str_contains ( $item, 'sequence' ) ) continue;
      if (   $sequence and   str_contains ( $item, 'develop'  ) ) continue;

      $store  = APP . "_regression/$item.html";
      $check  = APP . "$item.pad";
      $source = padFileGet($check);

      file_put_contents ( APP . "_regression.txt", $item ) ;

      if     ( ! $source                                  ) $status = 'no';
      elseif ( strpos ( $source, 'PAD: SKIP REGRESSION' ) ) $status = 'no';
      elseif ( strpos ( $store, 'error' )                 ) $status = 'no';
      elseif ( strpos ( $store, 'test' )                  ) $status = 'no';
      elseif ( strpos ( $store, 'restart' )               ) $status = 'no';
      elseif ( strpos ( $store, 'redirect' )              ) $status = 'no';
      elseif ( strpos ( $store, 'deep' )                  ) $status = 'no';
      else                                                  $status = 'go';

      if ( $status == 'go' ) {

        $old  = padFileGet($store);
        $curl = getPage ($item, 1, 1);
        $good = str_starts_with ( $curl ['result'], '2');
        $new  = $curl ['data'] ?? '';
        $new  = str_replace ( "\r\n", "\n", $new );

        if     ( ! $good                            ) $status = 'error';
        elseif ( ! file_exists ($store)             ) $status = 'new';
        elseif ( strpos($source, '{example')        ) $status = 'skip' ;
        elseif ( strpos($source, '{ajax')           ) $status = 'skip' ;
        elseif ( strpos($source, 'random')          ) $status = 'random' ;
        elseif ( strpos($source, 'shuffle')         ) $status = 'random' ;
        elseif ( strpos($source, 'chance')          ) $status = 'random' ;
        elseif ( strpos($new, 'padAjax')            ) $status = 'skip' ;
        elseif ( $old == $new                       ) $status = 'ok';
        else                                          $status = 'warning';

        if ( $status == 'new' ) {
          padFileChkDir     ( $store );
          padFileChkFile    ( $store );
          file_put_contents ( $store, $new ) ;
        }

      }

      if ( $status == 'new' )
        $status = 'ok';

      $store = APP . "_regression/$item.txt";
      padFileChkDir     ( $store );
      padFileChkFile    ( $store );
      file_put_contents ( $store, $status ) ;

    }

  }


  function pqTypes () {
  
    return array_diff ( scandir ( 'sequence/types' ), [ '.', '..' ] ) ;

  }


  function pqActions () {

    $array = array_diff ( scandir ( 'sequence/actions/types' ), [ '.', '..' ] ) ;

    foreach ( $array as &$str ) 
      $str = str_replace ( '.php', '', $str );

    return $array;

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
      if ( padFileContains ( $one ['path'], '{page')    ) continue;
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