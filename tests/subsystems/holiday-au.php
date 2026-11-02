<?php
require __DIR__.'/_holiday_boot.php';
// Independent complete sets transcribed from ACT Government's current/prior-year calendars.
holidayFixture('au',2022,'01-01 01-03 01-26 03-14 04-15 04-16 04-17 04-18 04-25 05-30 06-13 09-22 10-03 12-25 12-26 12-27','act');
holidayFixture('au',2024,'01-01 01-26 03-11 03-29 03-30 03-31 04-01 04-25 05-27 06-10 10-07 12-25 12-26','act');
holidayFixture('au',2026,'01-01 01-26 03-09 04-03 04-04 04-05 04-06 04-25 04-27 06-01 06-08 10-05 12-25 12-26 12-28','act');
holidayFixture('au',2027,'01-01 01-26 03-08 03-26 03-27 03-28 03-29 04-25 04-26 05-31 06-14 10-04 12-25 12-26 12-27 12-28','act');
holidayFixture('au',2026,'01-01 01-26 04-03 04-04 04-05 04-06 04-25 04-27 06-08 10-05 12-25 12-26 12-28','nsw');
$dates=holidayDates(holidayRows('au',2025,'nsw'));verify(in_array('2025-01-27',$dates)&&!in_array('2025-01-26',$dates),'Australia Day moves, no duplicate actual');verify(!in_array('2026-04-27',holidayDates(holidayRows('au',2026))),'national baseline no regional additional day');
$rows=holidayRows('au',2027,'act');$sub=array_column(array_filter($rows,fn($r)=>$r['substitute']),'date');verify($sub===['2027-04-26','2027-12-27','2027-12-28'],'substitute flags');
verify(is_string(padHolidaysPlace('au-vic')),'unsupported region');foreach([2021,2028] as $y)verify(padHolidaysKnown('au',padHolidaysDay($y,1,1),padHolidaysDay($y,12,31))!=='','unsupported year');echo "holidays au: OK\n";
