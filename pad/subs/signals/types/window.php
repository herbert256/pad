<?php

  // window - Multiply samples by rectangular, Hann (default), Hamming or Blackman coefficients. Symmetric windows default to denominator N−1; periodic=true uses N. Length-one windows use coefficient 1. Returns coefficients, coherent gain and mean-square gain. sampleRate is optional metadata.
  //
  // `padSignal('window',[1,1,1,1,1],['window'=>'hann'])`

return ['options'=>['window','periodic','sampleRate'],'call'=>function($input,$options,&$state){$samples=padSignalSamples($input,$state);$name=$options['window']??'hann';$periodic=$options['periodic']??FALSE;$rate=padSignalRate($options);$weights=padSignalWindowCoefficients(count($samples),$name,$periodic,$state);$out=[];foreach($samples as $i=>$v){padSignalTick($state);$out[]=$v*$weights[$i];}return ['status'=>'ok','values'=>$out,'coefficients'=>$weights,'window'=>$name,'periodic'=>$periodic,'coherentGain'=>array_sum($weights)/count($weights),'meanSquareGain'=>array_sum(array_map(fn($w)=>$w*$w,$weights))/count($weights),'sampleRate'=>$rate];}];
