<?php

  // weibull - Inverse CDF scale × (-log(1-U))^(1/shape). Shape 0.01..10000; scale 1e-150..1e150. No shift parameter. Zero uniform yields zero. Underflow yields zero and overflow fails through the shared sampler. One draw per sample; seed replay is isolated.
  //
  // Runnable example: `apps/tags/examples/distribution/weibull.pad`. Independent fixture: `php tests/subsystems/expansion-distributions-weibull.php`.

return ['parameters'=>['shape'=>[1,.01,10000,FALSE],'scale'=>[1,1e-150,1e150,FALSE]],'support'=>'nonnegative real','work'=>fn($p)=>1,'sample'=>fn($p,$random)=>$p['scale']*(-log(1-$random()))**(1/$p['shape'])];
