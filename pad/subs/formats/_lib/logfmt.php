<?php
  function padLogfmtRead ( $text, $options ) {
    $rows = [];
    foreach ( preg_split ( '/\r\n|\n|\r/', $text ) as $line => $record ) {
      if ( trim ( $record, " \t" ) === '' ) continue;
      $row = []; $at = 0; $length = strlen ( $record );
      while ( $at < $length ) {
        while ( $at < $length and str_contains ( " \t", $record [$at] ) ) $at++;
        if ( $at === $length ) break;
        if ( ! preg_match ( '/\G([A-Za-z_][A-Za-z0-9_.-]*)=/', $record, $match, 0, $at ) ) padFormatFail ( $line + 1, 'expected key=value' );
        $key = $match [1]; $at += strlen ( $match [0] );
        if ( array_key_exists ( $key, $row ) ) padFormatFail ( $line + 1, 'duplicate key: ' . $key );
        $value = '';
        if ( $at < $length and $record [$at] === '"' ) {
          $at++; $closed = FALSE;
          while ( $at < $length ) {
            $character = $record [$at++];
            if ( ord ( $character ) < 32 or ord ( $character ) === 127 ) padFormatFail ( $line + 1, 'literal control character in quoted value' );
            if ( $character === '"' ) { $closed = TRUE; break; }
            if ( $character === '\\' ) {
              if ( $at === $length ) padFormatFail ( $line + 1, 'unfinished escape' );
              $escape = $record [$at++];
              $map = [ 'n'=>"\n", 'r'=>"\r", 't'=>"\t", '\\'=>'\\', '"'=>'"' ];
              if ( ! isset ( $map [$escape] ) ) padFormatFail ( $line + 1, 'unsupported quoted escape' );
              $character = $map [$escape];
            }
            $value .= $character;
          }
          if ( ! $closed ) padFormatFail ( $line + 1, 'unterminated quoted value' );
          if ( $at < $length and ! str_contains ( " \t", $record [$at] ) ) padFormatFail ( $line + 1, 'expected whitespace after quoted value' );
        } else {
          $start = $at;
          while ( $at < $length and ! str_contains ( " \t", $record [$at] ) ) {
            if ( str_contains ( "\"\\", $record [$at] ) or ord ( $record [$at] ) < 32 ) padFormatFail ( $line + 1, 'quote or escape requires a quoted value' );
            $at++;
          }
          $value = substr ( $record, $start, $at - $start );
        }
        $row [$key] = $value;
      }
      $rows [] = $row;
    }
    return $rows;
  }

  function padLogfmtWrite ( $data, $options ) {
    $lines = [];
    foreach ( padFormatRows ( $data ) as $i => $row ) {
      if ( ! $row ) padFormatFail ( $i + 1, 'an empty record cannot be represented' );
      ksort ( $row, SORT_STRING ); $fields = [];
      foreach ( $row as $key => $value ) {
        if ( ! preg_match ( '/^[A-Za-z_][A-Za-z0-9_.-]*$/D', (string) $key ) ) padFormatFail ( $i + 1, 'invalid logfmt key' );
        if ( ! is_scalar ( $value ) and $value !== NULL ) padFormatFail ( $i + 1, 'logfmt values must be scalar' );
        $value = is_bool ( $value ) ? ( $value ? 'true' : 'false' ) : (string) $value;
        if ( preg_match ( '/[\x00-\x08\x0b\x0c\x0e-\x1f\x7f]/', $value ) ) padFormatFail ( $i + 1, 'unsupported control character in value' );
        if ( $value === '' or preg_match ( '/[\s"\\\\]/', $value ) )
          $value = '"' . strtr ( $value, [ '\\'=>'\\\\', '"'=>'\\"', "\n"=>'\\n', "\r"=>'\\r', "\t"=>'\\t' ] ) . '"';
        $fields [] = $key . '=' . $value;
      }
      $lines [] = implode ( ' ', $fields );
    }
    return $lines ? implode ( "\n", $lines ) . "\n" : '';
  }
?>
