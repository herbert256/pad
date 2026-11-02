<?php
return ['parameters'=>['p'=>[.5,1e-12,1,FALSE]],'support'=>'failures before first success, starting at zero','work'=>fn($p)=>1,'sample'=>function($p,$random){if($p['p']===1.0)return 0;$v=floor(log(1-$random())/log1p(-$p['p']));if($v>PHP_INT_MAX)throw new UnexpectedValueException('geometric integer overflow');return (int)$v;}];
