<?php
include_once PAD . 'subs/calendars/_lib/timegrid.php';
// Half-open intervals are compared as instants, preserving both occurrences of a DST fold.
function padCalendarAvailability ( array $windows, array $busy, $timezone = 'UTC', $minimum = 30 ) {
 if (!in_array($timezone,DateTimeZone::listIdentifiers(DateTimeZone::ALL_WITH_BC),TRUE)) throw new InvalidArgumentException('Availability needs an IANA timezone');
 if (!is_int($minimum)||$minimum<1||$minimum>10080) throw new InvalidArgumentException('Minimum slot is 1 to 10080 whole minutes');
 $zone=new DateTimeZone($timezone);$windows=padCalendarIntervals($windows,$zone);$busy=padCalendarIntervals($busy,$zone);$groups=[];$out=[];
 foreach($windows as $w)$groups[$w['resource']][]=[$w['start'],$w['end']];
 foreach($groups as $resource=>$ranges){
  sort($ranges);$merged=[];
  foreach($ranges as [$a,$b]){ $last=count($merged)-1;if($last>=0&&$merged[$last][1]>=$a)$merged[$last][1]=max($b,$merged[$last][1]);else $merged[]=[$a,$b]; }
  $occupied=array_values(array_filter($busy,fn($e)=>$e['resource']===(string)$resource));usort($occupied,fn($a,$b)=>$a['start']<=>$b['start']);
  foreach($merged as [$a,$b]){
   $cursor=$a;$free=[];
   foreach($occupied as $event){if($event['end']<=$cursor)continue;if($event['start']>=$b)break;if($event['start']>$cursor)$free[]=[$cursor,min($b,$event['start'])];$cursor=max($cursor,$event['end']);if($cursor>=$b)break;}
   if($cursor<$b)$free[]=[$cursor,$b];
   foreach($free as [$start,$end])if($end-$start >= $minimum*60){
    $stamp=fn($s)=>(new DateTimeImmutable('@'.$s))->setTimezone($zone)->format('Y-m-d\TH:i:sP');
    $out[]=['resource'=>(string)$resource,'start'=>$stamp($start),'end'=>$stamp($end),'minutes'=>($end-$start)/60];
   }
  }
 }
 return $out;
}
?>
