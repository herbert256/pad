<?php

  // correlation - Pearson correlation (default) or Spearman with average ranks for ties. Uses paired missing=omit/error, x/y field selectors, native finite values and shifted compensated centering. Constants or fewer than two pairs return status=degenerate and correlation=null. No p-values or causal claims. At most 100000 pairs.
  //
  // Runnable example: `apps/tags/examples/stats/correlation.pad`. Independent fixture: `php tests/subsystems/expansion-statistics-correlation.php`.

return ['options'=>['x','y','missing','method'],'call'=>function($rows,$options){
 $method=$options['method']??'pearson';if(!in_array($method,['pearson','spearman'],TRUE))throw new InvalidArgumentException('method is pearson or spearman');
 [$pairs,,$meta]=padStatsRows($rows,$options,TRUE);$n=count($pairs);$x=array_column($pairs,0);$y=array_column($pairs,1);
 $rank=function($values){$order=array_keys($values);usort($order,fn($a,$b)=>($values[$a]<=>$values[$b])?:($a<=>$b));$r=[];for($i=0;$i<count($order);$i=$j){$j=$i+1;while($j<count($order)&&$values[$order[$j]]===$values[$order[$i]])$j++;for($k=$i;$k<$j;$k++)$r[$order[$k]]=($i+$j+1)/2;}ksort($r);return array_values($r);};
 if($method==='spearman'){$x=$rank($x);$y=$rank($y);}$r=NULL;if($n>1){[, $dx]=padStatsCenter($x);[, $dy]=padStatsCenter($y);$sx=max(array_map('abs',$dx));$sy=max(array_map('abs',$dy));if($sx>0&&$sy>0){$dx=array_map(fn($v)=>$v/$sx,$dx);$dy=array_map(fn($v)=>$v/$sy,$dy);$xx=padStatsSum(array_map(fn($v)=>$v*$v,$dx));$yy=padStatsSum(array_map(fn($v)=>$v*$v,$dy));$xy=padStatsSum(array_map(fn($a,$b)=>$a*$b,$dx,$dy));$r=max(-1.0,min(1.0,$xy/sqrt($xx*$yy)));}}
 return $meta+['method'=>$method,'status'=>$r===NULL?'degenerate':'ok','correlation'=>$r,'ties'=>$method==='spearman'?'average ranks':NULL];
}];
