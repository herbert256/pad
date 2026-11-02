<?php
return ['options'=>['method'],'call'=>function($input,$options,&$state){
 if(($options['method']??'trapezoid')!=='trapezoid')throw new InvalidArgumentException('integration method is trapezoid');$rows=padNumericSamples($input,$state);$sum=0.0;$c=0.0;for($i=1;$i<count($rows);$i++){padNumericTick($state);$width=padNumericFinite($rows[$i][0]-$rows[$i-1][0],'interval width');$area=padNumericFinite(($rows[$i][1]/2+$rows[$i-1][1]/2)*$width,'trapezoid area');$term=$area-$c;$next=padNumericFinite($sum+$term,'integral');$c=($next-$sum)-$term;$sum=$next;}
 return ['status'=>'ok','method'=>'sampled trapezoid','value'=>$sum,'iterations'=>count($rows)-1,'domain'=>[$rows[0][0],$rows[count($rows)-1][0]],'tolerance'=>NULL,'errorEstimate'=>NULL];
}];
