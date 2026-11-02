<?php

  // Every file of pad/subs/tables/formulas/types/ starts with a comment that names the function,
  // gives its syntax and examples - a line each, the formula and after two spaces or more
  // what its cell shows on the sample sheet (_lib/formulas.php) - and returns its group, the
  // arguments it takes and its code. Each example is computed here and must show what its
  // comment says; the sheets application's functions page lists the same.

  padFormulaLoad ();

  $examplesKinds = padKinds ( 'tables/formulas' );
  $examplesCount = 0;
  $examplesWrong = [];
  $examplesShape = [];
  $examplesRows  = padSheetRead ( (string) file_get_contents ( APPS . 'tables/_data/sample.csv' ) );

  foreach ( $examplesKinds as $examplesKind ) {

    $examplesFile = PAD . "subs/tables/formulas/types/$examplesKind.php";
    $examplesType = include $examplesFile;

    if ( ! preg_match ( "/\A<\?php\s*\n  \/\/ $examplesKind - [A-Z]/", (string) file_get_contents ( $examplesFile ) )
         or ! is_array ( $examplesType ) or ! isset ( $examplesType ['group'], $examplesType ['args'], $examplesType ['call'] ) )
      $examplesShape [] = $examplesKind;

    preg_match_all ( '/^[ \t]*\/\/   (=\S.*?)(?:[ \t]{2,}(\S.*))?$/m', (string) file_get_contents ( $examplesFile ), $examplesFound, PREG_SET_ORDER );

    if ( ! $examplesFound )
      $examplesShape [] = "$examplesKind (no example)";

    foreach ( $examplesFound as $examplesOne ) {

      $examplesCount++;

      $examplesSheet = padSheetFromRows ( $examplesRows );
      padSheetPut ( $examplesSheet, 7, 1, padSheetTyped ( $examplesOne [1] ) );
      padSheetCompute ( $examplesSheet );

      $examplesCell  = $examplesSheet ['cells'] [1] [7];
      $examplesShown = padSheetShow ( $examplesCell ['result'], $examplesCell ['format'] );

      if ( $examplesShown !== ( $examplesOne [2] ?? '' ) or isset ( $examplesCell ['problem'] ) )
        $examplesWrong [] = "$examplesKind: {$examplesOne [1]} shows $examplesShown";

    }

  }

  $examplesSummary = count ( $examplesKinds ) . ' functions, ' . $examplesCount . ' examples, '
                   . count ( $examplesWrong ) . ' that show something else'
                   . ( $examplesWrong ? ': ' . implode ( '; ', $examplesWrong ) : '' )
                   . ', ' . count ( $examplesShape ) . ' files of another shape'
                   . ( $examplesShape ? ': ' . implode ( ', ', $examplesShape ) : '' );

?>
