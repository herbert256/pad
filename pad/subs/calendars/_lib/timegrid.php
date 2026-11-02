<?php
// Timed events are absolute instants with explicit offsets; all-day dates have exclusive ends.
function padCalendarInstant($value){
 if(!is_string($value)||!preg_match('/^(\d{4}-\d{2}-\d{2})T(\d{2}:\d{2})(?::(\d{2}))?(Z|[+-]\d{2}:\d{2})$/D',$value,$m))throw new InvalidArgumentException('Timed events need an ISO timestamp with an explicit offset');
 $offset=$m[4]==='Z'?'+00:00':$m[4];
 if((int)substr($offset,1,2)>14||((int)substr($offset,1,2)===14&&(int)substr($offset,4,2)!==0))throw new InvalidArgumentException('Invalid timestamp offset');
 $canonical=$m[1].'T'.$m[2].':'.($m[3]!==''?$m[3]:'00').$offset;
 $date=DateTimeImmutable::createFromFormat('!Y-m-d\TH:i:sP',$canonical);
 if(!$date||$date->format('Y-m-d\TH:i:sP')!==$canonical)throw new InvalidArgumentException('Invalid event timestamp');
 return $date;
}
function padCalendarCivil($value,$zone){
 if(!is_string($value))throw new InvalidArgumentException('Calendar date must be YYYY-MM-DD');
 $date=DateTimeImmutable::createFromFormat('!Y-m-d',$value,$zone);
 if(!$date||$date->format('Y-m-d')!==$value)throw new InvalidArgumentException('Calendar date must be YYYY-MM-DD');return $date;
}
function padCalendarIntervals($rows,$zone){
 if(count($rows)>2000)throw new InvalidArgumentException('Time grid supports at most 2000 events');$out=[];
 foreach($rows as $i=>$row){
  if(!is_array($row))throw new InvalidArgumentException('Calendar event must be a record');$all=($row['allDay']??FALSE)===TRUE;
  if($all){$start=padCalendarCivil($row['start']??NULL,$zone);$end=padCalendarCivil($row['end']??NULL,$zone);}else{$start=padCalendarInstant($row['start']??NULL);$end=padCalendarInstant($row['end']??NULL);}
  if($end<=$start)throw new InvalidArgumentException('Event end must be after start');
  foreach(['title','resource','link'] as $field)if(isset($row[$field])&&!is_scalar($row[$field]))throw new InvalidArgumentException('Calendar text fields must be scalar');
  $out[]=['start'=>$start->getTimestamp(),'end'=>$end->getTimestamp(),'allDay'=>$all,'title'=>(string)($row['title']??'Event'),'resource'=>(string)($row['resource']??''),'link'=>(string)($row['link']??''),'index'=>$i];
 }
 return $out;
}
// Interval coloring; connected overlap clusters share their final lane count.
function padCalendarLanes($events,$from,$to){
 $list=[];
 foreach($events as $event)if(!$event['allDay']&&$event['start']<$to&&$event['end']>$from){$event['a']=max($from,$event['start']);$event['b']=min($to,$event['end']);$list[]=$event;}
 usort($list,fn($a,$b)=>[$a['a'],$a['b'],$a['index']]<=>[$b['a'],$b['b'],$b['index']]);
 $end=[];$cluster=[];$out=[];$flush=function()use(&$cluster,&$out,&$end){$width=count($end);foreach($cluster as $e){$e['lanes']=$width;$out[]=$e;}$cluster=[];$end=[];};
 foreach($list as $e){if($end&&min($end)<=$e['a']&&max($end)<=$e['a'])$flush();$lane=0;while(isset($end[$lane])&&$end[$lane]>$e['a'])$lane++;$end[$lane]=$e['b'];$e['lane']=$lane;$cluster[]=$e;}
 if($cluster)$flush();return $out;
}
function padCalendarTimeColumns($events,$columns,$zone,$label){
 $id=padWidgetId('timegrid',[$label,count($events)]);$css='';$html='<section class="pad-timegrid" id="'.$id.'" aria-label="'.padCalendarEscape($label).'"><h2>'.padCalendarEscape($label).'</h2><p>Timezone: '.padCalendarEscape($zone->getName()).'</p><div class="pct-columns">';
 $stamp=fn($n)=>(new DateTimeImmutable('@'.$n))->setTimezone($zone);
 foreach($columns as $c=>$column){$day=$column['day'];$from=$day->getTimestamp();$to=$day->modify('+1 day')->getTimestamp();$set=$column['events'];$html.='<article class="pct-column"><h3>'.padCalendarEscape($column['label']).'</h3><ul class="pct-allday" aria-label="All-day events">';
  foreach($set as $e)if($e['allDay']&&$e['start']<$to&&$e['end']>$from)$html.='<li>'.padCalendarEscape($e['title']).' <span>(all day)</span></li>';
  $html.='</ul><div class="pct-axis"><ol class="pct-ticks" aria-hidden="true">';
  for($t=$from,$n=0;$t<$to;$t+=3600,$n++){$class='pct-t'.$c.'-'.$n;$css.='#'.$id.' .'.$class.'{top:'.round(($t-$from)/($to-$from)*100,5).'%;}';$html.='<li class="'.$class.'">'.$stamp($t)->format('H:i P').'</li>';}
  $html.='</ol><ol class="pct-events" aria-label="Timed events">';
  foreach(padCalendarLanes($set,$from,$to) as $n=>$e){$class='pct-e'.$c.'-'.$n;$left=$e['lane']/$e['lanes']*100;$width=100/$e['lanes'];$css.='#'.$id.' .'.$class.'{top:'.round(($e['a']-$from)/($to-$from)*100,5).'%;height:'.round(($e['b']-$e['a'])/($to-$from)*100,5).'%;left:'.round($left,5).'%;width:'.round($width,5).'%;}';
   $title=padCalendarEscape($e['title']);$link=padWidgetLink($e['link']);$description=$stamp($e['start'])->format('Y-m-d H:i P').' – '.$stamp($e['end'])->format('Y-m-d H:i P');
   $html.='<li class="pct-event '.$class.'" tabindex="0" aria-label="'.padCalendarEscape($e['title'].': '.$description).'"><strong>'.($link!==''?'<a href="'.$link.'">'.$title.'</a>':$title).'</strong><span class="pct-when">'.padCalendarEscape($description).'</span></li>';
  }
  $html.='</ol></div></article>';
 }
 return padWidgetSheet(padWidgetFile('calendars/timegrid.css','raw')).padProtect('<style'.padStyleNonce().'>'.$css.'</style>').$html.'</div></section>';
}
function padCalendarTimegridTag($resources=NULL){
 global $pad,$padData,$padParm;
 try{
  $zone=new DateTimeZone((string)padTagParm('timezone','UTC'));
  $date=trim((string)$padParm);if($date==='')$date=padNow('Y-m-d');$first=padCalendarCivil($date,$zone);
  $days=(int)padTagParm('days',$resources===NULL?7:1);if(!in_array($days,[1,7],TRUE))throw new InvalidArgumentException('Time grid days must be 1 or 7');
  $sunday=(bool)padTagParm('sunday',FALSE);if($days===7)$first=$first->modify('-'.($sunday?(int)$first->format('w'):(int)$first->format('N')-1).' days');
  $rows=padChartRows('','calendar');$events=padCalendarIntervals((array)$rows,$zone);$columns=[];
  if($resources!==NULL){if(!is_array($resources)||count($resources)<1||count($resources)>20)throw new InvalidArgumentException('Resources must map 1 to 20 IDs to labels');foreach($resources as $key=>$text)if(!is_scalar($text))throw new InvalidArgumentException('Resource labels must be scalar');foreach($events as $e)if(!array_key_exists($e['resource'],$resources))throw new InvalidArgumentException('Event names an unknown resource');}
  for($i=0;$i<$days;$i++){$day=$first->modify('+'.$i.' days');if($resources===NULL)$columns[]=['day'=>$day,'label'=>$day->format('D j M Y'),'events'=>$events];else foreach($resources as $key=>$name)$columns[]=['day'=>$day,'label'=>$name.' · '.$day->format('D j M'),'events'=>array_values(array_filter($events,fn($e)=>$e['resource']===(string)$key))];}
  $padData[$pad]=padDefaultData();return padCalendarTimeColumns($events,$columns,$zone,(string)padTagParm('label',$resources===NULL?'Time grid':'Resource planning'));
 }catch(Exception $e){padError($e->getMessage());return '';}
}
?>
