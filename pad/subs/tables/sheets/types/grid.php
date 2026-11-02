<?php

  // {sheet:grid 'budget'} ... {/sheet:grid} - a grid of cells whose formulas are computed on
  // the server, as an accessible table: the cells are the content, a line a row and a comma
  // between cells, or the rows of data=; a formula is spreadsheet syntax - =B2*C2,
  // =SUM(D2:D9), =VLOOKUP(A2,F2:G9,2,FALSE) - its functions the files of subs/tables/formulas/types/.
  // The first parameter names the sheet; the rest - formulas=, totals=, formats=, headers,
  // caption=, editable, seed= - is the body every sheet runs, subs/tables/formulas/spreadsheet.php.

  $padSheetRecord = FALSE;
  $padSheetName = trim ( (string) ( is_string ( $padParm ) ? $padParm : '' ) );

  return include PAD . 'subs/tables/formulas/spreadsheet.php';

?>
