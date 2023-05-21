<?php

  function diff ( $old, $new ) {

    $diff = Diff::toTable(Diff::compare($old,$new));   
    $diff = str_replace('</span><br></td>', '</span></td>', $diff);   
    $diff = str_replace('<span></span><br><span> </span>', '', $diff);
    $diff = str_replace('<span></span>', '', $diff);
    $diff = str_replace('<span> </span>', '', $diff);
    $diff = str_replace('<span> ', '<span>', $diff);
    $diff = str_replace('<table class="diff">', '', $diff);
    $diff = str_replace('</table>', '', $diff);
    $diff = str_replace('<span>', '', $diff);
    $diff = str_replace('</span>', '', $diff);

    return $diff;

  }

  function getExta ( $base ) {

    $file = "$base.extra";

    if ( ! padExists ( $file ) )
      return [];

    $data = file_get_contents ($file);
    $array = padExplode ( $data , ',');

    return $array;

  }


  function getExtaFiles ($dir) {

    if ( ! padIsDir ( $dir) )
      return [];

    $files = [];

    $dir = new RecursiveDirectoryIterator ( $dir );
    $dir = new RecursiveIteratorIterator  ( $dir );

    foreach ( $dir as $one ) {

      $path = $one->getPathname();
      $file = str_replace ( padApp, '', $path );

      if ( substr ( $path, -1   ) == '.'        ) continue;
      if (  str_ends_with ( $file, '.DS_Store') ) continue;

      $files [] = $file;

    }

    return $files;

  }


  function onlyResult ( $file ) {

    $html = ( padExists( $file ) ) ? file_get_contents( $file ) : ''; 

    if ( strpos($html, '{demo') !== false or str_ends_with($file, 'index.html'))  
      return ',onlyResult';
    else
      return '';

  }


  function getPage ( $page, $ignoreErrors=0 ) {

    global $padHost, $padScript;

    $url  = "$padHost$padScript?$page&padInclude";
    $curl = padCurl ($url);

    if ( ! $ignoreErrors )
      if ( ! str_starts_with ( $curl ['result'], '2') )
        return padError ("Curl failed: $url");

    return $curl;
    
  }


  function getPageData ( $page ) {

    $curl = getPage ($page);

    return $curl ['data'];
    
  }




  function dirList ($dir) {

    $list = [];

    $dir = padApp . $dir;

    $directory = new DirectoryIterator ( $dir       );
    $iterator  = new IteratorIterator  ( $directory );

    foreach ( $iterator as $loop ) {

      if ( $loop->isDir() )
        continue;

      $one   = $loop->getPathname();
      $file  = str_replace($dir,  '', $one );
      $ext   = substr($file,    strrpos($file, '.')+1 );
      $item  = substr($file, 1, strrpos($file, '.')-1 );

      if ( substr($item, -4) == 'todo'      ) continue;
      if ( $item == 'index'                 ) continue;
      if ( $ext <> 'html' and $ext <> 'php' ) continue;

      $list [$item] ['item'] = $item;
      
    }

    ksort($list);

    return $list;

  }


  function recursivePages () {

    $directory = new RecursiveDirectoryIterator ( padApp );
    $iterator  = new RecursiveIteratorIterator  ( $directory );

    $pages = [];

    foreach ($iterator as $one) {

      $ext = $one->getExtension();

      if ( $ext == 'html' or $ext == 'php' ) { 

        $file = str_replace ( padApp, '', $one->getPathname() );
        $page = substr($file, 0, strrpos($file, '.')   );

        if ( substr ($page, 0, 1) <> '_' )
          $pages [$page] ['page'] = $page;

      }

    }

    ksort($pages);

    return $pages;

  }



  function padPages ( ) {

    $files = [];

    $directory = new RecursiveDirectoryIterator (padApp);
    $iterator  = new RecursiveIteratorIterator  ($directory);

    foreach ($iterator as $loop_info) {

      $file  = str_replace(padApp, '', $loop_info->getPathname() );
      $ext   = substr($file,    strrpos($file, '.')+1 );
      $item  = substr($file, 0, strrpos($file, '.')   );

      if ( strpos($item, 'error') !== FALSE       ) continue;
      if ( strpos($item, 'todo')  !== FALSE       ) continue;
      if ( strpos($item, 'development') !== FALSE ) continue;
      if ( strpos($item, 'test')  !== FALSE       ) continue;
      if ( strpos($item, '/_')    !== FALSE       ) continue;
      if ( substr($item, 0, 1) == '_'             ) continue;
      if ( $ext <> 'html' and $ext <> 'php'       ) continue;
      if ( $item == 'hello/html'                  ) continue;
      if ( $item == 'tags/restart'                ) continue;
      if ( $item == 'tags/rediect'                ) continue;
   
      $files [$item] ['item'] = $item;

    }

    ksort ($files);

    return $files;

  }


  function refLink () {

    global $padPage;

    if ( $padPage == 'reference/reference' )
      return TRUE;

    if ( $padPage == 'reference/show' )
      return TRUE;

    if ($padPage == 'index' or $padPage == 'reference/index' )
      return FALSE;

    $types = padData ('references');

    foreach ( $types as $key => $value ) {
      if ( str_starts_with ( $padPage, $value ['ref'] . '/' ) )
        return TRUE;

    }

    return FALSE;

  }


  function parts ( ) {
 
    if ( $GLOBALS['padPage'] == 'index')
      return [];

    $parts ['home'] ['part'] = 'home';
    $parts ['home'] ['link'] = 'index';    

    if ( $GLOBALS['padPage'] == 'reference/index') {

      $parts ['ref'] ['part'] = 'reference';
      $parts ['ref'] ['link'] = '';    

      return $parts;

    } 

    $refLink = refLink();
        
    if ( $refLink ) {
      $parts ['ref'] ['part'] = 'reference';
      $parts ['ref'] ['link'] = 'reference/index'; 
    }  

    if     ( $GLOBALS['padPage'] == 'reference/reference' )  $source = $GLOBALS['reference'];
    elseif ( $GLOBALS['padPage'] == 'reference/show' )       $source = $GLOBALS['item'];
    else                                                     $source = $GLOBALS['padPage'];

    $source = str_replace ( '/index', '', $source ); 
    $source = padExplode ( $source, '/' );

    $link = '';

    foreach ( $source as $key => $part ) {
          
      $link = ($link) ? "$link/$part" : $part;

      $parts [$key] ['part'] = $part;
 
      if ( $refLink and $key <> array_key_last ($source))
        $parts [$key] ['link'] = "reference/reference&reference=$link";
      elseif ( $key == array_key_last ($source) ) 
        $parts [$key] ['link'] = '';
      else
        $parts [$key] ['link'] =  $link;
   
    }

    return $parts;

  }


?>