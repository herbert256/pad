<?php

  // AVERAGEIFS, MINIFS and MAXIFS (types/averageifs.php, minifs.php, maxifs.php) on the tables
  // of Microsoft's pages for them, with the answers those pages give - the criteria those of
  // SUMIFS and COUNTIFS (padFormulaHits); a criteria range of another size or shape is #VALUE!,
  // even one shifted by a row is taken place by place; only the numbers of the range count, an
  // error at a place that meets the criteria is the answer, one at another place is passed
  // over; without a number AVERAGEIFS is #DIV/0!, MINIFS and MAXIFS 0. Then on the sample sheet
  // (_lib/formulas.php).

  $ifsQuiz = [
    [ 'Student', 'First Quiz Grade', 'Second Quiz Grade', 'Final Exam Grade' ],
    [ 'Emilio', 75, 85, 87 ],
    [ 'Julie', 94, 80, 88 ],
    [ 'Hans', 86, 93, 'Incomplete' ],
    [ 'Frederique', 'Incomplete', 75, 75 ]
  ];

  $ifsHouses = [
    [ 'Type', 'Price', 'Town', 'Number of Bedrooms', 'Garage?' ],
    [ 'Cozy Rambler', 230000, 'Issaquah', 3, 'No' ],
    [ 'Snug Bungalow', 197000, 'Bellevue', 2, 'Yes' ],
    [ 'Cool Cape Codder', 345678, 'Bellevue', 4, 'Yes' ],
    [ 'Splendid Split Level', 321900, 'Issaquah', 2, 'Yes' ],
    [ 'Exclusive Tudor', 450000, 'Bellevue', 5, 'Yes' ],
    [ 'Classy Colonial', 395000, 'Bellevue', 4, 'No' ]
  ];

  $ifsGrades = [ [ 'Grade', 'Weight' ], [ 89, 1 ], [ 93, 2 ], [ 96, 2 ], [ 85, 3 ], [ 91, 1 ], [ 88, 1 ] ];

  $ifsWeights = [
    [ 'Weight', 'Grade', 'Class', 'Level' ],
    [ 10, 'b', 'Business', 100 ],
    [ 1, 'a', 'Technical', 100 ],
    [ 100, 'a', 'Business', 200 ],
    [ 1, 'b', 'Technical', 300 ],
    [ 1, 'a', 'Technical', 100 ],
    [ 1, 'a', 'Business', 400 ]
  ];

  $ifsOdd = [ [ 'x', 5 ], [ 'x', '#N/A' ], [ 'y', '#DIV/0!' ], [ 'x', TRUE ], [ 'x', 'text' ], [ 'x' ] ];

  $ifsCases = [
    [ '=AVERAGEIFS(B2:B5,B2:B5,">70",B2:B5,"<90")', $ifsQuiz ],
    [ '=AVERAGEIFS(C2:C5,C2:C5,">95")', $ifsQuiz ],
    [ '=AVERAGEIFS(D2:D5,D2:D5,"<>Incomplete",D2:D5,">80")', $ifsQuiz ],
    [ '=AVERAGEIFS(B2:B7,C2:C7,"Bellevue",D2:D7,">2",E2:E7,"Yes")', $ifsHouses ],
    [ '=AVERAGEIFS(B2:B7,C2:C7,"Issaquah",D2:D7,"<=3",E2:E7,"No")', $ifsHouses ],
    [ '=AVERAGEIFS(B2:B7,C2:C6,"Bellevue")', $ifsHouses ],
    [ '=AVERAGEIFS(A2:A7,C2:C7,"Bellevue")', $ifsHouses ],
    [ '=MINIFS(A2:A7,B2:B7,1)', $ifsGrades ],
    [ '=MINIFS(A2:A5,B3:B6,"a")', $ifsWeights ],
    [ '=MINIFS(A2:A7,B2:B7,"b",D2:D7,">100")', $ifsWeights ],
    [ '=MINIFS(A2:A7,B2:B7,"c")', $ifsWeights ],
    [ '=MINIFS(A2:A5,B2:C6,"a")', $ifsWeights ],
    [ '=MAXIFS(A2:A7,B2:B7,1)', $ifsGrades ],
    [ '=MAXIFS(A2:A5,B3:B6,"a")', $ifsWeights ],
    [ '=MAXIFS(A2:A7,B2:B7,"b",D2:D7,">100")', $ifsWeights ],
    [ '=MAXIFS(A2:A7,B2:B7,"c")', $ifsWeights ],
    [ '=MAXIFS(A2:A6,B2:B7,"a")', $ifsWeights ],
    [ '=MAXIFS(C2:C7,B2:B7,"a")', $ifsWeights ],
    [ '=MAXIFS(A2:A7,C2:C7,"t*",B2:B7,"a")', $ifsWeights ],
    [ '=MAXIFS(A2:A7,1,"a")', $ifsWeights ],
    [ '=AVERAGEIFS(B1:B1,A1:A1,"x")', $ifsOdd ],
    [ '=AVERAGEIFS(B1:B6,A1:A6,"x")', $ifsOdd ],
    [ '=MAXIFS(B1:B6,A1:A6,"y")', $ifsOdd ],
    [ '=MINIFS(B4:B6,A4:A6,"x")', $ifsOdd ],
    [ '=AVERAGEIFS(B4:B6,A4:A6,"x")', $ifsOdd ],
    [ '=MAXIFS(B1:B9,A1:A9,"")', $ifsOdd ]
  ];

  $ifsOut = [];

  foreach ( $ifsCases as [ $ifsFormula, $ifsCells ] )
    $ifsOut [] = "$ifsFormula: " . var_export ( padFormula ( $ifsFormula, $ifsCells ), TRUE );

  $cases = implode ( ' | ', $ifsOut ) . ' | ' . formulasRun ( [
    '=AVERAGEIFS(B2:B7,A2:A7,"Apples")',
    '=AVERAGEIFS(B2:B7,A2:A7,"apples",C2:C7,">1.15")',
    '=AVERAGEIFS(C2:C7,B2:B7,">5",B2:B7,"<20")',
    '=AVERAGEIFS(B2:B7,A2:A7,"Kiwis")',
    '=AVERAGEIFS(B2:B7,A2:A3,"Apples")',
    '=MINIFS(C2:C7,A2:A7,"Apples")',
    '=MINIFS(B2:B7,C2:C7,">2",B2:B7,">0")',
    '=MINIFS(D2:D7,D2:D7,">=2024-04-01")',
    '=MINIFS(B2:B7,A2:A7,"Kiwis")',
    '=MAXIFS(B2:B7,A2:A7,"Apples")',
    '=MAXIFS(D2:D7,A2:A7,"Apples")',
    '=MAXIFS(E2:E7,B2:B7,"<10")',
    '=MAXIFS(B2:B7,A2:A7,"Kiwis")',
    '=MAXIFS(B:B,A:A,"P*")',
    '=MAXIFS(B2:B7,A2:A7)'
  ] );

?>
