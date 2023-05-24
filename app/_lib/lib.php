<?php


  function padPages () {

    $directory = new RecursiveDirectoryIterator (padApp);
    $iterator  = new RecursiveIteratorIterator  ($directory);

    foreach ($iterator as $one ) {

      $path  = $one->getPathname();
      $file  = str_replace(padApp, '', $path );
      $ext   = substr($file,    strrpos($file, '.')+1 );
      $item  = substr($file, 0, strrpos($file, '.')   );
      $dir   = substr($item, 0, strrpos($item, '/')   );
      $file  = substr($item,    strrpos($item, '/')+1 );
 
      if ( ! $dir                           ) continue;
      if ( strpos($path, 'error')           ) continue;
      if ( strpos($path, 'todo')            ) continue;    
      if ( strpos($path, 'restart')         ) continue;
      if ( strpos($path, 'redirect')        ) continue;
      if ( strpos($path, '/_')              ) continue;
      if ( $ext <> 'html' and $ext <> 'php' ) continue;
      if ( $item == 'hello/html'            ) continue;
 
      $files [$item] ['item'] = $item;
      $files [$item] ['dir']  = $dir;
      $files [$item] ['file'] = $file;
    
    }

    ksort ($files);

    return $files;

  }


  function padPagesFiltered () {

    $work = $result = padPages ();

    foreach ( $work as $one )
      if ( $one ['file'] == 'index' )
        foreach ($result as $key => $value )
          if ( $value ['dir'] == $one ['dir'] and $value ['file'] <> 'index')
            unset ( $result [$key] );

    return $result;

  }


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

    if ( ! $ignoreErrors and ! str_starts_with ( $curl ['result'], '2') )
      return padError ("Curl failed: $url");

    return $curl;
    
  }


  function getPageData ( $page, $ignoreErrors=0  ) {

    $curl = getPage ($page, 1);

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


  function refLink () {

    global $padPage;

    if ( $padPage == 'reference' )
      return TRUE;

    if ( str_starts_with ($padPage, 'show' ) )
      return TRUE;

    if ($padPage == 'index' or $padPage == 'index' )
      return FALSE;

    $types = padData ('references');

    foreach ( $types as $key => $value ) {
      if ( str_starts_with ( $padPage, $value ['ref'] . '/' ) )
        return TRUE;

    }

    return FALSE;

  }


  function parts ( ) {

    global $padPage; 

    if ( $padPage == 'index')
      return [];

    $parts ['home'] ['part'] = 'home';
    $parts ['home'] ['link'] = 'index';    

    if ( $padPage == 'references') {

      $parts ['ref'] ['part'] = 'reference';
      $parts ['ref'] ['link'] = '';    

      return $parts;

    } 

    $refLink = refLink();
        
    if ( $refLink ) {

      $parts ['ref'] ['part'] = 'reference';
      $parts ['ref'] ['link'] = 'references'; 
 
    } elseif ( ! strpos( $padPage, '/') and $padPage <> 'index' and $padPage <> 'development' ) {

      $parts ['dev'] ['part'] = 'development';
      $parts ['dev'] ['link'] = 'development'; 

      $parts ['now'] ['part'] = $padPage;
      $parts ['now'] ['link'] = ''; 

      return $parts;

    }  


    if     ( $padPage == 'reference'             ) $source = $GLOBALS['reference'];
    elseif ( str_starts_with ($padPage, 'show' ) ) $source = $GLOBALS['item'];
    else                                           $source = $padPage;

    $source = str_replace ( '/index', '', $source ); 
    $source = padExplode ( $source, '/' );

    $link = '';

    foreach ( $source as $key => $part ) {
          
      $link = ($link) ? "$link/$part" : $part;

      $parts [$key] ['part'] = $part;
 
      if ( $refLink and $key <> array_key_last ($source))
        $parts [$key] ['link'] = "reference&reference=$link";
      elseif ( $key == array_key_last ($source) ) 
        $parts [$key] ['link'] = '';
      else
        $parts [$key] ['link'] =  $link;
   
    }

    return $parts;

  }


  function getReferences () {

    $references = padData ('references.json');

    foreach ( $references as $key => $type ) {

      $index = padApp . $type['ref'] . '/index';

      if ( padExists ("$index.php") or padExists ("$index.html") )
        $references [$key] ['link'] = TRUE;
      else
        $references [$key] ['link'] = FALSE;

    }

    return $references;

  }


  function refs ($type, $ref, $dir, $kind) {

    if ( !$type )
      return [];
    
    if ($kind == 'ref')
      $dir = $ref;
    
    if ( $type == 'eval types' ) {
      $one   = refs ('n/a', 'eval', 'eval/single', 'pad');
      $two   = refs ('n/a', 'eval', 'eval/parms',  'pad') ;
      $items = array_merge ( $one, $two );
      ksort ($items);
      return $items;
    }

    $manual = padApp . $ref;

    if ( $kind == 'pad' )
      $source = pad . $dir;
    else
      $source = $manual;

    $directory = new DirectoryIterator ( $source );
    $iterator  = new IteratorIterator  ( $directory );

    $items = [];
    
    foreach ($iterator as $one) {

      $ext  = $one->getExtension();
      $file = $one->getFilename();

      if ( $kind == 'pad' and $type <>  'sequences' )
        if ( $ext <> 'html' and $ext <> 'php' ) 
          continue;

      if ( $file == '.'  ) continue;
      if ( $file == '..' ) continue;

      if ( $kind == 'ref' and $one->isDir() )
        $item = $one->getBasename();
      elseif ( $type == 'sequences' )
        $item = $one->getBasename();
      else
        $item = substr($file, 0, strrpos($file, '.')   );

      if ( substr ( $item, 0, 1 ) == '_' ) continue;
      if ( $item == 'todo' )               continue;
      if ( $item == 'code' )               continue;

      $items [$item] ['item'] = $item;

      $check = "$manual/$item";

      if ( $kind == 'ref' )
        $items [$item] ['link'] = TRUE;
      elseif ( padExists ("$check.php") or padExists ("$check.html") or padIsDir ($check) )
        $items [$item] ['link'] = TRUE;
      else
        $items [$item] ['link'] = FALSE;
 
    }

    ksort ( $items );
    return $items;

  }


?>