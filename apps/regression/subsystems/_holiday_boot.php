<?php
define('PAD',dirname(__DIR__,3).'/pad/');define('DATA','/nonexistent-pad-test-data/');
require PAD.'lib/dirs.php';require PAD.'lib/subsystem.php';require PAD.'lib/encoding.php';require PAD.'lib/template.php';require PAD.'subs/holidays/_lib/holidays.php';
function verify($ok,$why){if(!$ok)throw new RuntimeException($why);}
function holidayRows($country,$year,$region=''){return padHolidaysRange($country,$region,padHolidaysDay($year,1,1),padHolidaysDay($year,12,31));}
function holidayDates($rows,$type='public'){return array_values(array_unique(array_column(array_filter($rows,fn($r)=>$r['type']===$type),'date')));}
function holidayFixture($country,$year,$expected,$region=''){$actual=holidayDates(holidayRows($country,$year,$region));$dates=array_map(fn($d)=>$year.'-'.$d,explode(' ',$expected));sort($actual);sort($dates);verify($actual===$dates,"$country-$region $year dates: ".json_encode($actual));}
