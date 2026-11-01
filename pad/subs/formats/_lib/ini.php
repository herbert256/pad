<?php

  // INI, the reader and the writer of types/ini.php. INI has no standard; this is the common
  // ground of PHP's own, Python's and git's:
  //
  //   ; a comment, # one too         a line of its own
  //   top = 1                        a key before any section is a key of the whole
  //   [database]                     a section: the keys below it are its fields
  //   host = localhost               the value trimmed; an unquoted one ends at a ; or #
  //                                  that has white space before it
  //   name = "My \"app\""            quoted: as written, \" \\ \n and \t its escapes
  //   path = 'C:\temp'               single-quoted: as written, no escapes
  //   tags[] = a                     key[] adds to a list,
  //   colors[dark] = #000            key[name] sets a field of a record
  //
  // A value is the text it is - INI has no types: on, 1 and true are texts a template
  // compares as it likes. A key given twice is the last one, as PHP reads it. A line that is
  // none of these - a key without its =, a section without its ] - is the text's error.

  function padIniRead ( $text ) {

    $data    = [];
    $section = NULL;

    foreach ( explode ( "\n", $text ) as $at => $line ) {

      $line = trim ( $line );

      if ( $line === '' or $line [0] == ';' or $line [0] == '#' )
        continue;

      if ( $line [0] == '[' ) {
        if ( ! preg_match ( '/^\[\s*([^\]]*?)\s*\]\s*([;#].*)?$/D', $line, $m ) or $m [1] === '' )
          padFormatFail ( $at + 1, 'a section is named between [ and ]' );
        $section = $m [1];
        if ( isset ( $data [$section] ) and ! is_array ( $data [$section] ) )
          padFormatFail ( $at + 1, "the section [$section] is named like the key $section above it - its keys would have no record to go in" );
        $data [$section] ??= [];
        continue;
      }

      if ( ! preg_match ( '/^([^=\[\]]+?)\s*(\[([^\]]*)\])?\s*=\s*(.*)$/D', $line, $m ) )
        padFormatFail ( $at + 1, "a line is key = value, [section] or a ; comment - '" . padMakeSafe ( $line, 30 ) . "' is none" );

      $key   = $m [1];
      $value = padIniValue ( $m [4], $at + 1 );

      if ( $section === NULL )
        $here = &$data;
      else
        $here = &$data [$section];

      if ( $m [2] === '' )
        $here [$key] = $value;
      elseif ( $m [3] === '' ) {
        if ( ! is_array ( $here [$key] ?? NULL ) )
          $here [$key] = [];
        $here [$key] [] = $value;
      } else {
        if ( ! is_array ( $here [$key] ?? NULL ) )
          $here [$key] = [];
        $here [$key] [ $m [3] ] = $value;
      }

      unset ( $here );

    }

    return $data;

  }

  function padIniValue ( $raw, $line ) {

    if ( $raw === '' )
      return '';

    if ( $raw [0] == '"' ) {
      if ( ! preg_match ( '/^"((?:[^"\\\\]|\\\\.)*)"\s*([;#].*)?$/D', $raw, $m ) )
        padFormatFail ( $line, 'a value in " is closed by a " at the end of its line' );
      return preg_replace_callback ( '/\\\\(.)/', fn ( $e ) => [ 'n' => "\n", 't' => "\t", 'r' => "\r" ] [ $e [1] ] ?? $e [1], $m [1] );
    }

    if ( $raw [0] == "'" ) {
      if ( ! preg_match ( "/^'([^']*)'\s*([;#].*)?$/D", $raw, $m ) )
        padFormatFail ( $line, "a value in ' is closed by a ' at the end of its line" );
      return $m [1];
    }

    return rtrim ( preg_replace ( '/\s[;#].*$/D', '', $raw ) );

  }

  // The writer: the plain values of the whole first, then a [section] per record - a value
  // that is a list of plain values written key[] = value, a record of them key[name] = value,
  // anything deeper as its JSON. A value is quoted when it would not read back as written:
  // white space at either end, a ; or # after a blank, a quote or a backslash, a newline -
  // or an empty text, quoted so it is seen. TRUE and FALSE are true and false, NULL is empty.

  function padIniWrite ( $data ) {

    $data = padFormatPlain ( $data );

    if ( ! is_array ( $data ) or ( $data and array_is_list ( $data ) and ! array_filter ( $data, 'is_array' ) ) )
      $data = [ 'value' => $data ];

    $top      = [];
    $sections = [];

    foreach ( $data as $key => $value )
      if ( is_array ( $value ) and $value and ( ! array_is_list ( $value ) or array_filter ( $value, 'is_array' ) ) )
        $sections [$key] = $value;
      else
        $top [$key] = $value;

    $out = padIniLines ( $top );

    foreach ( $sections as $name => $values ) {
      if ( $out )
        $out [] = '';
      $out [] = '[' . $name . ']';
      array_push ( $out, ...padIniLines ( $values ) );
    }

    return $out ? implode ( "\n", $out ) . "\n" : '';

  }

  function padIniLines ( $values ) {

    $lines = [];

    foreach ( $values as $key => $value )
      if ( is_array ( $value ) and array_is_list ( $value ) and ! array_filter ( $value, 'is_array' ) )
        foreach ( $value as $one )
          $lines [] = "{$key}[] = " . padIniText ( $one );
      elseif ( is_array ( $value ) and ! array_filter ( $value, 'is_array' ) )
        foreach ( $value as $name => $one )
          $lines [] = "{$key}[$name] = " . padIniText ( $one );
      else
        $lines [] = "$key = " . padIniText ( $value );

    return $lines;

  }

  function padIniText ( $value ) {

    if ( is_array ( $value ) )
      $value = padFormatCell ( $value );
    elseif ( is_bool ( $value ) )
      return $value ? 'true' : 'false';
    elseif ( $value === NULL )
      return '';
    elseif ( is_float ( $value ) )
      return padFormatCell ( $value );

    $value = (string) $value;

    if ( $value === '' or $value !== trim ( $value ) or preg_match ( '/\s[;#]|^[;#"\']|["\\\\\n\r\t]/', $value ) )
      return '"' . strtr ( $value, [ '\\' => '\\\\', '"' => '\\"', "\n" => '\\n', "\t" => '\\t', "\r" => '\\r' ] ) . '"';

    return $value;

  }

?>
