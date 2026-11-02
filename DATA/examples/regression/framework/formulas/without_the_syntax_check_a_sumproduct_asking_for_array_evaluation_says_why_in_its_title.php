<?php

  // The lenient walk under test: with the syntax check off a SUMPRODUCT of an operator on a
  // range - array evaluation, which the formulas do not have - shows #VALUE!, and its title
  // says why, as the title of every formula that cannot be read does, whatever the lenient
  // reading of it answers: =1/0+SUMM(1) is #DIV/0!, its title naming SUMM. Before, the
  // reason was shown only for #ERROR!, #NAME? and #N/A, and the SUMPRODUCT's title said
  // "a value of the wrong kind". The range is named as it is written, at its own place - 1:2
  // at 13, where it was A1:XFD2 at the S of SUMPRODUCT, 2.

  $padCheckSyntax = FALSE;

?>
