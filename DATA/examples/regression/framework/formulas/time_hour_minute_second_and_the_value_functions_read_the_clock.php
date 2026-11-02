<?php

  // TIME, HOUR, MINUTE, SECOND, TIMEVALUE and DATEVALUE (types/time.php, hour.php, minute.php,
  // second.php, timevalue.php, datevalue.php) on the tables of Microsoft's pages for them, with
  // the answers those pages give - DATEVALUE("5-JUL"), which reads the year of the clock, left
  // out; then the edges: TIME's parts cut toward 0 and running over a day, 32768 and a time
  // below 0 #NUM!; HOUR, MINUTE and SECOND rounded to the second as a time shows, 13:45 less
  // 1:45 the 12 hours it shows, a serial below 0 or past 9999-12-31 #NUM!; TIMEVALUE and
  // DATEVALUE take text only, a time alone no date, a date alone the time 0, a date before
  // 1900 no date. Then on the sample sheet (_lib/formulas.php), whose TIME cell shows hh:mm and
  // DATEVALUE's a date.

  $clockTime   = [ [ 'Hour', 'Minute', 'Second' ], [ 12, 0, 0 ], [ 16, 48, 10 ] ];
  $clockHour   = [ [ 'Time' ], [ 0.75 ], [ '7/18/2011 7:45' ], [ '4/21/2012' ] ];
  $clockMinute = [ [ 'Time' ], [ '12:45:00 PM' ] ];
  $clockSecond = [ [ 'Time' ], [ '' ], [ '4:48:18 PM' ], [ '4:48 PM' ] ];
  $clockDate   = [ [ 'Data' ], [ 11 ], [ 3 ], [ 2011 ] ];

  $clockCases = [
    [ '=TIME(A2,B2,C2)', $clockTime ],
    [ '=ROUND(TIME(A3,B3,C3),7)', $clockTime ],
    [ '=HOUR(A2)', $clockHour ],
    [ '=HOUR(A3)', $clockHour ],
    [ '=HOUR(A4)', $clockHour ],
    [ '=MINUTE(A2)', $clockMinute ],
    [ '=SECOND(A3)', $clockSecond ],
    [ '=SECOND(A4)', $clockSecond ],
    [ '=DATEVALUE("8/22/2011")', [] ],
    [ '=DATEVALUE("22-MAY-2011")', [] ],
    [ '=DATEVALUE("2011/02/23")', [] ],
    [ '=DATEVALUE(A2 & "/" & A3 & "/" & A4)', $clockDate ],
    [ '=ROUND(TIMEVALUE("2:24 AM"),2)', [] ],
    [ '=ROUND(TIMEVALUE("22-Aug-2011 6:35 AM"),4)', [] ],
    [ '=TIME(27,0,0)', [] ],
    [ '=TIME(0,750,0)=TIME(12,30,0)', [] ],
    [ '=TIME(0,0,2000)=TIME(0,33,20)', [] ],
    [ '=TIME(1.9,30.9,0)=TIME(1,30,0)', [] ],
    [ '=TIME(1,-30,0)*1440', [] ],
    [ '=TIME(-1,120,0)*24', [] ],
    [ '=TIME(24,0,0)', [] ],
    [ '=TIME(0,-1,0)', [] ],
    [ '=TIME(32768,0,0)', [] ],
    [ '=TIME(32767,0,0)*24', [] ],
    [ '=TIME("x",0,0)', [] ],
    [ '=HOUR("13:45"-"1:45")', [] ],
    [ '=MINUTE("13:45"-"1:45")', [] ],
    [ '=SECOND("12:00:59.6")', [] ],
    [ '=MINUTE("12:00:59.6")', [] ],
    [ '=HOUR(0.99999999)', [] ],
    [ '=HOUR(-0.5)', [] ],
    [ '=SECOND(2958466)', [] ],
    [ '=MINUTE("noon")', [] ],
    [ '=HOUR(#N/A)', [] ],
    [ '=TIMEVALUE("18:45")', [] ],
    [ '=TIMEVALUE("2024-01-15")', [] ],
    [ '=TIMEVALUE(0.5)', [] ],
    [ '=TIMEVALUE("25:00")', [] ],
    [ '=DATEVALUE("13:45")', [] ],
    [ '=DATEVALUE("5-JUL")', [] ],
    [ '=DATEVALUE("1899-12-31")', [] ],
    [ '=DATEVALUE("9999-12-31 23:59")', [] ],
    [ '=DATEVALUE(45306)', [] ],
    [ '=DATEVALUE("2024-02-30")', [] ]
  ];

  $clockOut = [];

  foreach ( $clockCases as [ $clockFormula, $clockCells ] )
    $clockOut [] = "$clockFormula: " . var_export ( padFormula ( $clockFormula, $clockCells ), TRUE );

  $cases = implode ( ' | ', $clockOut ) . ' | ' . formulasRun ( [
    '=HOUR(D2)',
    '=DATEVALUE(D2)',
    '=DATEVALUE(TEXT(D3,"yyyy-mm-dd"))',
    '=TIMEVALUE(TEXT(D2+TIME(9,30,0),"yyyy-mm-dd hh:mm"))*24',
    '=D2+TIME(36,0,0)'
  ] );

?>
