<?php


  function getXref ( $type, $dir ) {

    if ( $type == 'Function types' ) {
      $one   = getXref ('n/a', 'eval/single' );
      $two   = getXref ('n/a', 'eval/parms' ) ;
      $items = array_merge ( $one, $two );
      ksort ($items);
      return $items;
    }

    $directory = new DirectoryIterator ( pad . $dir );
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

      if     ( padIsDir ( padApp . "_reference/$item" ) )
        $items [$item] ['dir'] = "$item";
      elseif ( file_exists ( padApp . "_reference/$item.txt" ) )
        $items [$item] ['pages'] = "$item.txt";

    }
   
    ksort ( $items );
    return $items;

  }


?>