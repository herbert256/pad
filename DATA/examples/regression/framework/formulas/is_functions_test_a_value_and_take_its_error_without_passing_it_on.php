<?php

  // The IS functions (types/is*.php) beside cells of their own: A1:A5 the table of Microsoft's
  // page on them - Gold, Region1, #REF!, 330.92, #N/A - with its answers; A6 empty, A7 the
  // empty text a formula answers, A8 the empty text typed ('), A9 a formula on the empty A6,
  // A10 a formula that cannot be read, A11 and A12 a circle, A13 TRUE, A14 a date. An IS
  // function takes the error it tests and never passes it on; ISBLANK is TRUE only for a
  // reference to an empty cell, never for the text "" or the 0 of a formula; ISEVEN and ISODD
  // cut a fraction toward zero, take an empty cell as 0, and answer #VALUE! for a text that is
  // no number and for a logical value. Then on the sample sheet (_lib/formulas.php).

  $isCells = [
    'A1' => 'Gold', 'A2' => 'Region1', 'A3' => '#REF!', 'A4' => 330.92, 'A5' => '#N/A',
    'A7' => '=""', 'A8' => "'", 'A9' => '=A6', 'A10' => '=SUM(1,', 'A11' => '=A12', 'A12' => '=A11',
    'A13' => TRUE, 'A14' => '2011-12-23'
  ];

  $isFormulas = [
    '=ISLOGICAL(TRUE)', '=ISLOGICAL("TRUE")', '=ISNUMBER(4)',
    '=ISBLANK(A1)', '=ISERROR(A3)', '=ISNA(A3)', '=ISNA(A5)', '=ISERR(A5)', '=ISNUMBER(A4)', '=ISTEXT(A2)',
    '=ISBLANK(A6)', '=ISBLANK(A7)', '=ISBLANK(A8)', '=ISBLANK(A9)', '=ISBLANK(A99)', '=ISBLANK("")', '=ISBLANK(0)',
    '=ISBLANK(IF(TRUE,))', '=ISBLANK(INDEX(A1:A9,6))', '=ISBLANK(A3)', '=ISBLANK(A1:A9)',
    '=ISTEXT(A7)', '=ISTEXT(A8)', '=ISTEXT(A6)', '=ISTEXT(A3)', '=ISTEXT(1)',
    '=ISNONTEXT(A6)', '=ISNONTEXT(A5)', '=ISNONTEXT(A1)', '=ISNONTEXT(A7)', '=ISNONTEXT(A13)',
    '=ISNUMBER(A9)', '=ISNUMBER(A14)', '=ISNUMBER("19")', '=ISNUMBER(A6)', '=ISNUMBER(1/0)',
    '=ISLOGICAL(A13)', '=ISLOGICAL(1=1)', '=ISLOGICAL(1)', '=ISLOGICAL(A6)',
    '=ISERROR(1/0)', '=ISERROR(#N/A)', '=ISERROR(SQRT(-1))', '=ISERROR(A10)', '=ISERROR(A11)', '=ISERROR(A4)', '=ISERROR(A6)', '=ISERROR(FOO(1))',
    '=ISERR(1/0)', '=ISERR(#N/A)', '=ISERR(A3)', '=ISERR(A10)', '=ISERR("x")',
    '=ISNA(#N/A)', '=ISNA(1/0)', '=ISNA(A1)', '=ISNA(SUM())',
    '=IF(ISERROR(1/0),"bad","ok")', '=NOT(ISNA(A5))',
    '=ISEVEN(-1)', '=ISEVEN(2.5)', '=ISEVEN(5)', '=ISEVEN(0)', '=ISEVEN(A14)', '=ISEVEN(DATE(2011,12,23))',
    '=ISODD(-1)', '=ISODD(2.5)', '=ISODD(5)',
    '=ISEVEN(-2.9)', '=ISODD(-2.9)', '=ISEVEN(A6)', '=ISODD(A6)', '=ISEVEN("4")', '=ISEVEN("x")', '=ISODD("")',
    '=ISEVEN(TRUE)', '=ISODD(A13)', '=ISEVEN(1/0)', '=ISODD(A5)'
  ];

  $isOut = [];

  foreach ( $isFormulas as $isFormula )
    $isOut [] = "$isFormula: " . var_export ( padFormula ( $isFormula, $isCells ), TRUE );

  $cases = implode ( ' | ', $isOut ) . ' | ' . formulasRun ( [
    '=ISBLANK(A8)',
    '=ISBLANK(A1)',
    '=ISBLANK(A1:A3)',
    '=ISNUMBER(D2)',
    '=ISTEXT(A2)',
    '=ISNA(VLOOKUP("Kiwis",A2:C7,2,FALSE))',
    '=ISNA(VLOOKUP("Figs",A2:C7,2,FALSE))',
    '=ISEVEN(B2)',
    '=ISODD(B3)',
    '=isError(C2/B6)'
  ] );

?>
