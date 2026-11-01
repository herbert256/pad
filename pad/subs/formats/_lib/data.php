<?php

  // The four formats the engine read before the formats subsystem - JSON, XML, YAML and CSV -
  // and the two it wrote, JSON and CSV, as page answers (build/_lib/expose.php).
  //
  // Their readers stay where they were, pad/data/<type>.php, and this file only hands a text
  // to them: data/ is the directory padData dispatches on with the sources that are no
  // format - a ( list ), a range, an address, a _data file, an HTML page - and padContentType
  // knows the four on sight in an order every stored answer depends on; and they report a
  // text they cannot read under every check, where a format of the subsystem reports under
  // the strict check only. Moved, either would read some text otherwise.
  //
  // Their writers moved here from build/_lib/expose.php, unchanged - the JSON and CSV page
  // answers are made by them byte for byte as before.

  function padFormatDataRead ( $type, $data ) {

    $name   = '';
    $result = include PAD . "data/$type.php";

    return is_array ( $result ) ? $result : [];

  }

  // JSON as a page answer writes it: pretty, the slashes and the characters past ASCII as
  // they are, a float past the range null (padJsonFinite) and a byte that is no UTF-8 as
  // U+FFFD - partial output made the whole value null, with no error, which a client cannot
  // tell from a real null. object writes an empty set as {}, the answer being one object.

  function padFormatJsonWrite ( $data, $options ) {

    $value = padJsonFinite ( $data );

    if ( $options ['object'] ?? FALSE )
      $value = (object) $value;

    $json = json_encode ( $value, JSON_PRETTY_PRINT | JSON_PARTIAL_OUTPUT_ON_ERROR
                                  | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                                  | JSON_INVALID_UTF8_SUBSTITUTE );

    return ( $json === FALSE ) ? '{}' : $json;

  }

  // The columns are every key the rows use, in the order they first appear, so a row that
  // lacks one gets an empty cell rather than shifting the line. A cell that holds an array
  // is written as its JSON. RFC 4180 quoting, with no escape character of PHP's own.

  function padFormatCsvWrite ( $rows ) {

    $columns = [];

    foreach ( $rows as $row )
      foreach ( array_keys ( $row ) as $column )
        if ( ! in_array ( $column, $columns, TRUE ) )
          $columns [] = $column;

    $csv = fopen ( 'php://temp', 'r+' );

    fputcsv ( $csv, array_map ( 'padFormatCsvCell', $columns ), ',', '"', '' );

    foreach ( $rows as $row ) {

      $line = [];

      foreach ( $columns as $column ) {
        $cell    = $row [$column] ?? '';
        $line [] = padFormatCsvCell ( is_array ( $cell ) || is_object ( $cell ) ? json_encode ( $cell ) : ( is_bool ( $cell ) ? (int) $cell : $cell ) );
      }

      fputcsv ( $csv, $line, ',', '"', '' );

    }

    rewind ( $csv );

    $body = stream_get_contents ( $csv );

    fclose ( $csv );

    return $body;

  }

  // YAML as yaml_emit writes it - the PHP yaml extension the reader needs as well: a
  // document between --- and ..., a text that would read as another kind of value quoted.

  function padFormatYamlWrite ( $data ) {

    return yaml_emit ( padFormatPlain ( $data ), YAML_UTF8_ENCODING, YAML_LN_BREAK );

  }

?>
