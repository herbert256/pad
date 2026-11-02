<?php
return ['options'=>['directed'],'call'=>function($g,$options){
 if(!$g['directed'])throw new InvalidArgumentException('strongcomponents requires directed=true');
 $seen=[];$order=[];$visit=function($v)use(&$visit,&$seen,&$order,$g){$seen[$v]=TRUE;foreach($g['adj'][$v] as [$w])if(!isset($seen[$w]))$visit($w);$order[]=$v;};foreach($g['ids'] as $v=>$id)if(!isset($seen[$v]))$visit($v);
 $seen=[];$groups=[];foreach(array_reverse($order) as $v){if(isset($seen[$v]))continue;$queue=[$v];$seen[$v]=TRUE;for($head=0;$head<count($queue);$head++)foreach($g['reverse'][$queue[$head]] as [$w])if(!isset($seen[$w])){$seen[$w]=TRUE;$queue[]=$w;}sort($queue,SORT_NUMERIC);$groups[]=$queue;}
 usort($groups,fn($a,$b)=>$a[0]<=>$b[0]);$owner=[];$components=[];foreach($groups as $i=>$group){foreach($group as $v)$owner[$v]=$i;$components[]=array_map(fn($v)=>$g['ids'][$v],$group);}
 $edges=[];foreach($g['normalized'] as [$a,$b])if($owner[$a]!==$owner[$b])$edges[$owner[$a].':'.$owner[$b]]=[$owner[$a],$owner[$b]];$edges=array_values($edges);usort($edges,fn($a,$b)=>($a[0]<=>$b[0])?:($a[1]<=>$b[1]));
 return ['status'=>'ok','method'=>'Kosaraju strongly connected components','components'=>$components,'condensation'=>['nodes'=>array_keys($groups),'edges'=>$edges]];
}];
