<?php

  // NETWORKDAYS, WORKDAY and their .INTL forms (types/networkdays.php, networkdays.intl.php,
  // workday.php, workday.intl.php, _lib/dates.php) on the tables of Microsoft's pages for them,
  // with the answers those pages give - the array constants of the holidays of
  // NETWORKDAYS.INTL's page written as cells, which the formulas have instead; then the edges:
  // a start after the end counts below 0, 0 workdays is the start itself, days cut toward 0,
  // a holiday in the weekend or twice left out once, a text that is no date and a logical value
  // among the holidays #VALUE!, a date before 0 #NUM!, an error the answer; the weekend numbers
  // and texts, 0 and 18 #NUM!, a text of six or other characters #VALUE!, seven 1s 0 workdays
  // and no date; a count past 9999-12-31 #NUM! without a step per day. Then on the sample sheet
  // (_lib/formulas.php), where WORKDAY's cell shows a date.

  $workProject = [
    [ 'Date',       'Description' ],
    [ '2012-10-01', 'Start date of project' ],
    [ '2013-03-01', 'End date of project' ],
    [ '2012-11-22', 'Holiday' ],
    [ '2012-12-04', 'Holiday' ],
    [ '2013-01-21', 'Holiday' ]
  ];

  $workDays = [
    [ 'Data',       'Description' ],
    [ '10/1/2008',  'Start date' ],
    [ 151,          'Days to completion' ],
    [ '11/26/2008', 'Holiday' ],
    [ '12/4/2008',  'Holiday' ],
    [ '1/21/2009',  'Holiday' ]
  ];

  $workIntl = [ [ '2006/1/2' ], [ '2006/1/16' ] ];

  $workOdd = [
    [ '2024-01-15', '2024-01-20', 'Holiday', TRUE,  '#N/A', -1,           '2024-01-16' ],
    [ '2024-01-26', '2024-01-16', NULL,      FALSE, NULL,   '2024-01-17', '' ]
  ];

  $workCases = [
    [ '=NETWORKDAYS(A2,A3)', $workProject ],
    [ '=NETWORKDAYS(A2,A3,A4)', $workProject ],
    [ '=NETWORKDAYS(A2,A3,A4:A6)', $workProject ],
    [ '=TEXT(WORKDAY(A2,A3),"m/d/yyyy")', $workDays ],
    [ '=TEXT(WORKDAY(A2,A3,A4:A6),"m/d/yyyy")', $workDays ],
    [ '=WORKDAY(A2,A3,A4:A6)', $workDays ],
    [ '=NETWORKDAYS.INTL(DATE(2006,1,1),DATE(2006,1,31))', [] ],
    [ '=NETWORKDAYS.INTL(DATE(2006,2,28),DATE(2006,1,31))', [] ],
    [ '=NETWORKDAYS.INTL(DATE(2006,1,1),DATE(2006,2,1),7,A1:A2)', $workIntl ],
    [ '=NETWORKDAYS.INTL(DATE(2006,1,1),DATE(2006,2,1),"0010001",A1:A2)', $workIntl ],
    [ '=WORKDAY.INTL(DATE(2012,1,1),30,0)', [] ],
    [ '=WORKDAY.INTL(DATE(2012,1,1),90,11)', [] ],
    [ '=TEXT(WORKDAY.INTL(DATE(2012,1,1),30,17),"m/dd/yyyy")', [] ],
    [ '=NETWORKDAYS(A1,A1)', $workOdd ],
    [ '=NETWORKDAYS(B1,B1)', $workOdd ],
    [ '=NETWORKDAYS(A1,A2,G1:G2)', $workOdd ],
    [ '=NETWORKDAYS(A1,A2,F2:G2)', $workOdd ],
    [ '=NETWORKDAYS(A1,A2,B1:B2)', $workOdd ],
    [ '=NETWORKDAYS(A1,A2,C1:C2)', $workOdd ],
    [ '=NETWORKDAYS(A1,A2,D1)', $workOdd ],
    [ '=NETWORKDAYS(A1,A2,E1:E2)', $workOdd ],
    [ '=NETWORKDAYS(A1,A2,F1)', $workOdd ],
    [ '=NETWORKDAYS(A1,A2,"2024-01-16")', $workOdd ],
    [ '=NETWORKDAYS(A1,"x")', $workOdd ],
    [ '=NETWORKDAYS(A1,3000000)', $workOdd ],
    [ '=WORKDAY(B1,0)', $workOdd ],
    [ '=WORKDAY(A1,4.9)', $workOdd ],
    [ '=WORKDAY(A1,-4.9)', $workOdd ],
    [ '=WORKDAY(A1,5,G1:G2)', $workOdd ],
    [ '=WORKDAY(A1,5,C1)', $workOdd ],
    [ '=WORKDAY(A1,2600000)', $workOdd ],
    [ '=WORKDAY(A1,-1E15)', $workOdd ],
    [ '=WORKDAY(DATE(1900,1,2),-1)', $workOdd ],
    [ '=WORKDAY.INTL(A1,1,1.9)', $workOdd ],
    [ '=WORKDAY.INTL(A1,1,7)', $workOdd ],
    [ '=WORKDAY.INTL(A1,1,"0000001")', $workOdd ],
    [ '=WORKDAY.INTL(A1,6,"1111110")', $workOdd ],
    [ '=WORKDAY.INTL(A1,1,18)', $workOdd ],
    [ '=WORKDAY.INTL(A1,1,"000001")', $workOdd ],
    [ '=WORKDAY.INTL(A1,1,"0000x11")', $workOdd ],
    [ '=WORKDAY.INTL(A1,1,"1111111")', $workOdd ],
    [ '=WORKDAY.INTL(A1,1,,G1)', $workOdd ],
    [ '=WORKDAY.INTL(A1,1,E2,G1)', $workOdd ],
    [ '=NETWORKDAYS.INTL(A1,A2,"1111111")', $workOdd ],
    [ '=NETWORKDAYS.INTL(A1,A2,-1)', $workOdd ],
    [ '=NETWORKDAYS.INTL(A1,A2,E1)', $workOdd ],
    [ '=NETWORKDAYS.INTL(A1,A2,12)', $workOdd ]
  ];

  $workOut = [];

  foreach ( $workCases as [ $workFormula, $workCells ] )
    $workOut [] = "$workFormula: " . var_export ( padFormula ( $workFormula, $workCells ), TRUE );

  $cases = implode ( ' | ', $workOut ) . ' | ' . formulasRun ( [
    '=NETWORKDAYS(D2,D3)',
    '=NETWORKDAYS(D3,D2)',
    '=NETWORKDAYS(D2,D7,D3:D6)',
    '=NETWORKDAYS(D2,D7,A2:A3)',
    '=NETWORKDAYS.INTL(D2,D3,11,D3)',
    '=WORKDAY(D2,10)',
    '=WORKDAY(D3,-1)',
    '=WORKDAY.INTL(D2,10,"0000110")'
  ] );

?>
