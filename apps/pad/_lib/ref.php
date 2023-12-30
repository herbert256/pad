<?php


  function getReferences () {

    $references = padData ('references.json');

    return $references;

  }


  function getXref ( $type, $xref, $dir ) {

    if ( $type == 'Function types' ) {
      $one   = getXref ('n/a', $xref, 'eval/single' );
      $two   = getXref ('n/a', $xref, 'eval/parms' ) ;
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

      if ( ! $item                        ) continue;
      if ( $file == '.'                   ) continue;
      if ( $file == '..'                  ) continue;

      $items [$item] ['item'] = $item;

      if ( padIsDir ( padApp . "_xref/manual/$xref/$item" ) or file_exists ( padApp . "_xref/manual/$xref/$item.txt" ) )
        $items [$item] ['link'] = "/$xref/manual/$item";
      else
        $items [$item] ['link'] = '';
 
    }
   
    ksort ( $items );
    return $items;

  }


  function getReference ($type, $ref, $dir, $kind, $onlyLinks=0) {

    if ( !$type )
      return [];
    
    if ( $kind == 'ref' )
      $dir = $ref;

    if ( $type == 'Configuration' ) 
      return getCfg ();

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

      if ( $kind == 'pad' and $type <>  'sequence' )
        if ( $ext <> 'pad' and $ext <> 'php' ) 
          continue;

      if ( $kind == 'ref' and $one->isDir() )
        $item = $one->getBasename();
      elseif ( $type == 'sequence' )
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
      elseif ( file_exists ("$check.php") or file_exists ("$check.pad") or padIsDir ($check) )
        $items [$item] ['link'] = TRUE;
      else
        $items [$item] ['link'] = FALSE;
 
    }
   
    ksort ( $items );
    return $items;

  }




 function getCfg () {

    $cfg = [];

    $settings = padFileGetContents (pad . 'config/config.php');

    foreach ($GLOBALS as $key => $value)

      if (strpos($settings, '$'.$key.' ') or strpos($settings, '$'.$key.'=') or strpos($settings, '$'.$key."\t")) {
        $cfg [$key] ['item'] = $key;
        $cfg [$key] ['link'] = ( file_exists ( padApp . "configuration/$key.pad" ) ) ;
      }

    ksort($cfg);

    return $cfg;

  }


?>