<?php
// Estonia: the national day and eleven public holidays (Public Holidays and Days of
// National Importance Act §§1–2), including Christmas Eve since 2005. No weekend transfers.
// Full calendar years 2006–2026; names from the State Chancellery. Verified 2026-11-02.
// Sources and multiyear fixtures: docs/subsystems/holidays/ee.md.
return [
 'name'=>'Estonia', 'langs'=>['et'], 'years'=>[2006,2026],
 'days'=>[
  ['date'=>'01-01','name'=>"New Year's Day",'local'=>'Uusaasta'],
  ['date'=>'02-24','name'=>'Independence Day','local'=>'Iseseisvuspäev'],
  ['easter'=>-2,'name'=>'Good Friday','local'=>'Suur reede'],
  ['easter'=>0,'name'=>'Easter Sunday','local'=>'Ülestõusmispühade 1. püha'],
  ['date'=>'05-01','name'=>'May Day','local'=>'Kevadpüha'],
  ['easter'=>49,'name'=>'Pentecost Sunday','local'=>'Nelipühade 1. püha'],
  ['date'=>'06-23','name'=>'Victory Day','local'=>'Võidupüha'],
  ['date'=>'06-24','name'=>'Midsummer Day','local'=>'Jaanipäev'],
  ['date'=>'08-20','name'=>'Restoration of Independence Day','local'=>'Taasiseseisvumispäev'],
  ['date'=>'12-24','name'=>'Christmas Eve','local'=>'Jõululaupäev'],
  ['date'=>'12-25','name'=>'Christmas Day','local'=>'Esimene jõulupüha'],
  ['date'=>'12-26','name'=>'Boxing Day','local'=>'Teine jõulupüha'],
 ]
];
