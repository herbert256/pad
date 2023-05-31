<?php


  function getReferences () {

    $references = padData ('references.json');

    return $references;

  }


  function getReference ($type, $ref, $dir, $kind, $onlyLinks=0) {

    if ( !$type )
      return [];
    
    if ( $kind == 'ref' )
      $dir = $ref;
    
    if ( $type == 'Function types' ) {
      $one   = getReference ('n/a', 'eval', 'eval/single', 'pad');
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

      $path = padCorrectPath ( $one->getPathname() );
      $ext  = $one->getExtension();
      $file = $one->getFilename();

      if ( $kind == 'pad' and $type <>  'sequences' )
        if ( $ext <> 'html' and $ext <> 'php' ) 
          continue;

      if ( $kind == 'ref' and $one->isDir() )
        $item = $one->getBasename();
      elseif ( $type == 'sequences' )
        $item = $one->getBasename();
      else
        $item = substr($file, 0, strrpos($file, '.')   );

      if ( ! $item                        ) continue;
      if ( $file == '.'                   ) continue;
      if ( $file == '..'                  ) continue;
      if ( strpos ( $path, '.settings.' ) ) continue;
      if ( strpos ( $path, '/docs/' )     ) continue;
      if ( strpos ( $path, '/intro/' )    ) continue;
      if ( substr ( $item, 0, 1 ) == '_'  ) continue;

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