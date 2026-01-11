<?php

  if ( ! isset ( $type ) ) $dir = 'PAD Tags';
  if ( ! isset ( $xref ) ) $dir = 'tag/pad';
  if ( ! isset ( $item ) ) $dir = 'switch';

  foreach ( file ( DAT . "reference/$xref/$item.txt", FILE_IGNORE_NEW_LINES ) as $file ) {

    list ( $app, $page ) = explode ( ';', $file );

    $go [] = [ 'app' => $app, 'page' => $page ];

  }

  if ( count ( $go ) > 15 )
    $go = array_slice ( $go, 0, 15 );

  $title = "Reference - $type - $item";

?>