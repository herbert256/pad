<?php

  // The lenient walk under test: with the syntax check off a SUMPRODUCT of an operator on a
  // range - array evaluation, which the formulas do not have - shows #VALUE!, and its title
  // says why, as the title of every formula that cannot be read does, whatever the lenient
  // reading of it answers: =1/0+SUMM(1) is #DIV/0!, its title naming SUMM. Before, the
  // reason was shown only for #ERROR!, #NAME? and #N/A, and the SUMPRODUCT's title said
  // "a value of the wrong kind".

  $padCheckSyntax = FALSE;

?>
