<?php
include_once PAD . 'subs/calendars/_lib/timegrid.php';
include_once PAD . 'subs/intervals/_lib/intervals.php';
// Half-open intervals are compared as instants, preserving both occurrences of a DST fold.
function padCalendarAvailability ( array $windows, array $busy, $timezone = 'UTC', $minimum = 30 ) {
 if (!in_array($timezone,DateTimeZone::listIdentifiers(DateTimeZone::ALL_WITH_BC),TRUE)) throw new InvalidArgumentException('Availability needs an IANA timezone');
 if (!is_int($minimum)||$minimum<1||$minimum>10080) throw new InvalidArgumentException('Minimum slot is 1 to 10080 whole minutes');
 $zone=new DateTimeZone($timezone);$windows=padCalendarIntervals($windows,$zone);$busy=padCalendarIntervals($busy,$zone);$groups=[];$out=[];
 foreach($windows as $w)$groups[$w['resource']][]=[$w['start'],$w['end']];
 foreach($groups as $resource=>$ranges){
  $merged=padIntervalUnionRanges($ranges);
  $occupied=array_values(array_filter($busy,fn($e)=>$e['resource']===(string)$resource));usort($occupied,fn($a,$b)=>$a['start']<=>$b['start']);
  $asRows=fn($ranges)=>array_map(fn($r)=>['start'=>$r[0],'end'=>$r[1]],$ranges);
  $taken=array_map(fn($e)=>[$e['start'],$e['end']],$occupied);
  $free=padIntervalCompute('subtract',$asRows($merged),$asRows($taken),[]);
   foreach($free as $interval){$start=$interval['start'];$end=$interval['end'];if($end-$start >= $minimum*60){
    $stamp=fn($s)=>(new DateTimeImmutable('@'.$s))->setTimezone($zone)->format('Y-m-d\TH:i:sP');
    $out[]=['resource'=>(string)$resource,'start'=>$stamp($start),'end'=>$stamp($end),'minutes'=>($end-$start)/60];
   }}
 }
 return $out;
}
?>
