<?php
  $cases = [
    '=TEXTBEFORE("abc","/",0)' => '#VALUE!',
    '=TEXTAFTER("abc","/",4)' => '#VALUE!',
    '=TEXTAFTER("abc","/",-4)' => '#VALUE!',
    '=TEXTBEFORE("abc","/",1,2)' => '#VALUE!',
    '=TEXTBEFORE("abc","/",1,0,2)' => '#VALUE!',
    '=TEXTAFTER("a/b","/",2)' => '#N/A',
    '=TEXTBEFORE("ABC","b")' => '#N/A',
    '=TEXTAFTER(1/0,"/")' => '#DIV/0!',
    '=TEXTAFTER("a/b","/",1,0,0,1/0)' => 'b'
  ];
  $failures = [];
  foreach ( $cases as $formula => $expected ) {
    $value = padFormula ( $formula );
    if ( ( is_array ( $value ) ? ( $value ['error'] ?? '' ) : $value ) !== $expected ) $failures [] = $formula;
  }
  $answer = $failures ? implode ( '; ', $failures ) : count ( $cases ) . ' error and fallback checks match';
?>
