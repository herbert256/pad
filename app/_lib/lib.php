<?php

 function inDocumentation () {

    global $padPage;

    if ( ! count ($_REQUEST) )
      return FALSE;

    $types = padData ('references.json');
    $first = array_key_first ($_REQUEST);

    foreach ( $types as $key => $value ) 
      if ( str_starts_with ( $first, $value ['ref'] ) )
        return TRUE;


    return FALSE;

  }


  function go ( $item ) {

    $data = trim ( padFileGetContents ( padApp . "$item.html" ) );

    if ( ! str_starts_with( $data, '@go@') )
      return '';

    $parts = padExplode ( $data, '@');

    return $parts [1];

  }

  function notGo ( $item ) {

    return ( ! go ($item) );

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


  function getExtra ( $base ) {

    $file = "$base.extra";

    if ( ! padExists ( $file ) )
      return [];

    $data = file_get_contents ($file);
    $data = str_replace ( "\n", ',', $data);
    $array = padExplode ( $data , ',');

    return $array;

  }


  function getExtraFiles ($dir) {

    if ( ! padIsDir ( $dir) )
      return [];

    $files = [];

    $dir = new RecursiveDirectoryIterator ( $dir );
    $dir = new RecursiveIteratorIterator  ( $dir );

    foreach ( $dir as $one ) {

      $path = padCorrectPath ( $one->getPathname() );
      $file = str_replace ( padApp, '', $path );

      if ( substr ( $path, -1   ) == '.'        ) continue;
      if (  str_ends_with ( $file, '.DS_Store') ) continue;

      $files [] = $file;

    }

    return $files;

  }


  function onlyResult ( $file ) {

    $html = ( padExists( $file ) ) ? file_get_contents( $file ) : ''; 

    if ( strpos($html, '<!-- PAD: ONLYRESULT -->') !== false ) return ',onlyResult';
    if ( strpos($html, '{present')                 !== false ) return ',onlyResult';
    if ( strpos($html, '{demo')                    !== false ) return ',onlyResult';
    if ( str_ends_with($file, 'index.html')                  ) return ',onlyResult';

    return '';

  }

  function layout ( $file ) {

    $html = ( padExists( $file ) ) ? file_get_contents( $file ) : ''; 

    if ( strpos($html, '<!-- PAD: VERTICAL -->') !== false ) 
      return 'vertical';
    else
      return 'horizontal';

  }


  function getPage ( $page, $ignoreErrors=0 ) {

    global $padHost, $padScript;

    $url  = "$padHost$padScript?$page&padInclude&noShow=1";
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

      $one   = padCorrectPath ( $loop->getPathname() );
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


?>