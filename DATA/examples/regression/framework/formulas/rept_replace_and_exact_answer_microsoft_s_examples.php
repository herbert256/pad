<?php

  // REPT, REPLACE and EXACT (types/rept.php, replace.php, exact.php) on the tables of
  // Microsoft's pages for them, with the answers those pages give; then the edges: REPT's
  // count cut toward 0, 0 and the empty text "", below 0 #VALUE!, past 32767 characters
  // #VALUE! without building the text; REPLACE inserting with a count of 0, adding at the end
  // from a start past it, a start below 1 or a count below 0 #VALUE!, characters - not bytes -
  // counted, a number replaced in as the text it shows; EXACT in the same case only, a number,
  // a logical value and an empty cell as their texts, an error the answer. Then on the sample
  // sheet (_lib/formulas.php).

  $textReplace = [ [ 'Data' ], [ 'abcdefghijk' ], [ 2009 ], [ 123456 ] ];
  $textExact   = [ [ 'First string', 'Second string' ], [ 'word', 'word' ], [ 'Word', 'word' ], [ 'w ord', 'word' ] ];

  $textCases = [
    [ '=REPT("*-", 3)', [] ],
    [ '=REPT("-",10)', [] ],
    [ '=REPLACE(A2,6,5,"*")', $textReplace ],
    [ '=REPLACE(A3,3,2,"10")', $textReplace ],
    [ '=REPLACE(A4,1,3,"@")', $textReplace ],
    [ '=EXACT(A2,B2)', $textExact ],
    [ '=EXACT(A3,B3)', $textExact ],
    [ '=EXACT(A4,B4)', $textExact ],
    [ '=REPT("ab",2.9)', [] ],
    [ '=REPT("ab",-0.5)', [] ],
    [ '=REPT("ab",0)', [] ],
    [ '=REPT("ab",-1)', [] ],
    [ '=REPT("",1E300)', [] ],
    [ '=LEN(REPT("ab",16383))', [] ],
    [ '=REPT("ab",16384)', [] ],
    [ '=REPT("x",1E300)', [] ],
    [ '=REPT(1.5,2)', [] ],
    [ '=REPT("x","y")', [] ],
    [ '=REPLACE("abc",2,0,"x")', [] ],
    [ '=REPLACE("abc",10,1,"x")', [] ],
    [ '=REPLACE("abc",2,1E300,"x")', [] ],
    [ '=REPLACE("abc",0,1,"x")', [] ],
    [ '=REPLACE("abc",2,-1,"x")', [] ],
    [ '=REPLACE("äöü",2,1,"o")', [] ],
    [ '=REPLACE(REPT("a",32767),1,0,"b")', [] ],
    [ '=REPLACE(REPT("a",32767),1,1,"b")=REPT("a",32767)', [] ],
    [ '=EXACT(1,"1")', [] ],
    [ '=EXACT(TRUE,"TRUE")', [] ],
    [ '=EXACT(TRUE,"true")', [] ],
    [ '=EXACT(A1,"")', [] ],
    [ '=EXACT("Ä","ä")', [] ],
    [ '=EXACT(#N/A,1/0)', [] ]
  ];

  $textOut = [];

  foreach ( $textCases as [ $textFormula, $textCells ] )
    $textOut [] = "$textFormula: " . var_export ( padFormula ( $textFormula, $textCells ), TRUE );

  $cases = implode ( ' | ', $textOut ) . ' | ' . formulasRun ( [
    '=REPT(A6,2)',
    '=REPT("|",B3)',
    '=REPLACE(A2,1,1,"Crab a")',
    '=REPLACE(C2,2,1,",")',
    '=EXACT(A2,A5)',
    '=EXACT(A2,"apples")',
    '=EXACT(B6,"0")'
  ] );

?>
