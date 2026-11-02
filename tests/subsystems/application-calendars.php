<?php
 define('PAD',dirname(__DIR__,2).'/pad/');function padDirList($p){return scandir($p);}require PAD.'lib/subsystem.php';require PAD.'lib/recurrence.php';require PAD.'subs/calendars/_lib/availability.php';
 function ck($ok,$why){if(!$ok)throw new Exception($why);}
 $slots=padCalendarAvailability([['resource'=>'a','start'=>'2026-03-29T01:00:00+01:00','end'=>'2026-03-29T04:00:00+02:00']],[['resource'=>'a','start'=>'2026-03-29T03:00:00+02:00','end'=>'2026-03-29T03:30:00+02:00']],'Europe/Amsterdam',30);
 ck(array_column($slots,'minutes')===[60,30],'DST gap uses elapsed instants');
 $slots=padCalendarAvailability([['start'=>'2026-10-25T02:00:00+02:00','end'=>'2026-10-25T02:30:00+01:00']],[],'Europe/Amsterdam',30);ck($slots[0]['minutes']===90,'DST fold');
 $w=[['resource'=>'r','start'=>'2026-11-02T22:00:00Z','end'=>'2026-11-03T02:00:00Z'],['resource'=>'r','start'=>'2026-11-03T02:00:00Z','end'=>'2026-11-03T03:00:00Z']];
 $b=[['resource'=>'r','start'=>'2026-11-02T23:00:00Z','end'=>'2026-11-03T01:00:00Z'],['resource'=>'r','start'=>'2026-11-03T00:00:00Z','end'=>'2026-11-03T02:00:00Z']];ck(array_column(padCalendarAvailability($w,$b),'minutes')===[60,60],'overlap merging and cross-midnight');ck(padCalendarAvailability([],[])===[],'closed');
 ck(padOccurrences('businessday',['start'=>'2026-12-24','count'=>3,'holidays'=>['2026-12-25']],'2026-12-01','2027-01-01')===['2026-12-24','2026-12-28','2026-12-29'],'holiday business count');
 ck(padOccurrences('businessday',['start'=>'2026-11-01','interval'=>2,'count'=>3,'exclude'=>['2026-11-04']],'2026-11-01','2026-12-01')===['2026-11-02','2026-11-05','2026-11-09'],'accepted-day interval and exclusions');
 ck(padOccurrences('businessday',['start'=>'2026-11-06','weekend'=>['FR','SA'],'count'=>2],'2026-11-01','2026-12-01')===['2026-11-08','2026-11-09'],'alternate weekend');
 ck(padOccurrences('rrule',['start'=>'2026-01-30','rrule'=>'FREQ=MONTHLY;BYDAY=-1FR;COUNT=3'],'2026-01-01','2026-05-01')===['2026-01-30','2026-02-27','2026-03-27'],'last Friday');
 ck(padOccurrences('rrule',['start'=>'2026-01-12','rrule'=>'FREQ=MONTHLY;BYDAY=MO;BYSETPOS=2;COUNT=3'],'2026-01-01','2026-05-01')===['2026-01-12','2026-02-09','2026-03-09'],'second Monday');
 ck(padOccurrences('rrule',['start'=>'2026-01-30','rrule'=>'FREQ=YEARLY;BYMONTH=1;BYDAY=MO,TU,WE,TH,FR;BYSETPOS=-1;COUNT=2'],'2026-01-01','2028-01-01')===['2026-01-30','2027-01-29'],'yearly selected month');
 foreach(['FREQ=MONTHLY;BYSETPOS=1','FREQ=MONTHLY;BYDAY=MO;BYMONTHDAY=1','FREQ=YEARLY;BYDAY=-1FR','FREQ=WEEKLY;BYDAY=2MO','FREQ=YEARLY;BYMONTH=1,2;BYDAY=FR;BYSETPOS=-1'] as $rrule){try{padOccurrences('rrule',['start'=>'2026-01-01','rrule'=>$rrule],'2026-01-01','2027-01-01');throw new Exception('Expected rejection');}catch(InvalidArgumentException $e){}}
 echo "Application calendars: intervals, DST gap/fold, closure, business counting, ordinals and set positions passed\n";
