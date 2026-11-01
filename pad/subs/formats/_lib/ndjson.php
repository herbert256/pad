<?php

  // NDJSON, the reader and the writer of types/ndjson.php. A blank line is no row; a line that
  // is no JSON is the text's error, at its line. A value that is no object - 3, "a" - is a row
  // of its own, as padData makes a list of plain values. The writer writes a row a line as
  // JSON writes it, the slashes and the characters past ASCII as they are, a float past the
  // range null.

  function padNdjsonRead ( $text ) {

    $rows = [];

    foreach ( explode ( "\n", $text ) as $at => $line ) {

      $line = trim ( $line );

      if ( $line === '' )
        continue;

      $rows [] = padFormatJson ( $line, $at );

    }

    return $rows;

  }

  function padNdjsonWrite ( $rows ) {

    $out = '';

    foreach ( $rows as $row )
      $out .= json_encode ( padJsonFinite ( $row ), JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE
                                                    | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE ) . "\n";

    return $out;

  }

?>
