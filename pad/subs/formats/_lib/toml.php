<?php

  // TOML 1.0 (https://toml.io/en/v1.0.0), the reader and the writer of types/toml.php.
  //
  // The reader is a scanner over the whole text, the position in $s ['p'], so a value may
  // run over lines - a multi-line string, an array - and an error names the line it is on.
  // It reads every part of the language: comments; bare, quoted and dotted keys; basic,
  // literal and multi-line strings with every escape; integers in decimal, hexadecimal,
  // octal and binary with underscores; floats with exponents, inf and nan; booleans;
  // offset and local date-times, local dates and local times; arrays over lines with
  // comments and a trailing comma; inline tables; tables and arrays of tables. And it holds
  // a document to the rules about defining: a key or a table defined twice, a table defined
  // by a [header] after dotted keys made it (or the other way round), a key added to an
  // inline table, an array of tables appended to a static array - each is the text's error.
  //
  // A date or a time is the text it is - TOML's dates have no PHP value of their own that a
  // template prints - with the T between date and time and the Z written in capitals: a space
  // there or a lower-case t or z read alike. The writer writes a text that is such a date back
  // as one, unquoted, so a document read and written keeps its dates.
  //
  // What each table was made by is kept beside the values, by path (padTomlKey):
  //
  //   T  a [header] defined it            I  a [header] made it on the way to another
  //   D  dotted keys made it              L  an inline table, closed for good
  //   A  an array of tables               V  a value - a static array among them
  //   E  dotted keys inside an inline table made it

  function padTomlRead ( $text ) {

    if ( ! mb_check_encoding ( $text, 'UTF-8' ) )
      foreach ( explode ( "\n", $text ) as $at => $line )
        if ( ! mb_check_encoding ( $line, 'UTF-8' ) )
          padFormatFail ( $at + 1, 'the text is not UTF-8' );

    $s = [ 't' => $text, 'p' => 0, 'n' => strlen ( $text ), 'd' => 0 ];

    $root    = [];
    $kinds   = [];
    $section = [];

    while ( TRUE ) {

      padTomlBlank ( $s );

      if ( $s ['p'] >= $s ['n'] )
        break;

      $c = $s ['t'] [ $s ['p'] ];

      if ( $c == '#' )
        padTomlComment ( $s );
      elseif ( $c == "\n" or ( $c == "\r" and ( $s ['t'] [ $s ['p'] + 1 ] ?? '' ) == "\n" ) ) {
        $s ['p'] += ( $c == "\r" ) ? 2 : 1;
        continue;
      } elseif ( $c == '[' )
        $section = padTomlHeader ( $s, $root, $kinds );
      else
        padTomlPair ( $s, $root, $kinds, $section, $section, 'D' );

      padTomlLineEnd ( $s );

    }

    return $root;

  }

  function padTomlFail ( $s, $why ) {

    padFormatFail ( padFormatLineAt ( $s ['t'], $s ['p'] ), $why );

  }

  function padTomlBlank ( &$s ) {

    $s ['p'] += strspn ( $s ['t'], " \t", $s ['p'] );

  }

  // A comment runs to the end of its line and holds no control character but a tab.

  function padTomlComment ( &$s ) {

    $end  = strcspn ( $s ['t'], "\n", $s ['p'] );
    $text = substr ( $s ['t'], $s ['p'], $end );

    if ( preg_match ( '/[\x00-\x08\x0A-\x1F\x7F]/', rtrim ( $text, "\r" ) ) )
      padTomlFail ( $s, 'a comment holds a control character' );

    $s ['p'] += $end;

  }

  // After a key = value or a [header]: blanks, a comment and the end of the line - or of the
  // text. Anything else is a second thing on one line.

  function padTomlLineEnd ( &$s ) {

    padTomlBlank ( $s );

    if ( ( $s ['t'] [ $s ['p'] ] ?? '' ) == '#' )
      padTomlComment ( $s );

    if ( $s ['p'] >= $s ['n'] )
      return;

    if ( $s ['t'] [ $s ['p'] ] == "\n" )
      $s ['p']++;
    elseif ( substr ( $s ['t'], $s ['p'], 2 ) == "\r\n" )
      $s ['p'] += 2;
    else
      padTomlFail ( $s, "the line goes on after its value - a new key, table or value starts on a line of its own" );

  }

  // Blanks, newlines and comments - between the values of an array.

  function padTomlSkip ( &$s ) {

    while ( $s ['p'] < $s ['n'] ) {
      $c = $s ['t'] [ $s ['p'] ];
      if ( $c == ' ' or $c == "\t" or $c == "\n" )
        $s ['p']++;
      elseif ( $c == "\r" and ( $s ['t'] [ $s ['p'] + 1 ] ?? '' ) == "\n" )
        $s ['p'] += 2;
      elseif ( $c == '#' )
        padTomlComment ( $s );
      else
        break;
    }

  }

  // The path of a table or value as one text, for the book of kinds.

  function padTomlKey ( $path ) {

    return json_encode ( $path );

  }

  // A key: bare, quoted or literal, and dotted - the list of its parts.

  function padTomlKeyRead ( &$s ) {

    $parts = [];

    while ( TRUE ) {

      padTomlBlank ( $s );

      $c = $s ['t'] [ $s ['p'] ] ?? '';

      if ( $c == '"' ) {
        if ( substr ( $s ['t'], $s ['p'], 3 ) == '"""' )
          padTomlFail ( $s, 'a key is no multi-line string' );
        $parts [] = padTomlBasic ( $s );
      } elseif ( $c == "'" ) {
        if ( substr ( $s ['t'], $s ['p'], 3 ) == "'''" )
          padTomlFail ( $s, 'a key is no multi-line string' );
        $parts [] = padTomlLiteral ( $s );
      } elseif ( preg_match ( '/\G[A-Za-z0-9_-]+/', $s ['t'], $m, 0, $s ['p'] ) ) {
        $parts [] = $m [0];
        $s ['p'] += strlen ( $m [0] );
      } else
        padTomlFail ( $s, $c === '' || $c == "\n" || $c == "\r" ? 'a key is missing' : "a key is letters, digits, _ and - or a quoted text, not '" . $c . "'" );

      padTomlBlank ( $s );

      if ( ( $s ['t'] [ $s ['p'] ] ?? '' ) != '.' )
        return $parts;

      $s ['p']++;

    }

  }

  // [table] or [[array of tables]]: the section that follows - its path from the root, an
  // element of an array of tables as its index.

  function padTomlHeader ( &$s, &$root, &$kinds ) {

    $array = ( substr ( $s ['t'], $s ['p'], 2 ) == '[[' );

    $s ['p'] += $array ? 2 : 1;

    $keys = padTomlKeyRead ( $s );

    if ( substr ( $s ['t'], $s ['p'], $array ? 2 : 1 ) != ( $array ? ']]' : ']' ) )
      padTomlFail ( $s, $array ? 'an array of tables is named between [[ and ]]' : 'a table is named between [ and ]' );

    $s ['p'] += $array ? 2 : 1;

    $path = [];
    $last = array_pop ( $keys );

    // The tables on the way: made where missing; an array of tables stands for its last
    // table; a value or an inline table is no table to go into.

    foreach ( $keys as $key ) {

      $path [] = $key;
      $kind    = $kinds [ padTomlKey ( $path ) ] ?? '';

      if ( $kind === '' ) {
        $kinds [ padTomlKey ( $path ) ] = 'I';
        padTomlSet ( $root, $path, [] );
      } elseif ( $kind == 'A' )
        $path [] = count ( padTomlGet ( $root, $path ) ) - 1;
      elseif ( $kind == 'V' or $kind == 'L' )
        padTomlFail ( $s, "'" . implode ( '.', $keys ) . "' is a value, no table to put a table in" );

    }

    $path [] = $last;
    $at      = padTomlKey ( $path );
    $kind    = $kinds [$at] ?? '';
    $name    = implode ( '.', array_filter ( $path, 'is_string' ) );

    if ( $array ) {

      if ( $kind === '' ) {
        $kinds [$at] = 'A';
        padTomlSet ( $root, $path, [] );
      } elseif ( $kind != 'A' )
        padTomlFail ( $s, "[[$name]] appends to an array of tables, and '$name' is " . ( $kind == 'V' ? 'a value' : 'a table' ) );

      $list    = padTomlGet ( $root, $path );
      $path [] = count ( $list );

      padTomlSet ( $root, $path, [] );

      $kinds [ padTomlKey ( $path ) ] = 'T';

      return $path;

    }

    if ( $kind === '' )
      padTomlSet ( $root, $path, [] );
    elseif ( $kind == 'T' )
      padTomlFail ( $s, "the table [$name] is defined twice" );
    elseif ( $kind == 'D' )
      padTomlFail ( $s, "the table [$name] was made by dotted keys already and cannot be defined again" );
    elseif ( $kind != 'I' )
      padTomlFail ( $s, "[$name] is " . ( $kind == 'A' ? 'an array of tables - [[' . $name . ']] adds one' : 'a value, no table' ) );

    $kinds [$at] = 'T';

    return $path;

  }

  // key = value, put in the table at $section. Dotted keys make the tables on their way, of
  // the kind $made - D in a section, E inside an inline table; they go into tables of that
  // kind only, so no table a [header] defined is added to by dotted keys of another section.

  function padTomlPair ( &$s, &$root, &$kinds, $section, $top, $made ) {

    $keys = padTomlKeyRead ( $s );

    if ( ( $s ['t'] [ $s ['p'] ] ?? '' ) != '=' )
      padTomlFail ( $s, "the key '" . implode ( '.', $keys ) . "' is followed by = and its value" );

    $s ['p']++;

    padTomlBlank ( $s );

    $path = $section;
    $last = array_pop ( $keys );

    foreach ( $keys as $key ) {

      $path [] = $key;
      $kind    = $kinds [ padTomlKey ( $path ) ] ?? '';

      if ( $kind === '' ) {
        $kinds [ padTomlKey ( $path ) ] = $made;
        padTomlSet ( $root, $path, [] );
      } elseif ( $kind != $made )
        padTomlFail ( $s, "the dotted key goes into '" . implode ( '.', array_filter ( $path, 'is_string' ) ) . "', which is "
                          . ( $kind == 'V' || $kind == 'L' ? 'a value' : 'a table defined elsewhere' ) );

    }

    $path [] = $last;
    $at      = padTomlKey ( $path );

    if ( isset ( $kinds [$at] ) )
      padTomlFail ( $s, "the key '" . implode ( '.', array_filter ( array_slice ( $path, count ( $top ) ), 'is_string' ) ) . "' is defined twice" );

    [ $value, $inline ] = padTomlValue ( $s );

    $kinds [$at] = $inline ? 'L' : 'V';

    padTomlSet ( $root, $path, $value );

  }

  function padTomlGet ( $root, $path ) {

    foreach ( $path as $key )
      $root = $root [$key];

    return $root;

  }

  function padTomlSet ( &$root, $path, $value ) {

    $here = &$root;

    foreach ( $path as $key )
      $here = &$here [$key];

    $here = $value;

  }

  // A value: [ the value, whether it is an inline table ].

  function padTomlValue ( &$s ) {

    $c = $s ['t'] [ $s ['p'] ] ?? '';

    if ( $c == '"' )
      return [ substr ( $s ['t'], $s ['p'], 3 ) == '"""' ? padTomlBasicLines ( $s ) : padTomlBasic ( $s ), FALSE ];

    if ( $c == "'" )
      return [ substr ( $s ['t'], $s ['p'], 3 ) == "'''" ? padTomlLiteralLines ( $s ) : padTomlLiteral ( $s ), FALSE ];

    if ( $c == '[' )
      return [ padTomlArray ( $s ), FALSE ];

    if ( $c == '{' )
      return [ padTomlInline ( $s ), TRUE ];

    if ( $c === '' or $c == "\n" or $c == "\r" or $c == '#' )
      padTomlFail ( $s, 'a value is missing after the =' );

    return [ padTomlScalar ( $s ), FALSE ];

  }

  // true, false, a number, a date or a time: the run of characters they are made of - a
  // date and a time with a space between them read as one.

  function padTomlScalar ( &$s ) {

    if ( ! preg_match ( '/\G[0-9A-Za-z_.:+\-]+/', $s ['t'], $m, 0, $s ['p'] ) )
      padTomlFail ( $s, "a value is a text in quotes, a number, true, false, a date, [ an array ] or { a table }, not '" . $s ['t'] [ $s ['p'] ] . "'" );

    $token = $m [0];

    if ( preg_match ( '/^\d{4}-\d\d-\d\d$/D', $token ) and preg_match ( '/\G \d\d:/', $s ['t'], $more, 0, $s ['p'] + strlen ( $token ) )
         and preg_match ( '/\G [0-9A-Za-z_.:+\-]+/', $s ['t'], $time, 0, $s ['p'] + strlen ( $token ) ) )
      $token .= $time [0];

    $value = padTomlToken ( $token );

    if ( $value === NULL and preg_match ( '/^([+-]?[1-9](_?\d)*|0x[0-9A-Fa-f_]+|0o[0-7_]+|0b[01_]+)$/D', $token ) )
      padTomlFail ( $s, "the integer $token is past the 64 bits TOML has" );

    if ( $value === NULL )
      padTomlFail ( $s, "'$token' is no TOML value - a text is written in quotes" );

    $s ['p'] += strlen ( $token );

    return $value;

  }

  // A bare token as its value, or NULL when it is none.

  function padTomlToken ( $token ) {

    if ( $token === 'true' )
      return TRUE;

    if ( $token === 'false' )
      return FALSE;

    if ( preg_match ( '/^[+-]?(inf|nan)$/D', $token, $m ) )
      return $m [1] == 'nan' ? NAN : ( $token [0] == '-' ? -INF : INF );

    if ( preg_match ( '/^[+-]?(0|[1-9](_?\d)*)$/D', $token ) ) {
      $digits = str_replace ( '_', '', $token );
      $int    = filter_var ( $digits, FILTER_VALIDATE_INT );
      return ( $int === FALSE ) ? NULL : $int;
    }

    if ( preg_match ( '/^0(x[0-9A-Fa-f](_?[0-9A-Fa-f])*|o[0-7](_?[0-7])*|b[01](_?[01])*)$/D', $token ) ) {
      $digits = str_replace ( '_', '', substr ( $token, 2 ) );
      $base   = [ 'x' => 16, 'o' => 8, 'b' => 2 ] [ $token [1] ];
      $digits = strtolower ( ltrim ( $digits, '0' ) ?: '0' );
      $max    = [ 16 => dechex ( PHP_INT_MAX ), 8 => decoct ( PHP_INT_MAX ), 2 => decbin ( PHP_INT_MAX ) ] [$base];
      if ( strlen ( $digits ) > strlen ( $max ) or ( strlen ( $digits ) == strlen ( $max ) and strcmp ( $digits, $max ) > 0 ) )
        return NULL;
      return intval ( $digits, $base );
    }

    if ( preg_match ( '/^[+-]?(0|[1-9](_?\d)*)(\.\d(_?\d)*)?([eE][+-]?\d(_?\d)*)?$/D', $token, $m ) and ( isset ( $m [3] ) and ( $m [3] !== '' or ( $m [5] ?? '' ) !== '' ) ) )
      return (float) str_replace ( '_', '', $token );

    if ( preg_match ( '/^(\d{4})-(\d\d)-(\d\d)(?:[Tt ](\d\d):(\d\d):(\d\d)(\.\d+)?([Zz]|[+-]\d\d:\d\d)?)?$/D', $token, $m ) ) {
      if ( ! checkdate ( (int) $m [2], (int) $m [3], (int) $m [1] ) )
        return NULL;
      if ( isset ( $m [4] ) and ( $m [4] > 23 or $m [5] > 59 or $m [6] > 60 ) )
        return NULL;
      if ( isset ( $m [8] ) and preg_match ( '/^[+-](\d\d):(\d\d)$/', $m [8], $o ) and ( $o [1] > 23 or $o [2] > 59 ) )
        return NULL;
      return isset ( $m [4] ) ? "{$m[1]}-{$m[2]}-{$m[3]}T{$m[4]}:{$m[5]}:{$m[6]}" . ( $m [7] ?? '' ) . strtoupper ( $m [8] ?? '' ) : $token;
    }

    if ( preg_match ( '/^(\d\d):(\d\d):(\d\d)(\.\d+)?$/D', $token, $m ) )
      return ( $m [1] > 23 or $m [2] > 59 or $m [3] > 60 ) ? NULL : $token;

    return NULL;

  }

  // "basic string": one line, the escapes of TOML 1.0, no control character but a tab.

  function padTomlBasic ( &$s ) {

    $s ['p']++;
    $out = '';

    while ( TRUE ) {

      $run  = strcspn ( $s ['t'], "\"\\\n\r", $s ['p'] );
      $part = substr ( $s ['t'], $s ['p'], $run );

      padTomlControls ( $s, $part );

      $out     .= $part;
      $s ['p'] += $run;

      $c = $s ['t'] [ $s ['p'] ] ?? '';

      if ( $c == '"' ) {
        $s ['p']++;
        return $out;
      }

      if ( $c == '\\' ) {
        $out .= padTomlEscape ( $s );
        continue;
      }

      padTomlFail ( $s, 'a text in " ends on its own line - """ starts one over more lines' );

    }

  }

  // """multi-line basic string""": a newline right after the opening quotes is left out, a
  // \ at the end of a line takes the white space up to the next text away, and up to two
  // quotes may stand just before the closing three.

  function padTomlBasicLines ( &$s ) {

    $s ['p'] += 3;

    if ( ( $s ['t'] [ $s ['p'] ] ?? '' ) == "\n" )
      $s ['p']++;
    elseif ( substr ( $s ['t'], $s ['p'], 2 ) == "\r\n" )
      $s ['p'] += 2;

    $out = '';

    while ( TRUE ) {

      $run  = strcspn ( $s ['t'], "\"\\", $s ['p'] );
      $part = substr ( $s ['t'], $s ['p'], $run );

      padTomlControls ( $s, str_replace ( [ "\r\n", "\n" ], '', $part ) );

      $out     .= $part;
      $s ['p'] += $run;

      $c = $s ['t'] [ $s ['p'] ] ?? '';

      if ( $c === '' )
        padTomlFail ( $s, 'a text opened with """ is not closed' );

      if ( $c == '"' ) {
        $quotes = strspn ( $s ['t'], '"', $s ['p'] );
        if ( $quotes >= 3 ) {
          if ( $quotes > 5 )
            padTomlFail ( $s, 'a text closed with """ has more than two quotes before its end' );
          $out     .= str_repeat ( '"', $quotes - 3 );
          $s ['p'] += $quotes;
          return $out;
        }
        $out     .= str_repeat ( '"', $quotes );
        $s ['p'] += $quotes;
        continue;
      }

      if ( preg_match ( '/\G\\\\[ \t]*\r?\n[ \t\r\n]*/', $s ['t'], $m, 0, $s ['p'] ) ) {
        $s ['p'] += strlen ( $m [0] );
        continue;
      }

      $out .= padTomlEscape ( $s );

    }

  }

  function padTomlEscape ( &$s ) {

    $c = $s ['t'] [ $s ['p'] + 1 ] ?? '';

    $simple = [ 'b' => "\x08", 't' => "\t", 'n' => "\n", 'f' => "\x0C", 'r' => "\r", '"' => '"', '\\' => '\\' ];

    if ( isset ( $simple [$c] ) ) {
      $s ['p'] += 2;
      return $simple [$c];
    }

    if ( ( $c == 'u' or $c == 'U' ) and preg_match ( $c == 'u' ? '/\G\\\\u([0-9A-Fa-f]{4})/' : '/\G\\\\U([0-9A-Fa-f]{8})/', $s ['t'], $m, 0, $s ['p'] ) ) {
      $code = hexdec ( $m [1] );
      if ( $code > 0x10FFFF or ( $code >= 0xD800 and $code <= 0xDFFF ) )
        padTomlFail ( $s, "\\$c{$m[1]} is no Unicode scalar value" );
      $s ['p'] += strlen ( $m [0] );
      return mb_chr ( $code, 'UTF-8' );
    }

    padTomlFail ( $s, "\\$c is no escape of TOML - \\b \\t \\n \\f \\r \\\" \\\\ \\uXXXX and \\UXXXXXXXX are" );

  }

  function padTomlControls ( $s, $part ) {

    if ( preg_match ( '/[\x00-\x08\x0A-\x1F\x7F]/', $part, $m, PREG_OFFSET_CAPTURE ) ) {
      $s ['p'] += $m [0] [1];
      padTomlFail ( $s, 'a text holds a control character - write it as an escape, \\u' . sprintf ( '%04X', ord ( $m [0] [0] ) ) );
    }

  }

  // 'literal string': one line, as written.

  function padTomlLiteral ( &$s ) {

    $s ['p']++;

    $run  = strcspn ( $s ['t'], "'\n\r", $s ['p'] );
    $part = substr ( $s ['t'], $s ['p'], $run );

    padTomlControls ( $s, $part );

    $s ['p'] += $run;

    if ( ( $s ['t'] [ $s ['p'] ] ?? '' ) != "'" )
      padTomlFail ( $s, "a text in ' ends on its own line - ''' starts one over more lines" );

    $s ['p']++;

    return $part;

  }

  // '''multi-line literal string''': as written, the newline after the opening quotes left out.

  function padTomlLiteralLines ( &$s ) {

    $s ['p'] += 3;

    if ( ( $s ['t'] [ $s ['p'] ] ?? '' ) == "\n" )
      $s ['p']++;
    elseif ( substr ( $s ['t'], $s ['p'], 2 ) == "\r\n" )
      $s ['p'] += 2;

    $end = strpos ( $s ['t'], "'''", $s ['p'] );

    if ( $end === FALSE )
      padTomlFail ( $s, "a text opened with ''' is not closed" );

    $quotes = strspn ( $s ['t'], "'", $end );

    if ( $quotes > 5 ) {
      $s ['p'] = $end;
      padTomlFail ( $s, "a text closed with ''' has more than two quotes before its end" );
    }

    $part = substr ( $s ['t'], $s ['p'], $end + $quotes - 3 - $s ['p'] );

    padTomlControls ( $s, str_replace ( [ "\r\n", "\n" ], '', $part ) );

    $s ['p'] = $end + $quotes;

    return $part;

  }

  // Arrays and inline tables nest at most 512 deep, as a JSON document does: deeper, the
  // reader recursed until PHP's stack ran out - 200000 brackets in a 400 KB text.

  function padTomlDeeper ( &$s ) {

    if ( ++$s ['d'] > 512 )
      padTomlFail ( $s, 'arrays and inline tables nest at most 512 deep' );

  }

  // [ values ] over as many lines as it likes, with comments between, a comma after the last.

  function padTomlArray ( &$s ) {

    padTomlDeeper ( $s );

    $s ['p']++;
    $list = [];

    while ( TRUE ) {

      padTomlSkip ( $s );

      if ( ( $s ['t'] [ $s ['p'] ] ?? '' ) == ']' ) {
        $s ['p']++;
        $s ['d']--;
        return $list;
      }

      $list [] = padTomlValue ( $s ) [0];

      padTomlSkip ( $s );

      $c = $s ['t'] [ $s ['p'] ] ?? '';

      if ( $c == ',' )
        $s ['p']++;
      elseif ( $c == ']' ) {
        $s ['p']++;
        $s ['d']--;
        return $list;
      } else
        padTomlFail ( $s, $c === '' ? 'an array is not closed with ]' : 'the values of an array are separated by commas' );

    }

  }

  // { key = value, ... } on one line, no comma after the last pair - TOML 1.0 - and closed:
  // nothing is added to it afterwards.

  function padTomlInline ( &$s ) {

    padTomlDeeper ( $s );

    $s ['p']++;

    $table = [];
    $kinds = [];

    padTomlBlank ( $s );

    if ( ( $s ['t'] [ $s ['p'] ] ?? '' ) == '}' ) {
      $s ['p']++;
      $s ['d']--;
      return $table;
    }

    while ( TRUE ) {

      padTomlPair ( $s, $table, $kinds, [], [], 'E' );

      padTomlBlank ( $s );

      $c = $s ['t'] [ $s ['p'] ] ?? '';

      if ( $c == '}' ) {
        $s ['p']++;
        $s ['d']--;
        return $table;
      }

      if ( $c != ',' )
        padTomlFail ( $s, 'an inline table is one line, its pairs separated by commas and closed with }' );

      $s ['p']++;

      padTomlBlank ( $s );

      if ( ( $s ['t'] [ $s ['p'] ] ?? '' ) == '}' )
        padTomlFail ( $s, 'an inline table has no comma after its last pair' );

    }

  }

  // The writer. A table's plain values come first, as key = value; then its tables, each
  // under its [header] - left out for a table that holds tables only - and its arrays of
  // tables, an [[header]] per table. A list of tables is an array of tables; any other list
  // an array on one line, a table inside it inline. TOML has no null: a NULL is left out.
  // A list handed over whole - rows - is written as the array of tables $name, rows unless
  // the option says.

  function padTomlWrite ( $data, $options ) {

    $data = padFormatPlainKeep ( $data );

    if ( ! is_array ( $data ) or ( $data and array_is_list ( $data ) ) )
      $data = [ (string) ( $options ['name'] ?? 'rows' ) => $data ];

    $out = [];

    padTomlTable ( $data, [], $out, FALSE );

    return ltrim ( implode ( "\n", $out ) ) . "\n";

  }

  function padTomlTable ( $table, $path, &$out, $header ) {

    $plain  = [];
    $tables = [];
    $arrays = [];

    foreach ( $table as $key => $value )
      if ( $value === NULL )
        continue;
      elseif ( padTomlIsTable ( $value ) )
        $tables [$key] = $value;
      elseif ( padTomlIsTables ( $value ) )
        $arrays [$key] = $value;
      else
        $plain [$key] = $value;

    if ( $header or $plain or ( ! $tables and ! $arrays and $path ) ) {
      if ( $header !== FALSE )
        $out [] = ( $out ? "\n" : '' ) . $header;
      elseif ( $path )
        $out [] = ( $out ? "\n" : '' ) . '[' . padTomlPath ( $path ) . ']';
    }

    foreach ( $plain as $key => $value )
      $out [] = padTomlName ( $key ) . ' = ' . padTomlInlineValue ( $value );

    foreach ( $tables as $key => $value )
      padTomlTable ( $value, array_merge ( $path, [ $key ] ), $out, FALSE );

    foreach ( $arrays as $key => $list )
      foreach ( $list as $one )
        padTomlTable ( $one, array_merge ( $path, [ $key ] ), $out, '[[' . padTomlPath ( array_merge ( $path, [ $key ] ) ) . ']]' );

  }

  function padTomlIsTable ( $value ) {

    return is_array ( $value ) and $value and ! array_is_list ( $value );

  }

  // A list of tables - an empty one among them, which PHP cannot tell from an empty list: the
  // [[products]] with nothing under it.

  function padTomlIsTables ( $value ) {

    if ( ! is_array ( $value ) or ! $value or ! array_is_list ( $value ) )
      return FALSE;

    $tables = count ( array_filter ( $value, 'padTomlIsTable' ) );
    $empty  = count ( array_filter ( $value, fn ( $one ) => $one === [] ) );

    return $tables and $tables + $empty == count ( $value );

  }

  function padTomlPath ( $path ) {

    return implode ( '.', array_map ( 'padTomlName', $path ) );

  }

  function padTomlName ( $key ) {

    $key = (string) $key;

    return preg_match ( '/^[A-Za-z0-9_-]+$/D', $key ) ? $key : padTomlString ( $key );

  }

  function padTomlInlineValue ( $value ) {

    if ( is_bool ( $value ) )
      return $value ? 'true' : 'false';

    if ( is_int ( $value ) )
      return (string) $value;

    if ( is_float ( $value ) ) {
      if ( is_nan ( $value ) )
        return 'nan';
      if ( is_infinite ( $value ) )
        return $value > 0 ? 'inf' : '-inf';
      return padFormatFloat ( $value );
    }

    if ( is_array ( $value ) ) {
      if ( ! $value )
        return '[]';
      if ( ! array_is_list ( $value ) ) {
        $pairs = [];
        foreach ( $value as $key => $one )
          if ( $one !== NULL )
            $pairs [] = padTomlName ( $key ) . ' = ' . padTomlInlineValue ( $one );
        return '{ ' . implode ( ', ', $pairs ) . ' }';
      }
      return '[ ' . implode ( ', ', array_map ( 'padTomlInlineValue', array_filter ( $value, fn ( $one ) => $one !== NULL ) ) ) . ' ]';
    }

    $value = (string) $value;

    if ( padTomlIsDate ( $value ) )
      return $value;

    return padTomlString ( $value );

  }

  // A text that is a TOML date or time as written - the writer leaves it bare.

  function padTomlIsDate ( $text ) {

    return preg_match ( '/^\d{4}-\d\d-\d\d(T\d\d:\d\d:\d\d(\.\d+)?(Z|[+-]\d\d:\d\d)?)?$|^\d\d:\d\d:\d\d(\.\d+)?$/D', $text )
           and padTomlToken ( $text ) === $text;

  }

  function padTomlString ( $text ) {

    $text = mb_scrub ( $text, 'UTF-8' );

    $out = strtr ( $text, [ '\\' => '\\\\', '"' => '\\"', "\n" => '\\n', "\t" => '\\t', "\r" => '\\r', "\x08" => '\\b', "\x0C" => '\\f' ] );

    $out = preg_replace_callback ( '/[\x00-\x1F\x7F]/', fn ( $m ) => sprintf ( '\\u%04X', ord ( $m [0] ) ), $out );

    return '"' . $out . '"';

  }

?>
