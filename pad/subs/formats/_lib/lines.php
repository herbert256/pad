<?php

  // Content lines, what iCalendar (RFC 5545 3.1) and vCard (RFC 6350 3.2) are both made of:
  // NAME;PARAM=value;PARAM="quoted":value, a line longer than 75 octets folded onto the
  // next ones, each starting with a space or a tab. Used by _lib/ics.php and _lib/vcf.php.
  //
  //   padLinesRead    the text as its unfolded lines: [ line, name, params, value ] each - the
  //                   line number where it starts, the name in capitals without a vCard group
  //                   (item1.EMAIL is EMAIL), the parameters by name in capitals
  //   padLinesText    a TEXT value unescaped: \\ \, \; and \n
  //   padLinesSplit   a structured value cut at its separators that are no escaped ones
  //   padLinesEscape  text escaped for a TEXT value
  //   padLinesFold    one line written: folded at 75 octets - never inside a character -
  //                   and ended with CRLF

  function padLinesRead ( $text ) {

    $logical = [];

    foreach ( preg_split ( '/\r\n|\n|\r/', $text ) as $at => $line ) {

      if ( $line === '' )
        continue;

      if ( $line [0] == ' ' or $line [0] == "\t" ) {
        if ( ! $logical )
          padFormatFail ( $at + 1, 'the text opens with a folded line - a line that goes on another starts with a space' );
        $logical [ count ( $logical ) - 1 ] [1] .= substr ( $line, 1 );
        continue;
      }

      $logical [] = [ $at + 1, $line ];

    }

    $lines = [];

    foreach ( $logical as [ $at, $line ] ) {

      if ( ! preg_match ( '/^(?:[A-Za-z0-9-]+\.)?([A-Za-z0-9-]+)/', $line, $m ) )
        padFormatFail ( $at, "a line is NAME:value - '" . padMakeSafe ( $line, 30 ) . "' has no name" );

      $name   = strtoupper ( $m [1] );
      $pos    = strlen ( $m [0] );
      $params = [];

      while ( ( $line [$pos] ?? '' ) == ';' ) {

        if ( ! preg_match ( '/\G;([A-Za-z0-9-]+)(?:=((?:"[^"]*"|[^";:,]*)(?:,(?:"[^"]*"|[^";:,]*))*))?/', $line, $p, 0, $pos ) )
          padFormatFail ( $at, "the parameters of $name are ;NAME=value" );

        $values = [];

        preg_match_all ( '/(?:^|,)("[^"]*"|[^",]*)/', $p [2] ?? '', $parts );

        foreach ( $parts [1] as $part )
          $values [] = strtr ( trim ( $part, '"' ), [ '^n' => "\n", '^N' => "\n", '^^' => '^', "^'" => '"' ] );

        $params [ strtoupper ( $p [1] ) ] = $values;

        $pos += strlen ( $p [0] );

      }

      if ( ( $line [$pos] ?? '' ) != ':' )
        padFormatFail ( $at, "a line is NAME:value - the : after $name is missing" );

      $lines [] = [ $at, $name, $params, substr ( $line, $pos + 1 ) ];

    }

    return $lines;

  }

  function padLinesText ( $value ) {

    return preg_replace_callback ( '/\\\\(.)/s', fn ( $m ) => ( $m [1] == 'n' or $m [1] == 'N' ) ? "\n" : $m [1], $value );

  }

  // Walked a character at a time, as a \\ before a separator escapes the backslash and not
  // the separator. The parts keep their escapes, for padLinesText.

  function padLinesSplit ( $value, $separator ) {

    $parts = [];
    $part  = '';
    $len   = strlen ( $value );

    for ( $at = 0; $at < $len; $at++ ) {
      $c = $value [$at];
      if ( $c == '\\' and $at + 1 < $len ) {
        $part .= $c . $value [ ++$at ];
      } elseif ( $c == $separator ) {
        $parts [] = $part;
        $part     = '';
      } else
        $part .= $c;
    }

    $parts [] = $part;

    return $parts;

  }

  function padLinesEscape ( $text ) {

    return strtr ( str_replace ( [ "\r\n", "\r" ], "\n", (string) $text ), [ '\\' => '\\\\', ';' => '\\;', ',' => '\\,', "\n" => '\\n' ] );

  }

  function padLinesFold ( $line ) {

    $out   = '';
    $limit = 75;

    while ( strlen ( $line ) > $limit ) {
      $piece = mb_strcut ( $line, 0, $limit, 'UTF-8' );
      $out  .= $piece . "\r\n ";
      $line  = substr ( $line, strlen ( $piece ) );
      $limit = 74;
    }

    return $out . $line . "\r\n";

  }

  // A parameter value, quoted when it holds a character that would end it.

  function padLinesParam ( $value ) {

    $value = strtr ( (string) $value, [ '^' => '^^', "\n" => '^n', '"' => "^'" ] );

    return preg_match ( '/[;:,]/', $value ) ? "\"$value\"" : $value;

  }

?>
