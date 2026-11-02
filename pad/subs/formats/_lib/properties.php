<?php
  function padPropertiesDecode ( $text, $line ) {
    $result = '';
    for ( $i = 0; $i < strlen ( $text ); $i++ ) {
      $c = $text [$i];
      if ( $c !== '\\' ) { $result .= $c; continue; }
      if ( ++$i === strlen ( $text ) ) break; // Java's final continuation slash contributes nothing.
      $c = $text [$i];
      if ( $c === 'u' ) {
        $hex = substr ( $text, $i + 1, 4 );
        if ( strlen ( $hex ) !== 4 or ! ctype_xdigit ( $hex ) ) padFormatFail ( $line, 'malformed Unicode escape' );
        $code = hexdec ( $hex ); $i += 4;
        if ( $code >= 0xD800 and $code <= 0xDBFF ) {
          if ( substr ( $text, $i + 1, 2 ) !== '\u' ) padFormatFail ( $line, 'unpaired Unicode high surrogate' );
          $low = substr ( $text, $i + 3, 4 );
          if ( strlen ( $low ) !== 4 or ! ctype_xdigit ( $low ) or hexdec ( $low ) < 0xDC00 or hexdec ( $low ) > 0xDFFF ) padFormatFail ( $line, 'invalid Unicode low surrogate' );
          $code = 0x10000 + ( ( $code - 0xD800 ) << 10 ) + hexdec ( $low ) - 0xDC00; $i += 6;
        } elseif ( $code >= 0xDC00 and $code <= 0xDFFF ) padFormatFail ( $line, 'unpaired Unicode low surrogate' );
        $result .= mb_chr ( $code, 'UTF-8' );
      } else $result .= [ 'n'=>"\n", 'r'=>"\r", 't'=>"\t", 'f'=>"\f" ] [$c] ?? $c;
    }
    return $result;
  }

  function padPropertiesRead ( $text, $options ) {
    $encoding = $options ['encoding'] ?? 'UTF-8';
    if ( ! in_array ( $encoding, ['UTF-8','ISO-8859-1'], TRUE ) ) padFormatFail ( 0, 'encoding must be UTF-8 or ISO-8859-1' );
    if ( $encoding === 'ISO-8859-1' ) $text = mb_convert_encoding ( $text, 'UTF-8', $encoding );
    elseif ( ! mb_check_encoding ( $text, 'UTF-8' ) ) padFormatFail ( 0, 'malformed UTF-8 input' );
    $duplicates = $options ['duplicates'] ?? 'last';
    if ( ! in_array ( $duplicates, ['last','error'], TRUE ) ) padFormatFail ( 0, 'duplicates must be last or error' );
    $lines = preg_split ( '/\r\n|\n|\r/', $text ); $data = [];
    for ( $line = 0; $line < count ( $lines ); $line++ ) {
      $start = $line + 1; $logical = ltrim ( $lines [$line], " \t\f" );
      if ( $logical === '' or str_contains ( '#!', $logical [0] ) ) continue;
      while ( TRUE ) {
        $slashes = strlen ( $logical ) - strlen ( rtrim ( $logical, '\\' ) );
        if ( $slashes % 2 === 0 ) break;
        $logical = substr ( $logical, 0, -1 );
        if ( ++$line >= count ( $lines ) ) break;
        $logical .= ltrim ( $lines [$line], " \t\f" );
      }
      $length = strlen ( $logical ); $at = 0;
      for ( ; $at < $length; $at++ ) {
        if ( $logical [$at] === '\\' ) { $at++; continue; }
        if ( str_contains ( "=: \t\f", $logical [$at] ) ) break;
      }
      $rawKey = substr ( $logical, 0, $at );
      while ( $at < $length and str_contains ( " \t\f", $logical [$at] ) ) $at++;
      if ( $at < $length and str_contains ( '=:', $logical [$at] ) ) $at++;
      while ( $at < $length and str_contains ( " \t\f", $logical [$at] ) ) $at++;
      $key = padPropertiesDecode ( $rawKey, $start ); $value = padPropertiesDecode ( substr ( $logical, $at ), $start );
      if ( $duplicates === 'error' and array_key_exists ( $key, $data ) ) padFormatFail ( $start, 'duplicate property key' );
      $data [$key] = $value;
    }
    return $data;
  }

  function padPropertiesEscape ( $text, $key, $ascii ) {
    if ( ! mb_check_encoding ( $text, 'UTF-8' ) ) padFormatFail ( 0, 'property text must be UTF-8' );
    $result = '';
    foreach ( mb_str_split ( $text, 1, 'UTF-8' ) as $i => $c ) {
      $code = mb_ord ( $c, 'UTF-8' );
      if ( $code < 32 or $code === 127 or ( $ascii and $code > 127 ) ) {
        if ( isset ( [ "\n"=>'n', "\r"=>'r', "\t"=>'t', "\f"=>'f' ] [$c] ) ) $result .= '\\' . [ "\n"=>'n', "\r"=>'r', "\t"=>'t', "\f"=>'f' ] [$c];
        elseif ( $code <= 0xFFFF ) $result .= sprintf ( '\u%04X', $code );
        else { $code -= 0x10000; $result .= sprintf ( '\u%04X\u%04X', 0xD800 + ( $code >> 10 ), 0xDC00 + ( $code & 1023 ) ); }
      } elseif ( $c === '\\' or str_contains ( '=:# !', $c ) and ( $key or $c !== ' ' or $i === 0 ) ) $result .= '\\' . $c;
      else $result .= $c;
    }
    return $result;
  }

  function padPropertiesWrite ( $data, $options ) {
    if ( ! is_array ( $data ) ) padFormatFail ( 0, 'properties data must be a key/value map' );
    $encoding = $options ['encoding'] ?? 'UTF-8';
    if ( ! in_array ( $encoding, ['UTF-8','ISO-8859-1'], TRUE ) ) padFormatFail ( 0, 'encoding must be UTF-8 or ISO-8859-1' );
    $ascii = $options ['ascii'] ?? ( $encoding === 'ISO-8859-1' );
    if ( ! is_bool ( $ascii ) or ( $encoding === 'ISO-8859-1' and ! $ascii ) ) padFormatFail ( 0, 'ISO-8859-1 output requires ascii=true' );
    ksort ( $data, SORT_STRING ); $lines = [];
    foreach ( $data as $key => $value ) {
      if ( ! is_scalar ( $value ) and $value !== NULL ) padFormatFail ( 0, 'property values must be scalar' );
      $lines [] = padPropertiesEscape ( (string) $key, TRUE, $ascii ) . '=' . padPropertiesEscape ( (string) $value, FALSE, $ascii );
    }
    return $lines ? implode ( "\n", $lines ) . "\n" : '';
  }
?>
