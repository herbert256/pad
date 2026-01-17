<?php

  $title = "Regression test";

  if ( isset ( $go ) ) {
    getRegression ();
    padRedirect ();
  }

  $directory = new RecursiveDirectoryIterator ( DAT . 'regression/' );
  $iterator  = new RecursiveIteratorIterator  ( $directory  );

  foreach ($iterator as $one ) {

    $path = $one->getPathname() ;
    $ext  = $one->getExtension() ;

    if ( $ext <> 'html' and $ext <> 'txt' ) continue;

    $base = str_replace ( DAT . 'regression/', '', $path ); 
    list ( $app, $file ) = explode ( '/', $base, 2 );
    $item = substr ( $file, 0, strrpos ( $file, '.') );

    $list [$app] ['app']                    = $app;
    $list [$app] ['items'] [$item] ['item'] = $item;

    if ( $ext == 'txt')
      $list [$app] ['items'] [$item] ['status'] = padFileGet ( $path );

  }

  ksort ( $list );

  foreach ( $list as $key => $value )
    ksort ( $list [$key] ['items'] );

?>