<?php
function padCalendarFiscal($periods,$from=NULL,$to=NULL){
 if(!is_array($periods)||!$periods||count($periods)>1000)throw new InvalidArgumentException('fiscal requires 1..1000 explicit periods');$out=[];
 $date=function($v){if(!is_string($v)||!preg_match('/^\d{4}-\d{2}-\d{2}$/D',$v))throw new InvalidArgumentException('fiscal boundaries must be YYYY-MM-DD');$d=DateTimeImmutable::createFromFormat('!Y-m-d',$v,new DateTimeZone('UTC'));if(!$d||$d->format('Y-m-d')!==$v||substr($v,0,4)==='0000')throw new InvalidArgumentException('invalid fiscal date');return $v;};
 foreach($periods as $row){if(!is_array($row)||!is_scalar($row['label']??NULL)||trim((string)$row['label'])==='')throw new InvalidArgumentException('fiscal period needs label');$a=$date($row['start']??NULL);$b=$date($row['end']??NULL);if($a>=$b)throw new InvalidArgumentException('fiscal end is exclusive and must follow start');$out[]=['label'=>(string)$row['label'],'start'=>$a,'end'=>$b];}
 usort($out,fn($a,$b)=>$a['start']<=>$b['start']);foreach($out as $i=>$row)if($i&&$out[$i-1]['end']!==$row['start'])throw new InvalidArgumentException('fiscal periods must cover without overlap or gaps');
 if($from!==NULL&&$date($from)!==$out[0]['start']||$to!==NULL&&$date($to)!==$out[count($out)-1]['end'])throw new InvalidArgumentException('fiscal coverage does not match from/to');return $out;
}
