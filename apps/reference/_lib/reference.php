<?php

  function getXref ( $dir, $xref,  ) {

    global $base, $check, $item;

    if ( ! $dir )
      return [];

    if ( $xref == '*' ) {
      $base  = DAT . "reference/$dir/";
      $check = DAT . "reference/$dir/";
    } else {
      $base  = PAD . "$dir/";
      $check = DAT . "reference/$xref/";
    }

    $items = [];

    foreach ( padItems ( $base ) as $one ) {

      extract ( $one );

      $item = str_replace ( "$dir/", '', $item );

      if ( ( $xref == 'tag'            and $dir == 'types'       and file_exists ( PQ . "start/types/$item.php" ) )
        or ( $xref == 'properties/pad' and $dir == 'tags'        and file_exists ( PQ . "start/tags/$item.php"  ) )
        or ( $xref == 'functions'      and $dir == 'eval/single' and file_exists ( PQ . "start/eval/$item.php"  ) )
        or ( $xref == 'functions'      and $dir == 'eval/parms'  and file_exists ( PQ . "start/eval/$item.php"  ) ) )
        continue;

      if ( $item ) {
        $items [$item] ['item']  = $item;
        $items [$item] ['dir']   = ( is_dir      ( "$check$item"     ) ) ? "$xref/$item"     : '';
        $items [$item] ['pages'] = ( file_exists ( "$check$item.txt" ) ) ? "$xref/$item.txt" : '';
      }

    }

    return $items;

  }

  function padItems ( $source ) {

    if ( ! file_exists ( $source ) ) return [];
    if ( ! is_dir      ( $source ) ) return [];

    $files = [];
    
    $directory = new RecursiveDirectoryIterator ( $source    );
    $iterator  = new RecursiveIteratorIterator  ( $directory );

    foreach ($iterator as $one ) {

      $path  = padCorrectPath ( $one->getPathname() );

        $file  = str_replace (APP, '', $path );
      $file  = str_replace (PAD, '', $path );

      $ext   = substr($file,    strrpos($file, '.')+1 );
      $item  = substr($file, 0, strrpos($file, '.')   );
      $dir   = substr($item, 0, strrpos($item, '/')   );
      $file  = substr($item,    strrpos($item, '/')+1 );

      if ( ! $item       ) continue;
      if ( $file == '.'  ) continue;
      if ( $file == '..' ) continue;
      if ( $ext  == 'md' ) continue;

      $files [$item] ['item'] = $item;
      $files [$item] ['path'] = $path;
      $files [$item] ['dir']  = $dir;
      $files [$item] ['file'] = $file;
      $files [$item] ['ext']  = $ext;

    }

    ksort ($files);

    return $files;

  }


?>