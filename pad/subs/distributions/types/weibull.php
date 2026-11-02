<?php
return ['parameters'=>['shape'=>[1,.01,10000,FALSE],'scale'=>[1,1e-150,1e150,FALSE]],'support'=>'nonnegative real','work'=>fn($p)=>1,'sample'=>fn($p,$random)=>$p['scale']*(-log(1-$random()))**(1/$p['shape'])];
