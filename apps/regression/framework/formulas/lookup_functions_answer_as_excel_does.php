<?php

  // The lookup functions on the sample sheet (_lib/formulas.php): an exact VLOOKUP and MATCH
  // compare a text in any case, with wildcards; an approximate one finds the last value not
  // above the one looked for in a column that rises (the dates of D); a column past the
  // table is #REF!, one below 1 #VALUE!; XLOOKUP's modes; INDEX of one row or column takes a
  // single place, 0 for the whole row or column.

  $cases = formulasRun ( [
    '=VLOOKUP("Cherries",A2:C7,3,FALSE)',
    '=VLOOKUP("pea*",A2:C7,2,FALSE)',
    '=VLOOKUP("apples",A2:C7,2,FALSE)',
    '=VLOOKUP("Kiwis",A2:C7,2,FALSE)',
    '=VLOOKUP("Cherries",A2:C7,4,FALSE)',
    '=VLOOKUP("Cherries",A2:C7,0,FALSE)',
    '=VLOOKUP(45400,D2:E7,2)',
    '=VLOOKUP(45000,D2:E7,2)',
    '=VLOOKUP(99999,D2:E7,2,TRUE)',
    '=HLOOKUP("Price",A1:E7,3,FALSE)',
    '=HLOOKUP("Size",A1:E7,2,FALSE)',
    '=HLOOKUP("Qty",A1:E7,8,FALSE)',
    '=XLOOKUP("Plums",A2:A7,C2:C7)',
    '=XLOOKUP("Kiwis",A2:A7,C2:C7,"none")',
    '=XLOOKUP("Kiwis",A2:A7,C2:C7)',
    '=XLOOKUP(12,B2:B7,A2:A7,,1)',
    '=XLOOKUP(12,B2:B7,A2:A7,,-1)',
    '=XLOOKUP("Apples",A2:A7,B2:B7,,0,-1)',
    '=XLOOKUP("p*",A2:A7,B2:B7,,2)',
    '=XLOOKUP("Apples",A2:A7,B2:B4)',
    '=INDEX(A2:C7,3,1)',
    '=INDEX(C2:C7,4)',
    '=INDEX(A1:E1,3)',
    '=INDEX(A2:C7,9,1)',
    '=SUM(INDEX(B2:C7,0,1))',
    '=MATCH("Pears",A2:A7,0)',
    '=MATCH(DATE(2024,6,1),D2:D7)',
    '=MATCH("Kiwis",A2:A7,0)',
    '=MATCH("*e*",A2:A7,0)',
    '=CHOOSE(2,"a","b","c")',
    '=CHOOSE(4,"a","b","c")',
    '=CHOOSE(1.9,"a","b")'
  ] );

?>
