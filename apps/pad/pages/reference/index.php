<?php

  function types () {

    $types = padData ('references.json');

    foreach ( $types as $key => $type ) {

      $index = padApp . 'pages/' . $type['ref'] . '/index';

      if ( padExists ("$index.php") or padExists ("$index.html") )
        $types [$key] ['link'] = TRUE;
      else
        $types [$key] ['link'] = FALSE;

      $types [$key] ['id'] = str_replace('/', '', $type['ref']);

    }

    return $types;

  }

  function items ($type, $ref, $dir, $kind) {

    if ($kind == 'ref')
      $dir = $ref;
    
    if ( $type == 'eval types' ) {
      $items = array_merge ( items ('', 'eval', 'eval/single', 'pad'), items ('', '', 'eval/parms', 'pad') );
      ksort ($items);
      return $items;
    }

    $manual = padApp . "pages/$ref";

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

      if ( $item == 'todo' ) continue;
      if ( $item == 'code' ) continue;

      $items [$item] ['item'] = $item;

      $check = "$manual/$item";

      if ( $kind == 'ref' )
        $items [$item] ['link'] = TRUE;
      elseif ( padExists ("$check.php") or padExists ("$check.html") or padIsDir ($check) )
        $items [$item] ['link'] = TRUE;
      else
        $items [$item] ['link'] = FALSE;
 
    }

    return $items;

  }

?>