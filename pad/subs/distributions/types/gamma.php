<?php
return ['parameters'=>['shape'=>[1,.01,10000,FALSE],'scale'=>[1,1e-150,1e150,FALSE]],'support'=>'nonnegative real (zero is possible after floating underflow)','work'=>fn($p)=>385,
'sample'=>function($p,$random){
 $shape=$p['shape'];$d=($shape<1?$shape+1:$shape)-1/3;$c=1/sqrt(9*$d);
 for($i=0;$i<128;$i++){
  $x=sqrt(-2*log(1-$random()))*cos(2*M_PI*$random());$v=1+$c*$x;if($v<=0)continue;$v=$v*$v*$v;
  if(log(1-$random())<.5*$x*$x+$d*(1-$v+log($v)))return exp(log($p['scale'])+log($d)+log($v)+($shape<1?log(1-$random())/$shape:0));
 }
 throw new UnexpectedValueException('gamma rejection limit exceeded');
}];
