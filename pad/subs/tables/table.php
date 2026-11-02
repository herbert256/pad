<?php

  // Tables share data parsing, formatting, sorting and paging; kinds shape their rows.
  include_once PAD . 'subs/tables/_lib/table.php';
  include_once PAD . 'subs/tables/_lib/reshape.php';

  if ( ! padKind ( 'tables', $padTableKind ) ) {
    if ( $padCheckSyntax )
      padError ( "there is no table type named '" . padMakeSafe ( $padTableKind, 20 ) . "' - " . implode ( ', ', padKinds ( 'tables' ) ) );
    return FALSE;
  }

  if ( $padInfoXref ?? FALSE )
    padInfoXref ( 'table', $padTableKind );

  return include PAD . "subs/tables/types/$padTableKind.php";

?>
