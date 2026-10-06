<?php

  // A list from the query string - type[]=x - is no type, as in dir.php.

  if ( ! isset ( $type ) or ! is_string ( $type ) ) $type = 'PAD Tags';
  if ( ! isset ( $xref ) ) $xref = 'tag/pad';
  if ( ! isset ( $item ) ) $item = 'switch';

  // As in dir.php: a reference that is not there is a page not found, not a PHP error.

  if ( ! referenceName ( $xref ) or ! referenceName ( $item ) or ! is_file ( DATA . "reference/$xref/$item.txt" ) )
    return padRefuse ( 404, 'There is no such reference' );

  $go = [];

  foreach ( file ( DATA . "reference/$xref/$item.txt", FILE_IGNORE_NEW_LINES ) as $file ) {

    if ( ! str_contains ( $file, ';' ) )
      continue;

    list ( $app, $page ) = explode ( ';', $file );

    $go [] = [ 'app' => $app, 'page' => $page ];

  }

  if ( count ( $go ) > 15 )
    $go = array_slice ( $go, 0, 15 );

  $title = "Reference - $type - $item";

?>
