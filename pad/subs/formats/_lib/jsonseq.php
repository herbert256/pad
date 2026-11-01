<?php

  // JSON text sequences (RFC 7464), the reader and the writer of types/jsonseq.php: every
  // record a JSON text with an RS (0x1E) in front of it and a line feed behind it - what
  // jq --seq writes, and a GeoJSON text sequence (RFC 8142) is made of.
  //
  // The reader follows the RFC's parser (2.1): the text is cut at its RS bytes, two of them
  // side by side being no empty record, and every piece is a JSON text - pretty, over many
  // lines, as well as on one. A value that is no object is a row of its own, as in NDJSON. A
  // piece that is no JSON, text before the first RS, and a number, true, false or null with
  // no white space before the next RS - a record cut short, which the RFC (2.4) has a parser
  // drop - are the format's error under the strict check, naming the line; on the lenient
  // walk they are passed over and the other records read, as the RFC (2.3) has it
  // (padFormatFault). The last record is read as it is: PAD takes a text with the white space
  // at its ends cut, so the line feed that would show it whole is gone there.
  //
  // The writer writes a row a record, RS, the row as JSON writes it - the slashes and the
  // characters past ASCII as they are, a float past the range null - and a line feed. JSON
  // writes an RS inside a string as \u001e, so a record never holds one.

  function padJsonSeqRead ( $text ) {

    $rows   = [];
    $pieces = explode ( "\x1E", $text );
    $at     = 0;

    foreach ( $pieces as $index => $piece ) {

      $start = $at;
      $at   += strlen ( $piece ) + 1;

      if ( $index == 0 ) {
        if ( trim ( $piece ) !== '' )
          padFormatFault ( 1, 'every record opens with an RS (0x1E) - the text before the first one is none' );
        continue;
      }

      if ( $piece === '' )
        continue;

      $line  = padFormatLineAt ( $text, $start );
      $value = json_decode ( $piece, TRUE, 512, JSON_BIGINT_AS_STRING );

      if ( json_last_error () !== JSON_ERROR_NONE ) {
        padFormatFault ( $line + padFormatJsonFault ( $piece ) - 1, 'record ' . $index . ' is no JSON - ' . json_last_error_msg () );
        continue;
      }

      if ( ! is_array ( $value ) and ! is_string ( $value ) and $index < count ( $pieces ) - 1
           and ! in_array ( substr ( $piece, -1 ), [ ' ', "\t", "\n", "\r" ], TRUE ) ) {
        padFormatFault ( $line, 'record ' . $index . ' may be cut short - a number, true, false or null has white space behind it before the next RS' );
        continue;
      }

      $rows [] = $value;

    }

    return $rows;

  }

  function padJsonSeqWrite ( $rows ) {

    $out = '';

    foreach ( $rows as $row )
      $out .= "\x1E" . json_encode ( padJsonFinite ( $row ), JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE
                                                         | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE ) . "\n";

    return $out;

  }

?>
