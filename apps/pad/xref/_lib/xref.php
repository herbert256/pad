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

    $directory = new DirectoryIterator ( "/pad/$dir" );
    $iterator  = new IteratorIterator  ( $directory );

    $items = [];
    
    foreach ($iterator as $one) {

      $path = padCorrectPath ( $one->getPathname() );
      $ext  = $one->getExtension();
      $file = $one->getFilename();

      if ( $type <> 'Sequences' )
        if ( $ext <> 'pad' and $ext <> 'php' ) 
          continue;

      if ( $type == 'Sequences' )
        $item = $one->getBasename();
      else
        $item = substr($file, 0, strrpos($file, '.')   );

      if ( ! $item       ) continue;
      if ( $file == '.'  ) continue;
      if ( $file == '..' ) continue;

      $items [$item] ['item']  = $item;
      $items [$item] ['dir']   = '';
      $items [$item] ['pages'] = '';

      if     ( is_dir ( "/app/_xref/$xref/$item" ) )
        $items [$item] ['dir'] = "$xref/$item";
      elseif ( file_exists ( "/app/_xref/$xref/$item.txt" ) )
        $items [$item] ['pages'] = "$xref/$item.txt";

    }
   
    ksort ( $items );
    return $items;

  }


?>