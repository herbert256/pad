<?php

  $extraFiles = [];

  $base = padTagParm ('dir');
  if ($base) 
    $base = padApp . $base;
  else
    $base = $padPath;

  $sources = padExplode ( $padContent, ',');

  foreach ( $sources as $source ) {
    if     ( substr ($source, -4) == '.php')  $extraFiles [$source] ['php']   = $source;
    elseif ( substr ($source, -5) == '.html') $extraFiles [$source] ['html']  = $source;
    else                                      $extraFiles [$source] ['other'] = $source;
  }

  if ( padExists ( "$base/_inits.php"  ) ) $extraFiles ['inits'] ['php']  = padSave ( "$padDir/_inits.php"  );
  if ( padExists ( "$base/_inits.html" ) ) $extraFiles ['inits'] ['html'] = padSave ( "$padDir/_inits.html" );

  $source = "$base/_lib/";

  if ( padIsDir ("$base/_lib/") ) {

    $directory = new RecursiveDirectoryIterator ( $source    );
    $iterator  = new RecursiveIteratorIterator  ( $directory );

    foreach ( $iterator as $one ) {

      $base = $one->getPathname();
   
      if ( substr($base, -1) == '.' ) 
        continue;
   
      $file = str_replace ( padApp, '', $base );
      $ext  = substr($file,    strrpos($file, '.')+1 );
      $item = substr($file, 0, strrpos($file, '.')   );

      if ( in_array ( $ext, ['php','html'] ) )
        $extraFiles [$item] [$ext] = $file;
      else
        $extraFiles [$item] ['other'] = $file;

    }

  }

  foreach ($extraFiles as $key => $value ) {
    if ( ! isset ( $extraFiles [$key] ['php'] )   ) $extraFiles [$key] ['php']   = '';
    if ( ! isset ( $extraFiles [$key] ['html'] )  ) $extraFiles [$key] ['html']  = '';
    if ( ! isset ( $extraFiles [$key] ['other'] ) ) $extraFiles [$key] ['other'] = '';
  }

?>