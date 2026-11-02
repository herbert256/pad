<?php

  // knapsack - Exact 0/1 integer knapsack accepts items `{id,weight,value}` and integer capacity. IDs are unique strings. Weights/values are nonnegative integers; zero weight is permitted. Capacity ≤10000, items ≤1000, DP cells ≤250000; values are bounded to prevent integer overflow. Returns chosen IDs, objective value, weight and solved/limit/cancelled status. A stopped run never claims infeasibility; ties prefer earlier items.
  //
  // Runnable example: `apps/tags/examples/solve/knapsack.pad`. Independent fixture: `php tests/subsystems/expansion-constraints-knapsack.php`.

return ['options'=>['maxSteps','cancel'],'call'=>function($problem,$options,&$state){
 padSolveProblemKeys($problem,['items','capacity']);$items=$problem['items'];$capacity=$problem['capacity'];
 if(!is_array($items)||!array_is_list($items)||count($items)>1000||!is_int($capacity)||$capacity<0||$capacity>10000||(count($items)+1)*($capacity+1)>250000)throw new InvalidArgumentException('knapsack requires at most 1000 items, capacity 0..10000 and 250000 DP cells');
 $ids=[];foreach($items as $item){if(!is_array($item))throw new InvalidArgumentException('invalid item');padSolveProblemKeys($item,['id','weight','value']);$ids[]=$item['id'];if(!is_int($item['weight'])||$item['weight']<0||$item['weight']>10000||!is_int($item['value'])||$item['value']<0||$item['value']>intdiv(PHP_INT_MAX,max(1,count($items))))throw new InvalidArgumentException('weights and values must be bounded nonnegative integers');}
 padSolveIds($ids,1000,'items');$dp=[array_fill(0,$capacity+1,0)];
 foreach($items as $i=>$item){$row=$dp[$i];for($c=0;$c<=$capacity;$c++){padSolveTick($state);if($item['weight']<=$c)$row[$c]=max($row[$c],$dp[$i][$c-$item['weight']]+$item['value']);}$dp[]=$row;}
 $chosen=[];$weight=0;$c=$capacity;for($i=count($items)-1;$i>=0;$i--)if($dp[$i+1][$c]>$dp[$i][$c]){$chosen[]=$items[$i]['id'];$weight+=$items[$i]['weight'];$c-=$items[$i]['weight'];}
 return ['status'=>'solved','solution'=>array_reverse($chosen),'value'=>$dp[count($items)][$capacity],'weight'=>$weight,'method'=>'exact 0/1 integer dynamic programming; equal objectives prefer earlier items'];
}];
