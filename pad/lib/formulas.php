<?php

  // Spreadsheet formulas for a page's PHP - the entries of the formulas subsystem, whose
  // engine stands in pad/subs/tables/formulas/ and is included the first time one is called:
  //
  //   padFormula ( '=SUM(1,2)' )                              3
  //   padFormula ( '=B1*2', [ 'B1' => 21 ] )                  42
  //   padSheet ( [ 'A1' => 10, 'A2' => '=A1*3' ] )            [ 'A1' => 10, 'A2' => 30 ]
  //   padSheet ( [ [ 2, 3, '=A1*B1' ] ] )                     [ [ 2, 3, 6 ] ]
  //
  // The cells are named - [ 'A1' => 10 ] - or rows of values, the first row row 1; a text
  // starting with = is a formula, as in {sheet:grid}. A whole number comes back as an int,
  // an error as its code - '#DIV/0!', '#N/A', '#REF!' for a circle of references, '#ERROR!'
  // for a formula that cannot be read; an empty result is 0, as a spreadsheet shows it. RAND
  // gives the same numbers for the same $seed. A formula is never PHP and never a PAD
  // expression: it runs the functions of pad/subs/tables/formulas/types/ and nothing else.

  function padFormulaLoad () {

    foreach ( [ 'formula', 'dates', 'format', 'collect', 'functions', 'sheet' ] as $part )
      include_once PAD . "subs/tables/formulas/_lib/$part.php";

  }

  function padFormula ( $formula, $cells = [], $seed = 0 ) {

    if ( ! is_string ( $formula ) and ! is_int ( $formula ) and ! is_float ( $formula ) ) {
      padError ( 'padFormula: the formula is a text, like =SUM(1,2), not ' . get_debug_type ( $formula ) );
      return '';
    }

    if ( ! is_array ( $cells ) ) {
      padError ( "padFormula: the cells are an array - [ 'A1' => 10 ] or rows of values - not " . get_debug_type ( $cells ) );
      return '';
    }

    padFormulaLoad ();

    $text  = trim ( (string) $formula );
    $text  = ( $text !== '' and $text [0] == '=' ) ? $text : "=$text";
    $sheet = padSheetFromData ( padSheetCellsCheck ( 'padFormula', $cells ) );

    padSheetCompute ( $sheet, $seed );

    $env = padSheetEnv ( $sheet, $seed );

    try {
      $tree = padFormulaParse ( $text, TRUE );
    } catch ( Exception $exception ) {
      try {
        $tree = padFormulaParse ( $text, FALSE );
      } catch ( Exception $again ) {
        return '#ERROR!';
      }
    }

    return padSheetExport ( padFormulaScalar ( padFormulaEval ( $tree, $env ), $env ) ?? 0.0 );

  }

  function padSheet ( $cells, $seed = 0 ) {

    if ( ! is_array ( $cells ) ) {
      padError ( "padSheet: the cells are an array - [ 'A1' => 10, 'A2' => '=A1*2' ] or rows of values - not " . get_debug_type ( $cells ) );
      return [];
    }

    padFormulaLoad ();

    $cells = padSheetCellsCheck ( 'padSheet', $cells );
    $sheet = padSheetFromData ( $cells );

    padSheetCompute ( $sheet, $seed );

    $result = [];

    foreach ( $cells as $key => $value ) {

      if ( is_array ( $value ) ) {
        foreach ( $value as $column => $one )
          $result [$key] [$column] = padSheetExport ( padSheetResult ( $sheet, $column + 1, $key + 1 ) );
        continue;
      }

      [ $column, $row ] = padFormulaCellAt ( $key );
      $result [$key] = padSheetExport ( padSheetResult ( $sheet, $column, $row ) );

    }

    return $result;

  }

?>
