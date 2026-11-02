<?php
include_once PAD.'subs/calendars/_lib/timegrid.php';
function padTimelineAudit($rows,$timezone='UTC',$title='Audit history'){
 if(!is_array($rows)||count($rows)>2000||!in_array($timezone,DateTimeZone::listIdentifiers(DateTimeZone::ALL_WITH_BC),TRUE))throw new InvalidArgumentException('audit supports 2000 records and an IANA timezone');$events=[];$zone=new DateTimeZone($timezone);$esc=fn($s)=>htmlspecialchars((string)$s,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');
 foreach(array_values($rows) as $i=>$r){if(!is_array($r))throw new InvalidArgumentException('audit record required');$at=padCalendarInstant($r['timestamp']??NULL);foreach(['actor','action','target'] as $key)if(!isset($r[$key])||!is_scalar($r[$key]))throw new InvalidArgumentException('audit needs timestamp actor action target');$events[]=['at'=>$at,'row'=>$r,'order'=>$i];}
 usort($events,fn($a,$b)=>($a['at']<=>$b['at'])?:($a['order']<=>$b['order']));$html='<section class="pad-timeline-audit"><h2>'.$esc($title).'</h2><p>Timezone: '.$esc($timezone).'</p><ol>';
 foreach($events as $event){$r=$event['row'];$at=$event['at']->setTimezone($zone);$html.='<li><time datetime="'.$esc($at->format(DATE_ATOM)).'">'.$esc($at->format('Y-m-d H:i:s P')).'</time> <strong>'.$esc($r['actor']).'</strong> '.$esc($r['action']).' <span>'.$esc($r['target']).'</span>';if(isset($r['detail'])&&is_scalar($r['detail']))$html.='<details><summary>Details</summary><p>'.$esc($r['detail']).'</p></details>';$html.='</li>';}
 return $html.'</ol>'.(!$events?'<p>No history.</p>':'').'</section>';
}
