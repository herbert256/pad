<?php

  // The same CSV primitives and spreadsheet-cell protection as CSV exports, with a pipe
  // separator. Short rows are filled, extra cells and duplicate headers are errors.
  function padPsvRead ( $text ) {
    $stream = fopen ( 'php://temp', 'r+' );
    fwrite ( $stream, $text );
    rewind ( $stream );
    $rows = [];
    $header = NULL;
    $line = 1;
    try {
      while ( TRUE ) {
        $start = ftell ( $stream );
        $cells = fgetcsv ( $stream, NULL, '|', '"', '' );
        if ( $cells === FALSE ) break;
        $current = $line;
        $line += substr_count ( substr ( $text, $start, ftell ( $stream ) - $start ), "\n" );
        if ( $cells === [ NULL ] or ( count ( $cells ) === 1 and trim ( (string) $cells [0] ) === '' ) ) continue;
        if ( $header === NULL ) {
          $header = array_map ( 'trim', $cells );
          if ( in_array ( '', $header, TRUE ) or count ( array_unique ( $header ) ) !== count ( $header ) )
            padFormatFail ( $current, 'header names must be nonempty and unique' );
          continue;
        }
        if ( count ( $cells ) > count ( $header ) )
          padFormatFail ( $current, 'a row has more cells than the header has names' );
        $row = [];
        foreach ( $header as $at => $name ) $row [$name] = (string) ( $cells [$at] ?? '' );
        $rows [] = $row;
      }
    } finally {
      fclose ( $stream );
    }
    return $rows;
  }

  function padPsvWrite ( $rows ) {
    $columns = padFormatColumns ( $rows );
    if ( ! $columns ) return '';
    $header = array_map ( fn ( $name ) => trim ( (string) $name ), $columns );
    if ( in_array ( '', $header, TRUE ) or count ( array_unique ( $header ) ) !== count ( $header ) )
      padFormatFail ( 0, 'header names must be nonempty and unique' );
    $stream = fopen ( 'php://temp', 'r+' );
    try {
      fputcsv ( $stream, array_map ( 'padFormatCsvCell', $header ), '|', '"', '' );
      foreach ( $rows as $row ) {
        $cells = [];
        foreach ( $columns as $column ) $cells [] = padFormatCsvCell ( padFormatCell ( $row [$column] ?? '' ) );
        fputcsv ( $stream, $cells, '|', '"', '' );
      }
      rewind ( $stream );
      return stream_get_contents ( $stream );
    } finally {
      fclose ( $stream );
    }
  }

?>
