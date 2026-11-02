<?php
require __DIR__.'/_holiday_boot.php';
holidayFixture('is',2024,'01-01 03-28 03-29 03-31 04-01 04-25 05-01 05-09 05-19 05-20 06-17 08-05 12-25 12-26');
holidayFixture('is',2025,'01-01 04-17 04-18 04-20 04-21 04-24 05-01 05-29 06-08 06-09 06-17 08-04 12-25 12-26');
holidayFixture('is',2026,'01-01 04-02 04-03 04-05 04-06 04-23 05-01 05-14 05-24 05-25 06-17 08-03 12-25 12-26');
holidayFixture('lt',2024,'01-01 02-16 03-11 03-31 04-01 05-01 05-05 06-02 06-24 07-06 08-15 11-01 11-02 12-24 12-25 12-26');
holidayFixture('lt',2025,'01-01 02-16 03-11 04-20 04-21 05-01 05-04 06-01 06-24 07-06 08-15 11-01 11-02 12-24 12-25 12-26');
holidayFixture('lt',2026,'01-01 02-16 03-11 04-05 04-06 05-01 05-03 06-07 06-24 07-06 08-15 11-01 11-02 12-24 12-25 12-26');
holidayFixture('lv',2024,'01-01 03-29 03-31 04-01 05-01 05-04 05-06 05-12 05-19 06-23 06-24 11-18 12-24 12-25 12-26 12-31');
holidayFixture('lv',2025,'01-01 04-18 04-20 04-21 05-01 05-04 05-05 05-11 06-08 06-23 06-24 11-18 12-24 12-25 12-26 12-31');
holidayFixture('lv',2026,'01-01 04-03 04-05 04-06 05-01 05-04 05-10 05-24 06-23 06-24 11-18 12-24 12-25 12-26 12-31');
foreach(['is','lt','lv'] as $country)foreach([2023,2027] as $year)verify(padHolidaysKnown($country,padHolidaysDay($year,1,1),padHolidaysDay($year,12,31))!=='','known year bounds');
foreach([2024,2025,2026] as $year){
 $partial=array_values(array_filter(holidayRows('is',$year),fn($r)=>$r['type']==='partial'));
 verify(array_column($partial,'date')===[$year.'-12-24',$year.'-12-31'],'partial dates');
 foreach($partial as $row){verify($row['startTime']==='13:00','partial time');verify(str_contains(padHolidaysTypeLabel($row),'13:00')&&!str_contains(padHolidaysTypeLabel($row),'no day off'),'partial label');}
 $z=padHolidaysDay($year,12,24);verify(!isset(padHolidaysOff('is','',$z,$z)[$z]),'partial is not a whole-day closure');
}
echo "holidays is/lt/lv: nine complete year sets and partial-day semantics passed\n";
