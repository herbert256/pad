<?php
// Australia: common national baseline, with complete statewide NSW and ACT full-day calendars.
// Supported 2022–2027 only. Local event/show days and sector-only bank days are excluded.
// Verified 2026-11-02 against official NSW/ACT calendars and exceptional declarations.
// Sources and complete fixtures: docs/subsystems/holidays/au.md.
$auAustraliaDay=function($year){$day=padHolidaysDay($year,1,26);$weekday=padHolidaysWeekday($day);return padHolidaysDate($day+($weekday>=6?8-$weekday:0));};
return [
 'name'=>'Australia (national baseline; NSW and ACT supported)',
 'langs'=>['en'], 'regions'=>['nsw'=>'New South Wales','act'=>'Australian Capital Territory'],
 'years'=>[2022,2027], 'substitute'=>'next', 'suffix'=>' (additional day)',
 'days'=>[
  ['date'=>'01-01','name'=>"New Year's Day",'substitute'=>TRUE],
  ['calc'=>$auAustraliaDay,'name'=>'Australia Day'],
  ['easter'=>-2,'name'=>'Good Friday'],
  ['easter'=>-1,'name'=>'Easter Saturday','in'=>['nsw','act']],
  ['easter'=>0,'name'=>'Easter Sunday','in'=>['nsw','act']],
  ['easter'=>1,'name'=>'Easter Monday'],
  ['date'=>'04-25','name'=>'Anzac Day','not'=>['nsw','act']],
  ['date'=>'04-25','name'=>'Anzac Day','in'=>['nsw','act'],'to'=>2025],
  ['date'=>'04-25','name'=>'Anzac Day','in'=>['nsw','act'],'from'=>2026,'to'=>2027,'substitute'=>'monday'],
  ['nth'=>[2,'mon',3],'name'=>'Canberra Day','in'=>['act']],
  ['onOrAfter'=>['mon','05-27'],'name'=>'Reconciliation Day','in'=>['act']],
  ['nth'=>[2,'mon',6],'name'=>"Queen's Birthday",'in'=>['nsw','act'],'to'=>2022],
  ['nth'=>[2,'mon',6],'name'=>"King's Birthday",'in'=>['nsw','act'],'from'=>2023],
  ['nth'=>[1,'mon',10],'name'=>'Labour Day','in'=>['nsw','act']],
  ['date'=>'09-22','name'=>'National Day of Mourning for Queen Elizabeth II','from'=>2022,'to'=>2022],
  ['date'=>'12-25','name'=>'Christmas Day','substitute'=>TRUE],
  ['date'=>'12-26','name'=>'Boxing Day','substitute'=>TRUE],
 ]
];
