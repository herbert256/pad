<?php
$date=padPrintableText('date',gmdate('Y-m-d'));
$start=DateTimeImmutable::createFromFormat('!Y-m-d',$date,new DateTimeZone('UTC'));
if(!$start||$start->format('Y-m-d')!==$date){padError('planner date must be YYYY-MM-DD');return [];}
$sunday=padPrintableChoice('weekstart','monday',['monday','sunday'])==='sunday';
$start=$start->modify('-'.($sunday?(int)$start->format('w'):(int)$start->format('N')-1).' days');
$first=padPrintableInt('from',8,0,23);$last=padPrintableInt('to',18,$first+1,24);
$slot=padPrintableChoice('slot','60',['30','60']);$weeks=padPrintableInt('weeks',1,1,52);
$holidays=padTagParm('holidays',[]);if(!is_array($holidays)){$holidays=[];padError('planner holidays must map YYYY-MM-DD to labels');}
[$name,$paper]=padPrintablePaper(['a4'=>PAD_PRINTABLE_PAPERS['a4'],'letter'=>PAD_PRINTABLE_PAPERS['letter']],'a4');
return padPrintablePlan(['name'=>['week','weeks'],'paper'=>$paper,'paperName'=>$name,'count'=>$weeks,
'item'=>function($cell,$parts,$n)use($paper,$start,$first,$last,$slot,$holidays){
 [$w,$h]=$paper;$week=$start->modify('+'.$n.' weeks');$left=24;$top=33;$bottom=$parts&&trim($parts[''])!==''?45:12;$cw=($w-$left-10)/7;$rows=($last-$first)*60/(int)$slot;$rh=($h-$top-$bottom)/$rows;
 $draw=[['box',10,8,$w-20,12,'<b>Week of '.$week->format('j F Y').'</b>',['size'=>18,'pad'=>0]]];
 for($d=0;$d<7;$d++){$day=$week->modify('+'.$d.' days');$holiday=$holidays[$day->format('Y-m-d')]??'';
 $draw[]=['box',$left+$d*$cw,21,$cw,12,$day->format('D j').($holiday!==''?'<br>'.padWidgetAttr($holiday):''),['size'=>8,'pad'=>1,'align'=>'center']];
 $draw[]=['line',$left+$d*$cw,$top,$left+$d*$cw,$h-$bottom,['stroke'=>'#888888','width'=>.3]];
 }
 $draw[]=['line',$w-10,$top,$w-10,$h-$bottom,['stroke'=>'#888888','width'=>.3]];
 for($r=0;$r<=$rows;$r++){$y=$top+$r*$rh;$minute=$first*60+$r*(int)$slot;
 $draw[]=['line',$left,$y,$w-10,$y,['stroke'=>'#aaaaaa','width'=>.25]];
 if($r<$rows)$draw[]=['box',10,$y,13,$rh,sprintf('%02d:%02d',intdiv($minute,60),$minute%60),['size'=>7,'pad'=>0]];
 }
 if($parts&&trim($parts[''])!=='')$draw[]=['box',10,$h-40,$w-20,30,$parts[''],['size'=>10,'pad'=>2]];
 return $draw;
}]);
?>
