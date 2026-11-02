<?php

  // bootstrap - Requires explicit seed, resamples (1..10000) and interval=percentile. Estimator is mean or median; confidence defaults .95. IID observations are resampled with replacement using a local deterministic SHA-256 counter stream and rejection to remove index modulo bias. No global RNG state changes. Type-7 quantiles form percentile bounds. maxWork defaults 2000000 and cannot exceed 10000000; sample/median sorting work and random draws are bounded. Does not claim automatic significance or support arbitrary callbacks.
  //
  // Runnable example: `apps/tags/examples/stats/bootstrap.pad`. Independent fixture: `php tests/subsystems/expansion-statistics-bootstrap.php`.

return ['options'=>['field','missing','seed','resamples','interval','estimator','confidence','maxWork'],'call'=>function($rows,$options){
 foreach(['seed','resamples','interval'] as $required)if(!array_key_exists($required,$options))throw new InvalidArgumentException('bootstrap requires seed, resamples and interval');$seed=$options['seed'];$b=$options['resamples'];$est=$options['estimator']??'mean';$confidence=$options['confidence']??.95;$max=$options['maxWork']??2000000;
 if((!is_string($seed)&&!is_int($seed))||strlen((string)$seed)>256||!is_int($b)||$b<1||$b>10000||$options['interval']!=='percentile'||!in_array($est,['mean','median'],TRUE)||(!is_int($confidence)&&!is_float($confidence))||!is_finite((float)$confidence)||$confidence<=0||$confidence>=1||!is_int($max)||$max<1||$max>10000000)throw new InvalidArgumentException('invalid bootstrap parameters');
 [$values,,$meta]=padStatsRows($rows,$options);$n=count($values);if(!$n)throw new InvalidArgumentException('bootstrap needs a nonempty sample');$cost=$n*($est==='median'?max(1,(int)ceil(log($n,2))):1);if($cost*$b>$max)throw new InvalidArgumentException('bootstrap work budget exceeded');
 $estimate=function($v)use($est){if($est==='mean')return padStatsCenter($v)[0];sort($v,SORT_NUMERIC);return padStatsQuantile($v,.5);};$out=[];$counter=0;$draws=0;$bound=intdiv(4294967296,$n)*$n;
 for($i=0;$i<$b;$i++){$sample=[];for($j=0;$j<$n;$j++){do{if(++$draws>$max)throw new InvalidArgumentException('bootstrap random-work budget exceeded');$u=unpack('N',substr(hash('sha256',serialize($seed).':'.$counter++,TRUE),0,4))[1];}while($u>=$bound);$sample[]=$values[$u%$n];}$out[]=$estimate($sample);}sort($out,SORT_NUMERIC);$alpha=(1-$confidence)/2;
 return $meta+['estimator'=>$est,'estimate'=>$estimate($values),'resamples'=>$b,'interval'=>'percentile','confidence'=>$confidence,'lower'=>padStatsQuantile($out,$alpha),'upper'=>padStatsQuantile($out,1-$alpha),'seed'=>$seed,'work'=>$cost*$b,'randomDraws'=>$draws,'assumption'=>'independent identically distributed observations; empirical resampling with replacement'];
}];
