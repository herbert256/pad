<?php

  // geometric - Counts failures before the first success: P(X=k)=(1-p)^k p, k≥0. Probability p is 1e-12..1; p=1 deterministically returns zero. Exact inverse transform uses log1p and checks integer overflow. No trial-count ambiguity; add one explicitly for trials.
  //
  // Runnable example: `apps/tags/examples/distribution/geometric.pad`. Independent fixture: `php tests/subsystems/expansion-distributions-geometric.php`.

return ['parameters'=>['p'=>[.5,1e-12,1,FALSE]],'support'=>'failures before first success, starting at zero','work'=>fn($p)=>1,'sample'=>function($p,$random){if($p['p']===1.0)return 0;$v=floor(log(1-$random())/log1p(-$p['p']));if($v>PHP_INT_MAX)throw new UnexpectedValueException('geometric integer overflow');return (int)$v;}];
