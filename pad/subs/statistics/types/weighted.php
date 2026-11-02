<?php

  // weighted - Weighted mean and sample variance with explicit semantics=frequency (integral counts) or reliability (nonnegative real weights). Defaults fields value/weight; paired missing=omit/error. Zero weights do not affect moments. Frequency denominator W−1; reliability denominator W−sum(w²)/W. Reports effectiveCount=W²/sum(w²), weightSum, positiveWeights and zeroWeights. Empty or zero-weight inputs yield null moments; insufficient denominator yields null variance. Uses shifted compensated accumulation with normalized weights.
  //
  // Runnable example: `apps/tags/examples/stats/weighted.pad`. Independent fixture: `php tests/subsystems/expansion-statistics-weighted.php`.

return ['options'=>['field','weight','missing','semantics'],'call'=>function($rows,$options){
 $sem=$options['semantics']??'frequency';if(!in_array($sem,['frequency','reliability'],TRUE))throw new InvalidArgumentException('semantics is frequency or reliability');
 [$pairs,,$meta]=padStatsRows($rows,['x'=>$options['field']??'value','y'=>$options['weight']??'weight','missing'=>$options['missing']??'omit'],TRUE);$values=[];$weights=[];$zero=0;
 foreach($pairs as [$value,$weight]){if($weight<0||($sem==='frequency'&&floor($weight)!=$weight))throw new InvalidArgumentException('weights must be nonnegative; frequency weights must be integral');if($weight==0){$zero++;continue;}$values[]=$value;$weights[]=$weight;}
 $sum=padStatsSum($weights);$result=$meta+['semantics'=>$sem,'zeroWeights'=>$zero,'positiveWeights'=>count($weights),'weightSum'=>$sum,'mean'=>NULL,'variance'=>NULL,'sd'=>NULL,'effectiveCount'=>0.0,'denominator'=>NULL];if(!$weights)return $result;
 $q=array_map(fn($w)=>$w/$sum,$weights);$origin=$values[0];$delta=array_map(fn($v)=>$v-$origin,$values);$shift=padStatsSum(array_map(fn($d,$q)=>$d*$q,$delta,$q));$mean=$origin+$shift;$d=array_map(fn($v)=>$v-$shift,$delta);$scale=max(array_map('abs',$d));$q2=padStatsSum(array_map(fn($v)=>$v*$v,$q));$fraction=$sem==='frequency'?1-1/$sum:1-$q2;$den=$sem==='frequency'?$sum-1:$sum*$fraction;$sd=$fraction<=0?NULL:($scale==0?0.0:sqrt(padStatsSum(array_map(fn($v,$q)=>($v/$scale)**2*$q,$d,$q))/$fraction)*$scale);
 return array_replace($result,['mean'=>$mean,'variance'=>$sd===NULL?NULL:$sd*$sd,'sd'=>$sd,'effectiveCount'=>1/$q2,'denominator'=>$den]);
}];
