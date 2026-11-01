<?php

  // TSV, the reader and the writer of types/tsv.php. A row with more cells than the header
  // has names is the text's error - its cells would have no field; a shorter row has the
  // rest empty, as a CSV row has. Blank lines are skipped; the header's names are trimmed,
  // a cell is kept as it is.

  function padTsvRead ( $text ) {

    $header = NULL;
    $rows   = [];

    foreach ( explode ( "\n", $text ) as $at => $line ) {

      $line = rtrim ( $line, "\r" );

      if ( trim ( $line ) === '' )
        continue;

      $cells = array_map ( 'padTsvUnescape', explode ( "\t", $line ) );

      if ( $header === NULL ) {
        $header = array_map ( 'trim', $cells );
        continue;
      }

      if ( count ( $cells ) > count ( $header ) )
        padFormatFail ( $at + 1, 'a row of ' . count ( $cells ) . ' cells under a header of ' . count ( $header ) . ' names' );

      $row = [];

      foreach ( $header as $index => $name )
        $row [$name] = $cells [$index] ?? '';

      $rows [] = $row;

    }

    return $rows;

  }

  function padTsvWrite ( $rows ) {

    $columns = padFormatColumns ( $rows );
    $lines   = [ implode ( "\t", array_map ( 'padTsvEscape', $columns ) ) ];

    foreach ( $rows as $row ) {
      $cells = [];
      foreach ( $columns as $column )
        $cells [] = padTsvEscape ( padFormatCell ( $row [$column] ?? '' ) );
      $lines [] = implode ( "\t", $cells );
    }

    return implode ( "\n", $lines ) . "\n";

  }

  function padTsvEscape ( $text ) {

    return strtr ( (string) $text, [ '\\' => '\\\\', "\t" => '\t', "\n" => '\n', "\r" => '\r' ] );

  }

  function padTsvUnescape ( $text ) {

    return strtr ( $text, [ '\\\\' => '\\', '\t' => "\t", '\n' => "\n", '\r' => "\r" ] );

  }

?>
