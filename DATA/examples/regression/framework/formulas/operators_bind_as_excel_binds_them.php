<?php

  // The operators by Excel's precedence (_lib/formulas.php): a minus before a value binds
  // tighter than ^ - =-2^2 is 4 - and ^ is read from the left - =2^3^2 is 64; then %, * and
  // /, + and -, &, the comparisons. Numbers compare as they show, to 15 digits, and a sum
  // that is only the last bits of its operands is 0; numbers come before texts before
  // logical values; a text compares in any case; an empty cell is 0 or "". Function names and
  // references are read in any case, $ fixes nothing a formula in one cell needs.

  $cases = formulasRun ( [
    '=-2^2',
    '=2^3^2',
    '=-2^3',
    '=2^-2',
    '=1+2*3',
    '=(1+2)*3',
    '=10-2-3',
    '=12/3/2',
    '=2*3^2',
    '=-3^2+1',
    '=50%',
    '=200%^2',
    '=-10%',
    '=1+2&3',
    '="a"&1+1',
    '=1+1=2',
    '=1<2=TRUE',
    '=2>1+1',
    '=0.1+0.2=0.3',
    '=0.1+0.2-0.3',
    '=1-0.9-0.1',
    '=--5',
    '=+-+5',
    '="A"="a"',
    '="b">"A"',
    '=TRUE>1',
    '="1"=1',
    '="10"+1',
    '=TRUE+TRUE',
    '=A99=0',
    '=A99=""',
    '=A99',
    '=sum(1,2)',
    '=Sum( 1 , 2 )',
    '=$B$2+B$3+$B4',
    '=b2*c2',
    '="say ""hi"""'
  ] );

?>
