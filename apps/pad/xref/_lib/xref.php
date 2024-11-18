<?php


  function getXref ( $type, $dir, $xref ) {

    if ( ! $dir )
      return [];

    if ( $type == 'Function types' ) {
      $one   = getXref ('n/a', 'eval/single', $xref );
      $two   = getXref ('n/a', 'eval/parms',  $xref ) ;
      $items = array_merge ( $one, $two );
      ksort ($items);
      return $items;
    }

    if ( $xref == '*') 
      return getXrefDir ( $type, $dir );
    
    $directory = new DirectoryIterator ( PAD . "$dir" );
    $iterator  = new IteratorIterator  ( $directory );

    $items = [];
    
    foreach ($iterator as $one) {

      $path = padCorrectPath ( $one->getPathname() );
      $ext  = $one->getExtension();
      $file = $one->getFilename();

      if ( $ext <> 'pad' and $ext <> 'php' ) 
        continue;

      $item = substr($file, 0, strrpos($file, '.')   );

      if ( ! $item       ) continue;
      if ( $file == '.'  ) continue;
      if ( $file == '..' ) continue;

      $items [$item] ['item']  = $item;
      $items [$item] ['dir']   = '';
      $items [$item] ['pages'] = '';

      if     ( is_dir ( APP . "_xref/$xref/$item" ) )
        $items [$item] ['dir'] = "$xref/$item";
      elseif ( file_exists ( APP . "_xref/$xref/$item.txt" ) )
        $items [$item] ['pages'] = "$xref/$item.txt";

    }
   
    ksort ( $items );
    return $items;

  }

  function getXrefDir ( $type, $dir ) {
    
    $base = APP . "_xref/";

    $directory = new DirectoryIterator ( "$base$dir" );
    $iterator  = new IteratorIterator  ( $directory );

    $items = [];
    
    foreach ($iterator as $one) {

      $path = padCorrectPath ( $one->getPathname() );
      $ext  = $one->getExtension();
      $file = $one->getFilename();

      $item = substr($file, 0, strrpos($file, '.')   );

      if ( ! $item       ) continue;
      if ( $file == '.'  ) continue;
      if ( $file == '..' ) continue;

      $items [$item] ['item']  = $item;
      $items [$item] ['dir']   = '';
      $items [$item] ['pages'] = "$dir/$file";

    }
   
    ksort ( $items );
    return $items;

  }


?>