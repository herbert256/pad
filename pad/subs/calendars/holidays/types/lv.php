<?php
// lv - Latvia: statutory public holidays, verified 2024-2026. Government working-day
// transfers are work schedules, not additional public holidays, and are not included.
return ['name'=>'Latvia','langs'=>['lv'],'years'=>[2024,2026],
 'substitute'=>'monday','suffix'=>' (substitute day)','suffixLocal'=>' (pārceltā brīvdiena)','days'=>[
 ['date'=>'01-01','name'=>"New Year's Day",'local'=>'Jaungada diena'],
 ['easter'=>-2,'name'=>'Good Friday','local'=>'Lielā Piektdiena'],
 ['easter'=>0,'name'=>'Easter Sunday','local'=>'Pirmās Lieldienas'],
 ['easter'=>1,'name'=>'Easter Monday','local'=>'Otrās Lieldienas'],
 ['date'=>'05-01','name'=>'Labour Day and Constituent Assembly Day','local'=>'Darba svētki, Latvijas Republikas Satversmes sapulces sasaukšanas diena'],
 ['date'=>'05-04','name'=>'Restoration of Independence Day','local'=>'Latvijas Republikas Neatkarības atjaunošanas diena','substitute'=>TRUE],
 ['nth'=>[2,'sun',5],'name'=>"Mother's Day",'local'=>'Mātes diena'],
 ['easter'=>49,'name'=>'Whit Sunday','local'=>'Vasarsvētki'],
 ['date'=>'06-23','name'=>'Midsummer Eve','local'=>'Līgo diena'],
 ['date'=>'06-24','name'=>'Midsummer Day','local'=>'Jāņu diena'],
 ['date'=>'11-18','name'=>'Proclamation of the Republic Day','local'=>'Latvijas Republikas Proklamēšanas diena','substitute'=>TRUE],
 ['date'=>'12-24','name'=>'Christmas Eve','local'=>'Ziemassvētku vakars'],
 ['date'=>'12-25','name'=>'Christmas Day','local'=>'Pirmie Ziemassvētki'],
 ['date'=>'12-26','name'=>'Second Day of Christmas','local'=>'Otrie Ziemassvētki'],
 ['date'=>'12-31','name'=>"New Year's Eve",'local'=>'Vecgada diena'],
]];
