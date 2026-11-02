<?php

  // The date functions on the sample sheet (_lib/formulas.php): a date is its serial of the
  // 1900 system, the days since 1899-12-30, with Excel's 29 February 1900 (serial 60) and
  // its 0 of 1900-01-00; DATE rolls a month or a day over, counts a year below 1900 from 1900;
  // WEEKDAY's types; EOMONTH and EDATE keep to the month's end. TODAY and NOW stand still
  // here (padNowFreeze), so they are compared, not shown.

  padNowFreeze ( '2026-11-01 14:30:00' );

  $cases = formulasRun ( [
    '=DATE(2024,1,15)',
    '=DATE(1900,1,1)',
    '=DATE(1900,2,28)',
    '=DATE(1900,2,29)',
    '=DATE(1900,3,1)',
    '=DATE(99,1,1)',
    '=DATE(2024,13,1)',
    '=DATE(2024,0,1)',
    '=DATE(2024,1,0)',
    '=DATE(9999,12,31)',
    '=DATE(-1,1,1)',
    '=DATE(10000,1,1)',
    '=TEXT(DATE(2024,2,30),"yyyy-mm-dd")',
    '=YEAR(D3)',
    '=YEAR("2025-06-30")',
    '=YEAR(0)',
    '=YEAR(-1)',
    '=MONTH(D3)',
    '=MONTH("15 March 2024")',
    '=MONTH(60)',
    '=DAY(D3)',
    '=DAY(45306)',
    '=DAY(60)',
    '=DAY(0)',
    '=WEEKDAY(D2)',
    '=WEEKDAY(D2,2)',
    '=WEEKDAY(D2,3)',
    '=WEEKDAY(D2,11)',
    '=WEEKDAY(D2,16)',
    '=WEEKDAY(1)',
    '=WEEKDAY(D2,4)',
    '=EOMONTH(D2,0)',
    '=EOMONTH(D2,1)',
    '=EOMONTH(D2,12)',
    '=EOMONTH("2024-03-15",-1)',
    '=EDATE("2024-01-31",1)',
    '=EDATE(D2,-2)',
    '=EDATE("2024-03-31",-1)',
    '=DAYS(D6,D2)',
    '=DAYS(D2,D6)',
    '=DAYS("2024-03-01","2024-02-01")',
    '=TODAY()',
    '=NOW()',
    '=TODAY()=INT(NOW())'
  ] );

?>
