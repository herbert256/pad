<?php

  // spanningtree - Minimum spanning forest using Kruskal. Defaults to undirected; directed=true is rejected. Finite nonnegative weights are supported, self-loops ignored, parallel edges rejected by normal graph validation. Equal weights use original node-order endpoint ties. Disconnected components and connected flag are explicit. Weight overflow fails.
  //
  // Runnable example: `apps/tags/examples/graph/spanningtree.pad`. Independent fixture: `php tests/subsystems/expansion-graphs-spanningtree.php`.

return ['options'=>['directed'],'call'=>function($g,$options){
 if($g['directed'])throw new InvalidArgumentException('spanningtree requires directed=false');$parent=array_keys($g['ids']);$find=function($v)use(&$find,&$parent){return $parent[$v]===$v?$v:($parent[$v]=$find($parent[$v]));};$edges=[];
 foreach($g['adj'] as $a=>$neighbors)foreach($neighbors as [$b,$weight])if($a<$b)$edges[]=[$a,$b,$weight];usort($edges,fn($a,$b)=>($a[2]<=>$b[2])?:($a[0]<=>$b[0])?:($a[1]<=>$b[1]));
 $chosen=[];$weight=0.0;foreach($edges as [$a,$b,$w]){ $ra=$find($a);$rb=$find($b);if($ra===$rb)continue;$parent[$rb]=$ra;$weight+=$w;if(!is_finite($weight))throw new InvalidArgumentException('forest weight exceeds finite range');$chosen[]=['from'=>$g['ids'][$a],'to'=>$g['ids'][$b],'weight'=>$w];}
 $groups=[];foreach($g['ids'] as $i=>$id)$groups[$find($i)][]=$id;
 return ['status'=>'ok','connected'=>count($groups)<=1,'components'=>array_values($groups),'edges'=>$chosen,'weight'=>$weight,'method'=>'Kruskal minimum spanning forest; stable node-order ties'];
}];
