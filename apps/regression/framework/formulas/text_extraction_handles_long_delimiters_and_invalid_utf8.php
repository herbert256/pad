<?php
  $delimiter = str_repeat ( '*', 16000 );
  $before = padFormula ( '=TEXTBEFORE(A1,B1)', [ 'A1' => 'left' . $delimiter . 'right', 'B1' => $delimiter ] );
  $after = padFormula ( '=TEXTAFTER(A1,B1)', [ 'A1' => 'left' . $delimiter . 'right', 'B1' => $delimiter ] );
  $error = padFormulaTextExtract ( [ "bad\xFF", 'a' ], [], FALSE );
  $answer = $before === 'left' && $after === 'right' && $error === [ 'error' => '#VALUE!' ]
    ? 'long literal delimiters work and invalid UTF-8 is a value error' : 'FAIL';
?>
