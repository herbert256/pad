<?php

  // Reads $data as CSV and returns it as a PAD data array, one row per line keyed by the
  // names in the header line. Included by padData() as data/<type>.php once padContentType
  // has settled on 'csv' - which it does last of all, so this is also the catch-all reader
  // for anything not recognised as list, json, yaml, xml, html, range, curl or file.
  //
  // The text is read with fgetcsv over a memory stream: RFC 4180 quoting - commas and
  // newlines inside quotes, "" for a quote inside a quoted field, no backslash escape - and
  // no decoding of any kind. Cells and header names are trimmed, lines with nothing on them
  // are skipped, rows are numbered from 1, and a cell beyond the header has no name to be
  // stored under and is left out.
  //
  // It used to emulate the quoting with urlencode and a !!Q!! marker: every cell went
  // through urldecode (A+B came out as A B, %41 as A), quoted cells were converted from
  // ISO-8859-1 (a quoted café became cafÃ©), "" and a literal !!Q!! both came out as ", and
  // a row wider than the header read an undefined key.

  $result = [];
  $header = NULL;
  $row    = 0;

  $stream = fopen ( 'php://memory', 'r+' );
  fwrite ( $stream, trim ( $data ) );
  rewind ( $stream );

  while ( ( $cells = fgetcsv ( $stream, NULL, ',', '"', '' ) ) !== FALSE ) {

    $cells = array_map ( 'trim', array_map ( 'strval', $cells ) );

    if ( count ( $cells ) == 1 and $cells [0] === '' )
      continue;

    if ( $header === NULL ) {
      $header = $cells;
      continue;
    }

    $row++;

    foreach ( $cells as $key => $cell )
      if ( isset ( $header [$key] ) )
        $result [$row] [ $header [$key] ] = $cell;

  }

  fclose ( $stream );

  return $result;

?>