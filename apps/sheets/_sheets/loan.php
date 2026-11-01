<?php

  // The loan: what is asked in B1:B3, what it comes to in B5:B8, and a year a row from row
  // 11 - thirty of them, those past the term left empty. The balance at the end of a year is
  // what FV says is left after its payments; the principal paid is the fall of the balance,
  // the interest the rest of what was paid.

  $loan = [
    'A1' => 'Amount',          'B1' => 200000,
    'A2' => 'Interest a year', 'B2' => '4.5%',
    'A3' => 'Years',           'B3' => 25,
    'A5' => 'Monthly payment', 'B5' => '=PMT(B2/12,B3*12,-B1)',
    'A6' => 'Payments',        'B6' => '=B3*12',
    'A7' => 'Total paid',      'B7' => '=B5*B6',
    'A8' => 'Total interest',  'B8' => '=B7-B1',
    'A10' => 'Year', 'B10' => 'Paid', 'C10' => 'Interest', 'D10' => 'Principal', 'E10' => 'Balance'
  ];

  for ( $year = 1; $year <= 30; $year++ ) {
    $row  = 10 + $year;
    $last = $row - 1;
    $loan ["A$row"] = "=IF($year>\$B\$3,\"\",$year)";
    $loan ["B$row"] = "=IF(A$row=\"\",\"\",12*\$B\$5)";
    $loan ["E$row"] = "=IF(A$row=\"\",\"\",FV(\$B\$2/12,A$row*12,\$B\$5,-\$B\$1))";
    $loan ["D$row"] = "=IF(A$row=\"\",\"\",IF(A$row=1,\$B\$1,E$last)-E$row)";
    $loan ["C$row"] = "=IF(A$row=\"\",\"\",B$row-D$row)";
  }

?>
