<?php
include_once PAD.'subs/calendars/_lib/timegrid.php';
function padCalendarCapacity(array $windows,array $bookings,$timezone='UTC',$mode='load'){
 if(!in_array($timezone,DateTimeZone::listIdentifiers(DateTimeZone::ALL_WITH_BC),TRUE)||!in_array($mode,['load','count'],TRUE)||count($windows)+count($bookings)>2000)throw new InvalidArgumentException('capacity needs an IANA zone, load/count mode and at most 2000 intervals');
 $zone=new DateTimeZone($timezone);$supply=padCalendarIntervals($windows,$zone);$busy=padCalendarIntervals($bookings,$zone);$events=[];$ranges=[];
 foreach($supply as $w){$v=$windows[$w['index']]['capacity']??NULL;if(!is_numeric($v)||!is_finite((float)$v)||$v<0||$v>1000000000000)throw new InvalidArgumentException('Every capacity window needs a nonnegative finite capacity');$ranges[$w['resource']][]=[$w['start'],$w['end']];foreach([[$w['start'],1],[$w['end'],-1]] as [$t,$sign]){$events[$w['resource']][$t]['capacity']=($events[$w['resource']][$t]['capacity']??0)+$sign*(float)$v;}}
 foreach($ranges as $list){sort($list);$end=NULL;foreach($list as [$a,$b]){if($end!==NULL&&$a<$end)throw new InvalidArgumentException('Capacity windows must not overlap for one resource');$end=$b;}}
 foreach($busy as $b){$v=$mode==='count'?1:($bookings[$b['index']]['load']??NULL);if(!is_numeric($v)||!is_finite((float)$v)||$v<0||$v>1000000000000)throw new InvalidArgumentException('Every booking needs explicit nonnegative finite load');foreach([[$b['start'],1],[$b['end'],-1]] as [$t,$sign])$events[$b['resource']][$t]['used']=($events[$b['resource']][$t]['used']??0)+$sign*(float)$v;}
 $out=[];ksort($events);$stamp=fn($t)=>(new DateTimeImmutable('@'.$t))->setTimezone($zone)->format(DATE_ATOM);
 foreach($events as $resource=>$points){ksort($points,SORT_NUMERIC);$keys=array_keys($points);$used=0.;$capacity=0.;foreach($keys as $i=>$t){$used+=$points[$t]['used']??0;$capacity+=$points[$t]['capacity']??0;if(!isset($keys[$i+1]))break;$out[]=['resource'=>(string)$resource,'start'=>$stamp($t),'end'=>$stamp($keys[$i+1]),'used'=>max(0,$used),'capacity'=>max(0,$capacity),'remaining'=>max(0,$capacity-$used),'over'=>max(0,$used-$capacity)];}}
 return $out;
}
