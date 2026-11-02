<?php
  function padFixedwidthColumns ( $options ) {
    $columns = $options ['columns'] ?? NULL;
    if ( ! is_array ( $columns ) or ! $columns or count ( $columns ) > 1000 ) padFormatFail ( 0, 'columns must be a nonempty map of names to widths or column options (at most 1000)' );
    $out = []; $total = 0;
    foreach ( $columns as $name => $spec ) {
      if ( ! is_string ( $name ) or $name === '' ) padFormatFail ( 0, 'column names must be nonempty strings' );
      if ( ! is_array ( $spec ) ) $spec = [ 'width' => $spec ];
      $width = $spec ['width'] ?? NULL; $align = $spec ['align'] ?? 'left'; $pad = $spec ['pad'] ?? ' ';
      if ( ! is_int ( $width ) or $width < 1 or $width > 100000 or ! in_array ( $align, ['left','right'], TRUE ) or ! is_string ( $pad ) or strlen ( $pad ) !== 1 or str_contains ( "\r\n", $pad ) )
        padFormatFail ( 0, 'columns require width 1..100000 bytes, left/right alignment and a one-byte pad other than newline' );
      $total += $width; $out [$name] = [ $width, $align, $pad ];
    }
    if ( $total > 1000000 ) padFormatFail ( 0, 'record width exceeds one million bytes' );
    return $out;
  }

  function padFixedwidthRead ( $text, $options ) {
    $columns = padFixedwidthColumns ( $options ); $rows = []; $width = array_sum ( array_column ( $columns, 0 ) );
    $lines = preg_split ( '/\r\n|\n|\r/', $text );
    if ( end ( $lines ) === '' ) array_pop ( $lines );
    foreach ( $lines as $line => $record ) {
      if ( strlen ( $record ) !== $width ) padFormatFail ( $line + 1, "record must contain exactly $width bytes" );
      $row = []; $offset = 0;
      foreach ( $columns as $name => [ $size, $align, $pad ] ) {
        $cell = substr ( $record, $offset, $size ); $offset += $size;
        // Trim only the selected padding byte, not PHP's character-mask syntax.
        if ( $align === 'left' ) { while ( str_ends_with ( $cell, $pad ) ) $cell = substr ( $cell, 0, -1 ); }
        else { while ( str_starts_with ( $cell, $pad ) ) $cell = substr ( $cell, 1 ); }
        $row [$name] = $cell;
      }
      $rows [] = $row;
    }
    return $rows;
  }

  function padFixedwidthWrite ( $data, $options ) {
    $columns = padFixedwidthColumns ( $options ); $lines = [];
    $overflow = $options ['overflow'] ?? 'error';
    if ( ! in_array ( $overflow, ['error','truncate'], TRUE ) ) padFormatFail ( 0, 'overflow must be error or truncate' );
    foreach ( padFormatRows ( $data ) as $i => $row ) {
      $line = '';
      foreach ( $columns as $name => [ $width, $align, $pad ] ) {
        $value = $row [$name] ?? '';
        if ( ! is_scalar ( $value ) and $value !== NULL ) padFormatFail ( $i + 1, 'fixed-width cells must be scalar' );
        $value = (string) $value;
        if ( strpbrk ( $value, "\r\n" ) !== FALSE ) padFormatFail ( $i + 1, 'a cell contains a record delimiter' );
        if ( strlen ( $value ) > $width ) {
          if ( $overflow !== 'truncate' ) padFormatFail ( $i + 1, 'cell exceeds its byte width' );
          $value = substr ( $value, 0, $width );
        }
        $line .= str_pad ( $value, $width, $pad, $align === 'right' ? STR_PAD_LEFT : STR_PAD_RIGHT );
      }
      $lines [] = $line;
    }
    return $lines ? implode ( "\n", $lines ) . "\n" : '';
  }
?>
