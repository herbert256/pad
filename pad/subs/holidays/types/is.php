<?php
// is - Iceland, verified 2024-2026. Statutory partial holidays start at 13:00.
// Sources and limits: docs/subsystems/holidays/is-lt-lv.md.
return ['name'=>'Iceland','langs'=>['is'],'years'=>[2024,2026],'days'=>[
 ['date'=>'01-01','name'=>"New Year's Day",'local'=>'Nýársdagur'],
 ['easter'=>-3,'name'=>'Maundy Thursday','local'=>'Skírdagur'],
 ['easter'=>-2,'name'=>'Good Friday','local'=>'Föstudagurinn langi'],
 ['easter'=>0,'name'=>'Easter Sunday','local'=>'Páskadagur'],
 ['easter'=>1,'name'=>'Easter Monday','local'=>'Annar í páskum'],
 ['onOrAfter'=>['thu','04-19'],'name'=>'First Day of Summer','local'=>'Sumardagurinn fyrsti'],
 ['date'=>'05-01','name'=>'Labour Day','local'=>'Verkalýðsdagurinn'],
 ['easter'=>39,'name'=>'Ascension Day','local'=>'Uppstigningardagur'],
 ['easter'=>49,'name'=>'Whit Sunday','local'=>'Hvítasunnudagur'],
 ['easter'=>50,'name'=>'Whit Monday','local'=>'Annar í hvítasunnu'],
 ['date'=>'06-17','name'=>'National Day','local'=>'Þjóðhátíðardagurinn'],
 ['nth'=>[1,'mon',8],'name'=>'Commerce Day','local'=>'Frídagur verslunarmanna'],
 ['date'=>'12-24','name'=>'Christmas Eve','local'=>'Aðfangadagur','type'=>'partial','startTime'=>'13:00'],
 ['date'=>'12-25','name'=>'Christmas Day','local'=>'Jóladagur'],
 ['date'=>'12-26','name'=>'Boxing Day','local'=>'Annar í jólum'],
 ['date'=>'12-31','name'=>"New Year's Eve",'local'=>'Gamlársdagur','type'=>'partial','startTime'=>'13:00'],
]];
