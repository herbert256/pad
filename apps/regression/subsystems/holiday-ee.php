<?php
require __DIR__.'/_holiday_boot.php';
// Complete sets: legal fixed dates plus official Tallinn calendars for movable dates.
holidayFixture('ee',2024,'01-01 02-24 03-29 03-31 05-01 05-19 06-23 06-24 08-20 12-24 12-25 12-26');
holidayFixture('ee',2025,'01-01 02-24 04-18 04-20 05-01 06-08 06-23 06-24 08-20 12-24 12-25 12-26');
holidayFixture('ee',2026,'01-01 02-24 04-03 04-05 05-01 05-24 06-23 06-24 08-20 12-24 12-25 12-26');
foreach([2006,2012,2023,2024,2025,2026] as $y){$rows=holidayRows('ee',$y);verify(count($rows)===12,'one national and eleven public holidays');foreach($rows as $r)verify($r['local']!==$r['name']&&!$r['substitute'],'local names and no observed transfers');}
verify(!in_array('2023-01-02',holidayDates(holidayRows('ee',2023))),'no Monday after Sunday new year');verify(!in_array('2024-04-01',holidayDates(holidayRows('ee',2024))),'no Easter Monday');foreach([2005,2027] as $y)verify(padHolidaysKnown('ee',padHolidaysDay($y,1,1),padHolidaysDay($y,12,31))!=='','unsupported year');echo "holidays ee: OK\n";
