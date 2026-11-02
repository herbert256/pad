<?php
  // Both delimiter functions share coercion, Unicode matching and the cell text budget.
  function padFormulaDelimited ( $args, $env, $after ) {
    $text = padFormulaArgText ( $args [0], $env );
    $delimiter = padFormulaArgText ( $args [1], $env );
    $instance = isset ( $args [2] ) ? padFormulaArgInt ( $args [2], $env ) : 1;
    $mode = isset ( $args [3] ) ? padFormulaArgInt ( $args [3], $env ) : 0;
    $end = isset ( $args [4] ) ? padFormulaArgInt ( $args [4], $env ) : 0;
    if ( $error = padFormulaFirstError ( $text, $delimiter, $instance, $mode, $end ) ) return $error;
    if ( $instance === 0 or ! in_array ( $mode, [0,1], TRUE ) or ! in_array ( $end, [0,1], TRUE )
         or padFormulaTooLong ( $text ) or padFormulaTooLong ( $delimiter ) ) return padFormulaError ( '#VALUE!' );
    if ( $text === '' ) return '';
    if ( abs ( $instance ) > mb_strlen ( $text ) ) return padFormulaError ( '#VALUE!' );
    if ( $delimiter === '' ) return ( $after === ( $instance > 0 ) ) ? $text : '';
    // Lookahead retains overlaps, then scan in the requested direction without reusing bytes.
    preg_match_all ( '~(?=(' . preg_quote ( $delimiter, '~' ) . '))~u' . ( $mode ? 'i' : '' ), $text, $matches, PREG_OFFSET_CAPTURE );
    $matches = $matches [1];
    if ( $instance < 0 ) $matches = array_reverse ( $matches );
    $edge = $instance > 0 ? 0 : strlen ( $text ); $left = abs ( $instance );
    foreach ( $matches as [ $matched, $position ] ) {
      $finish = $position + strlen ( $matched );
      if ( $instance > 0 ? $position < $edge : $finish > $edge ) continue;
      if ( --$left === 0 ) return $after ? substr ( $text, $finish ) : substr ( $text, 0, $position );
      $edge = $instance > 0 ? $finish : $position;
    }
    if ( $end and $left === 1 ) return ( $after === ( $instance < 0 ) ) ? $text : '';
    return isset ( $args [5] ) ? padFormulaScalar ( $args [5], $env ) : padFormulaError ( '#N/A' );
  }
?>
