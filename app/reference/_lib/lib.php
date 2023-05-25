<?php


  function getReferences () {

    $references = padData ('references.json');

    return $references;

  }


  function getReference ($type, $ref, $dir, $kind) {

    if ( !$type )
      return [];
    
    if ( $kind == 'ref' )
      $dir = $ref;
    
    if ( $type == 'eval types' ) {
      $one   = getReference  ('n/a', 'eval', 'eval/single', 'pad');
      $two   = getReference ('n/a', 'eval', 'eval/parms',  'pad') ;
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