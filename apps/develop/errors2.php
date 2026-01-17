<?php

  $dir = new RecursiveDirectoryIterator ( DAT . 'dumps' );
  $dir = new RecursiveIteratorIterator  ( $dir );

  $list = [];

  foreach ( $dir as $one ) {

    $path = padCorrectPath ( $one->getPathname() );

    if ( ! str_ends_with( $path, '_ERROR.html' ) ) continue;
    if ( str_contains ( $path, '/error' )        ) continue;
    if ( str_contains ( $path, '/support/' )     ) continue;

    $list [] = [ 'url' => substr ( str_replace ( DAT , '', $path ), 0, -11),
                 'txt' => padFileGet ($path) ];

  }

?>