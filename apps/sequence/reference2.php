<?php

  if ( ! isset ( $type ) ) $dir = 'Sequences';
  if ( ! isset ( $dir  ) ) $dir = 'sequences';
  if ( ! isset ( $item ) ) $dir = 'happy';

  foreach ( file ( DAT . "reference/sequence/$dir/$item.txt", FILE_IGNORE_NEW_LINES ) as $file ) {

    list ( $app, $page ) = explode ( ';', $file );

    $go [] = [ 'app' => $app, 'page' => $page ];

  }

  if ( count ( $go ) > 15 )
    $go = array_slice ( $go, 0, 15 );

  $title = "sequences - Xref - $type - $item";

?>