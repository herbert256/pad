<?php

  if ( ! isset ( $xref ) ) $xref = '';
  if ( ! isset ( $go   ) ) $go   = '';

  $title = 'cross reference';

  if ( $xref ) $title .= ' - ' . substr ( str_replace ('/', '-', $xref), 1 );
  if ( $go   ) $title .= ' - ' . substr ( $go, 1);

  if ( $go )
    return TRUE;

  $dirs  = [];
  $pages = [];
  $list  = [];

  if ( file_exists  ( DAT . 'xref' . $xref ) )
    $list = scandir ( DAT . 'xref' . $xref ); 

  foreach ( $list as $file ) {

    if ( $file == '.'  ) continue;
    if ( $file == '..' ) continue;

    if ( str_contains ( $file, '-hit' ) ) {

      $file = substr ($file, 0, -4);
      $file = str_replace ('@', '/', $file);

      $pages [$file] ['page'] = $file;

      continue;

    }
    
    $dirs [$file] ['dir']  = $file; 

  }

?>