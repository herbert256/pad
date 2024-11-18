<?php


  function sequenceDir ( $dir )  {
  
    $out = [];

    foreach ( array_diff ( scandir ( $dir ), [ '.', '..' ] ) as $file )
      $out [] = str_replace( '.pad', '', $file );
    
    return $out;

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

    if ( ! file_exists ( $file ) )
      return [];

    $data = file_get_contents ($file);
    $data = str_replace ( "\n", ',', $data);
    $array = padExplode ( $data , ',');

    return $array;

  }


  function getExtraFiles ($dir) {

    if ( ! is_dir ( $dir) )
      return [];

    $files = [];

    $dir = new RecursiveDirectoryIterator ( $dir );
    $dir = new RecursiveIteratorIterator  ( $dir );

    foreach ( $dir as $one ) {

      $path = padCorrectPath ( $one->getPathname() );
      $file = str_replace ( APP, '', $path );

      if ( substr ( $path, -1   ) == '.'        ) continue;
      if (  str_ends_with ( $file, '.DS_Store') ) continue;

      $files [] = $file;

    }

    return $files;

  }


  function onlyResult ( $file ) {

    $pad = ( file_exists( $file ) ) ? file_get_contents( $file ) : ''; 

    if ( strpos($pad, '<!-- PAD: ONLYRESULT -->') !== false ) return ',onlyResult';
    if ( strpos($pad, '{present')                 !== false ) return ',onlyResult';
    if ( strpos($pad, '{demo')                    !== false ) return ',onlyResult';
    if ( str_ends_with($file, 'index.pad')                  ) return ',onlyResult';

    return '';

  }

  function layout ( $file ) {

    $pad = ( file_exists( $file ) ) ? file_get_contents( $file ) : ''; 

    if ( strpos($pad, '<!-- PAD: VERTICAL -->') !== false ) 
      return 'vertical';
    elseif ( strpos($pad, '<!-- PAD: ABOVE -->') !== false ) 
      return 'above';
    else
      return 'horizontal';

  }


  function getPage ( $page, $ignoreErrors=0, $include=1 ) {

    global $padHost, $padScript;

    if ($include) $include = '&padInclude';
    else          $include = '';

    $url  = "$padHost$padScript?$page$include";
    $curl = padCurl ($url);

    if ( ! $ignoreErrors and ! str_starts_with ( $curl ['result'], '2') )
      return padError ("Curl failed: $url");

    return $curl;
    
  }


  function getPageData ( $page, $ignoreErrors=0 ) {

    $curl = getPage ( $page, $ignoreErrors );

    return $curl ['data'];
    
  }


  function dirList ($dir) {

    $list = [];

    $dir = APP . $dir;

    $directory = new DirectoryIterator ( $dir       );
    $iterator  = new IteratorIterator  ( $directory );

    foreach ( $iterator as $loop ) {

      if ( $loop->isDir() )
        continue;

      $one   = padCorrectPath ( $loop->getPathname() );
      $file  = str_replace($dir,  '', $one );
      $ext   = substr($file,    strrpos($file, '.')+1 );
      $item  = substr($file, 1, strrpos($file, '.')-1 );

      if ( $item == 'index'                ) continue;
      if ( $item == 'all'                  ) continue;
      if ( $ext <> 'pad' and $ext <> 'php' ) continue;

      $list [$item] ['item'] = $item;
      
    }

    ksort($list);

    return $list;

  }


?>