<?php
require __DIR__.'/../../pad/subs/calendars/_lib/timegrid.php';
function check($ok,$why){if(!$ok)throw new Exception($why);}
$tz=new DateTimeZone('Europe/Amsterdam');
foreach(['2026-03-29'=>23,'2026-10-25'=>25] as $day=>$hours){$a=padCalendarCivil($day,$tz);check(($a->modify('+1 day')->getTimestamp()-$a->getTimestamp())/3600===$hours,'DST day length');}
$rows=[['start'=>'2026-10-25T02:00:00+02:00','end'=>'2026-10-25T02:00:00+01:00'],['start'=>'2026-10-25T02:30:00+02:00','end'=>'2026-10-25T03:30:00+01:00']];
$events=padCalendarIntervals($rows,$tz);check($events[0]['end']-$events[0]['start']===3600,'Repeated hour');$a=padCalendarCivil('2026-10-25',$tz);$lanes=padCalendarLanes($events,$a->getTimestamp(),$a->modify('+1 day')->getTimestamp());check(array_column($lanes,'lane')===[0,1]&&array_column($lanes,'lanes')===[2,2],'Overlap lanes');
$span=padCalendarIntervals([['start'=>'2026-11-01T23:00:00+01:00','end'=>'2026-11-02T01:00:00+01:00']],$tz);$a=padCalendarCivil('2026-11-02',$tz);$clipped=padCalendarLanes($span,$a->getTimestamp(),$a->modify('+1 day')->getTimestamp());check($clipped[0]['b']-$clipped[0]['a']===3600,'Midnight clipping');
foreach(['2026-02-30T12:00:00Z','2026-11-02T25:00:00Z','2026-11-02T09:00:00','2026-11-02T09:00:00+99:00'] as $bad){try{padCalendarInstant($bad);throw new Exception('Accepted '.$bad);}catch(InvalidArgumentException $e){}}
echo "timegrid: DST, offsets, overlaps, midnight clipping and invalid input passed\n";
