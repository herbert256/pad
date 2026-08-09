<?php

  if ( ! isset ( $type ) ) $type = 'PAD Tags';
  if ( ! isset ( $xref ) ) $xref = 'tag/pad';
  if ( ! isset ( $item ) ) $item = 'switch';

  foreach ( file ( DATA . "reference/$xref/$item.txt", FILE_IGNORE_NEW_LINES ) as $file ) {

    list ( $app, $page ) = explode ( ';', $file );

    $go [] = [ 'app' => $app, 'page' => $page ];

  }

  if ( count ( $go ) > 15 )
    $go = array_slice ( $go, 0, 15 );

  // The regression tests naming the item - real usage above, asserted usage below.

  $caseList    = getReferenceCaseList ( $item, $xref );
  $casesCount  = count ( $caseList );
  $casesGroups = implode ( ', ', array_unique ( array_column ( $caseList, 'suite' ) ) );

  $title = "Reference - $type - $item";

?>