<?php

  // The logical functions on the sample sheet (_lib/formulas.php): IF is FALSE without an
  // else and 0 for a branch left empty, its test a number, a logical value or the text TRUE
  // or FALSE; AND, OR and XOR leave the text of a range out and answer #VALUE! without any
  // logical value; IFERROR catches every error, IFNA only #N/A.

  $cases = formulasRun ( [
    '=IF(5>3,"yes","no")',
    '=IF(1>2,"yes")',
    '=IF(1>2,"yes",)',
    '=IF(TRUE,,"no")',
    '=IF("maybe",1,2)',
    '=IF("true",1,2)',
    '=IF(0,1,2)',
    '=IF(1/0,1,2)',
    '=IF(TRUE,1,1/0)',
    '=IFS(1>2,"a",2>1,"b")',
    '=IFS(FALSE,1)',
    '=IFS(1/0,1,TRUE,2)',
    '=AND(TRUE,1,"TRUE")',
    '=AND(TRUE,0)',
    '=AND(A2:A3)',
    '=AND(B2:B3)',
    '=AND(B2:B7)',
    '=AND("x")',
    '=OR(FALSE,0,"TRUE")',
    '=OR(FALSE,0)',
    '=OR(B6,FALSE)',
    '=XOR(TRUE,TRUE,TRUE)',
    '=XOR(TRUE,TRUE)',
    '=NOT(TRUE)',
    '=NOT(0)',
    '=NOT("x")',
    '=IFERROR(1/0,"none")',
    '=IFERROR(4/2,"none")',
    '=IFERROR(#N/A,0)',
    '=IFNA(#N/A,0)',
    '=IFNA(1/0,0)',
    '=IFNA(VLOOKUP("Kiwis",A2:C7,2,FALSE),"none")'
  ] );

?>
