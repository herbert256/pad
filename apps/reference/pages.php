<?php

  if ( ! isset ( $type ) ) $type = 'PAD Tags';
  if ( ! isset ( $xref ) ) $xref = 'tag/pad';
  if ( ! isset ( $item ) ) $item = 'switch';

  foreach ( file ( DAT . "reference/$xref/$item.txt", FILE_IGNORE_NEW_LINES ) as $file ) {

    list ( $app, $page ) = explode ( ';', $file );

    $go [] = [ 'app' => $app, 'page' => $page ];

  }

  if ( count ( $go ) > 15 )
    $go = array_slice ( $go, 0, 15 );

  $title = "Reference - $type - $item";

?>