<?php

  // coloring - Fixed color budget 1..32; ≤256 nodes and graph validation reused with undirected unit edges. Colors are zero-based. Degree-first deterministic backtracking returns solved/infeasible/limit/cancelled, and never seeks a minimum chromatic number. Self-loops are infeasible. maxSteps and cancellation charge each search node and neighbor constraint.
  //
  // Runnable example: `apps/tags/examples/solve/coloring.pad`. Independent fixture: `php tests/subsystems/expansion-constraints-coloring.php`.

include_once PAD.'subs/graphs/_lib/graphs.php';
return ['options'=>['maxSteps','cancel'],'call'=>function($problem,$options,&$state){
 padSolveProblemKeys($problem,['nodes','edges','colors']);$k=$problem['colors'];if(!is_int($k)||$k<1||$k>32)throw new InvalidArgumentException('colors must be an integer 1..32');
 $g=padGraphNormalize($problem['nodes'],$problem['edges'],['directed'=>FALSE],'coloring');$n=count($g['ids']);if($n>256)throw new InvalidArgumentException('coloring permits at most 256 nodes');
 foreach($g['normalized'] as [$a,$b])if($a===$b)return ['status'=>'infeasible','solution'=>NULL];
 $order=range(0,max(0,$n-1));if(!$n)$order=[];usort($order,fn($a,$b)=>(count($g['adj'][$b])<=>count($g['adj'][$a]))?:($a<=>$b));$colors=array_fill(0,$n,-1);
 $search=function($at)use(&$search,&$colors,&$state,$order,$g,$k,$n){padSolveTick($state);if($at===$n)return TRUE;$node=$order[$at];$blocked=[];foreach($g['adj'][$node] as [$next]){padSolveTick($state);if($colors[$next]>=0)$blocked[$colors[$next]]=TRUE;}for($c=0;$c<$k;$c++)if(!isset($blocked[$c])){$colors[$node]=$c;if($search($at+1))return TRUE;}$colors[$node]=-1;return FALSE;};
 if(!$search(0))return ['status'=>'infeasible','solution'=>NULL];$solution=[];foreach($g['ids'] as $i=>$id)$solution[]=['id'=>$id,'color'=>$colors[$i]];return ['status'=>'solved','solution'=>$solution,'colors'=>$k,'method'=>'bounded fixed-budget coloring; degree-first backtracking'];
}];
