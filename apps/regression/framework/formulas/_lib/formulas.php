<?php

  // The cases of the formulas group compute formulas as a cell beside the sample sheet of
  // the sheets application (apps/tables/_data/sample.csv) - in G1, as its functions page
  // does:
  //
  //        A         B     C      D            E
  //   1    Item      Qty   Price  Date         Flow
  //   2    Apples    10    1.20   2024-01-15   -70000
  //   3    Pears     5     0.80   2024-02-29   12000
  //   4    Cherries  20    3.50   2024-03-31   15000
  //   5    Apples    15    1.10   2024-04-01   18000
  //   6    Plums     0     2.25   2024-12-31   21000
  //   7    Figs      8     4.00   2025-06-30   26000
  //
  // and write each as formula = value: a number to the 15 digits a spreadsheet keeps - a
  // date as its serial - a text as it is, TRUE and FALSE, an error as its code - one after
  // the other with | between them, on one line, as the framework suite reads an answer. The
  // answers beside the cases were written by hand: what Excel answers.

  function formulasRun ( $formulas ) {

    padFormulaLoad ();

    $rows = padSheetRead ( (string) file_get_contents ( APPS . 'tables/_data/sample.csv' ) );
    $out  = [];

    foreach ( $formulas as $formula ) {

      $sheet = padSheetFromRows ( $rows );

      padSheetPut ( $sheet, 7, 1, padSheetTyped ( $formula ) );
      padSheetCompute ( $sheet );

      $value = $sheet ['cells'] [1] [7] ['result'];

      $out [] = $formula . ' = ' . ( ( is_float ( $value ) or is_int ( $value ) ) ? padFormulaNumberText ( (float) $value ) : padSheetShow ( $value, NULL ) );

    }

    return implode ( ' | ', $out );

  }

?>
