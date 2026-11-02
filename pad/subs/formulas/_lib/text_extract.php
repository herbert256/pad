<?php

  // TEXTBEFORE/TEXTAFTER share literal Unicode delimiter matching. Lookahead records
  // overlapping candidates, then the chosen direction selects non-overlapping matches.
  // Byte offsets refer to the original text, preserving accented characters and emoji.
  function padFormulaTextExtract ( $args, $env, $after ) {
    $text = padFormulaArgText ( $args [0], $env );
    $delimiter = padFormulaArgText ( $args [1], $env );
    $instance = isset ( $args [2] ) ? padFormulaArgInt ( $args [2], $env ) : 1;
    $mode = isset ( $args [3] ) ? padFormulaArgInt ( $args [3], $env ) : 0;
    $end = isset ( $args [4] ) ? padFormulaArgInt ( $args [4], $env ) : 0;
    if ( $error = padFormulaFirstError ( $text, $delimiter, $instance, $mode, $end ) ) return $error;
    if ( $instance === 0 or ! in_array ( $mode, [ 0, 1 ], TRUE ) or ! in_array ( $end, [ 0, 1 ], TRUE ) )
      return padFormulaError ( '#VALUE!' );
    if ( ! mb_check_encoding ( $text, 'UTF-8' ) or ! mb_check_encoding ( $delimiter, 'UTF-8' ) )
      return padFormulaError ( '#VALUE!' );
    if ( $text === '' ) return '';
    if ( abs ( $instance ) > mb_strlen ( $text, 'UTF-8' ) ) return padFormulaError ( '#VALUE!' );
    if ( $delimiter === '' ) return ( $after === ( $instance > 0 ) ) ? $text : '';

    $matches = [];
    $pattern = '/(?=(' . preg_quote ( $delimiter, '/' ) . '))/u' . ( $mode ? 'i' : '' );
    if ( strlen ( $pattern ) > 30000 ) {
      // Large literal delimiters can exceed PCRE's compiled-pattern limit. Their few
      // non-overlapping matches are found directly, without building a huge pattern.
      $remaining = $text;
      $base = 0;
      $candidates = [];
      $size = mb_strlen ( $delimiter, 'UTF-8' );
      while ( $remaining !== '' ) {
        $at = $instance < 0
          ? ( $mode ? mb_strripos ( $remaining, $delimiter, 0, 'UTF-8' ) : mb_strrpos ( $remaining, $delimiter, 0, 'UTF-8' ) )
          : ( $mode ? mb_stripos ( $remaining, $delimiter, 0, 'UTF-8' ) : mb_strpos ( $remaining, $delimiter, 0, 'UTF-8' ) );
        if ( $at === FALSE ) break;
        $prefix = mb_substr ( $remaining, 0, $at, 'UTF-8' );
        $found = mb_substr ( $remaining, $at, $size, 'UTF-8' );
        $candidates [] = [ $found, $base + strlen ( $prefix ) ];
        if ( $instance < 0 ) $remaining = $prefix;
        else {
          $base += strlen ( $prefix ) + strlen ( $found );
          $remaining = mb_substr ( $remaining, $at + $size, NULL, 'UTF-8' );
        }
      }
    } else {
      preg_match_all ( $pattern, $text, $matches, PREG_OFFSET_CAPTURE );
      $candidates = $instance < 0 ? array_reverse ( $matches [1] ) : $matches [1];
    }
    $edge = $instance < 0 ? strlen ( $text ) : 0;
    $seen = 0;
    foreach ( $candidates as [ $found, $at ] ) {
      $past = $at + strlen ( $found );
      if ( $instance > 0 ? $at < $edge : $past > $edge ) continue;
      $edge = $instance > 0 ? $past : $at;
      if ( ++$seen === abs ( $instance ) )
        return $after ? substr ( $text, $past ) : substr ( $text, 0, $at );
    }
    if ( $end and $seen + 1 === abs ( $instance ) )
      return ( $after === ( $instance < 0 ) ) ? $text : '';
    return array_key_exists ( 5, $args ) ? padFormulaScalar ( $args [5], $env ) : padFormulaError ( '#N/A' );
  }

?>
