<?php

  // Errors are values that travel (_lib/formulas.php): an operator answers the first error of
  // its two sides, the left one first; a function taking a number answers the error it is
  // given; COUNT leaves an error out, IFERROR catches it. A name that is no function or cell
  // is #NAME? - A0 and XFE1 are none - a call with too few arguments #N/A and a formula that
  // cannot be read #ERROR!, as Google Sheets has them: Excel would not take them at all, and
  // the strict check reports them in a {spreadsheet}. A range where one value is wanted is
  // the cell of the formula's own row or column, else #VALUE!.

  $cases = formulasRun ( [
    '=1/0',
    '=#N/A+1/0',
    '=1/0+#N/A',
    '="a"+1',
    '=""+1',
    '=#REF!&"x"',
    '=-#NUM!',
    '=#NAME?%',
    '=1/0=1/0',
    '=SUM(1,1/0)',
    '=SUM(B2:B7)/COUNT(A2:A7)',
    '=ABS("abc")',
    '=MAX(B2:B7,"x")',
    '=COUNT(1/0,#N/A,3)',
    '=IFERROR(SQRT(-1),"negative")',
    '=FOO(1)',
    '=xyz+1',
    '=A0+1',
    '=XFE1',
    '=SUM()',
    '=ROUND(1)',
    '=SUM(1',
    '=1+',
    '=(1+2',
    '=1+2)',
    '="abc',
    '=1 2',
    '=B2:B7',
    '=E2:E7*2',
    '=#DIV/0!'
  ] );

?>
