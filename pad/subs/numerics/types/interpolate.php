<?php

  // interpolate - Piecewise linear interpolation of strictly increasing [x,y] samples. at is a native finite number or list. extrapolate is error (default), clamp, or linear. Binary-search alignment; no sorting/coercion. Returns x/y records and domain. No statistical uncertainty estimate.
  //
  // `padNumeric('interpolate',[[0,0],[2,4],[5,10]],['at'=>[1,3]])`

return ['options'=>['at','extrapolate','method'],'call'=>function($input,$options,&$state){
 $rows=padNumericSamples($input,$state);$at=$options['at']??NULL;$queries=is_array($at)?$at:[$at];$policy=$options['extrapolate']??'error';if(($options['method']??'linear')!=='linear'||!array_is_list($queries)||count($queries)>10000||!in_array($policy,['error','clamp','linear'],TRUE))throw new InvalidArgumentException('at is a number/list of at most 10000 points; extrapolate is error/clamp/linear');$last=count($rows)-1;$out=[];
 foreach($queries as $query){padNumericTick($state);$x=padNumericFinite($query,'query');if($x<$rows[0][0]||$x>$rows[$last][0]){if($policy==='error')throw new InvalidArgumentException('query outside sample domain');if($policy==='clamp'){$out[]=['x'=>$x,'y'=>$x<$rows[0][0]?$rows[0][1]:$rows[$last][1]];continue;}}$lo=0;$hi=$last;while($lo+1<$hi){padNumericTick($state);$mid=intdiv($lo+$hi,2);if($rows[$mid][0]<=$x)$lo=$mid;else $hi=$mid;}$span=padNumericFinite($rows[$hi][0]-$rows[$lo][0],'sample interval');$t=($x-$rows[$lo][0])/$span;$y=padNumericFinite((1-$t)*$rows[$lo][1]+$t*$rows[$hi][1],'interpolated result');$out[]=['x'=>$x,'y'=>$y];}
 return ['status'=>'ok','method'=>'piecewise linear','values'=>$out,'iterations'=>count($out),'extrapolate'=>$policy,'domain'=>[$rows[0][0],$rows[$last][0]],'tolerance'=>NULL];
}];
