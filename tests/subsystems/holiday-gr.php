<?php
require __DIR__.'/_holiday_boot.php';
// Orthodox dates from Bank of Greece/HBA calendars; legal public scope from the Labour Inspectorate.
$easters=[2022=>'04-24',2023=>'04-16',2024=>'05-05',2025=>'04-20',2026=>'04-12'];
$mondays=[2022=>'04-25',2023=>'04-17',2024=>'05-06',2025=>'04-21',2026=>'04-13'];
$may=[2022=>'05-02',2023=>'05-01',2024=>'05-07',2025=>'05-01',2026=>'05-01'];
$clean=[2022=>'03-07',2023=>'02-27',2024=>'03-18',2025=>'03-03',2026=>'02-23'];
$whit=[2022=>'06-13',2023=>'06-05',2024=>'06-24',2025=>'06-09',2026=>'06-01'];
foreach($easters as $year=>$easter){verify(padHolidaysDate(padHolidaysOrthodox($year))==="$year-$easter",'Orthodox fixture');holidayFixture('gr',$year,"01-01 01-06 03-25 {$mondays[$year]} {$may[$year]} 08-15 10-28 12-25 12-26");$rows=holidayRows('gr',$year);$obs=holidayDates($rows,'observance');verify(in_array("$year-{$clean[$year]}",$obs)&&in_array("$year-{$whit[$year]}",$obs),'sector-specific dates');foreach($rows as $r)verify($r['local']!==$r['name']&&!$r['substitute'],'local names and no automatic transfers');}
verify(padHolidaysOrthodox(2024)!==padHolidaysEaster(2024),'Orthodox differs from Western Easter');verify(!in_array('2024-05-01',holidayDates(holidayRows('gr',2024))),'May Day original removed');foreach([2021,2027] as $y)verify(padHolidaysKnown('gr',padHolidaysDay($y,1,1),padHolidaysDay($y,12,31))!=='','unsupported year');echo "holidays gr: OK\n";
