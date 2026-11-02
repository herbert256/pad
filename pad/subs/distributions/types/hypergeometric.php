<?php

  // hypergeometric - Sequential sampling without replacement. Integer population, successes and draws are each 0..100000, with successes and draws no greater than population. Returns the number of successes. Empty population permits zero successes and draws. One uniform per draw, subject to the batch work cap. Exact small-case hypergeometric mass is covered by the fixture.
  //
  // Runnable example: `apps/tags/examples/distribution/hypergeometric.pad`. Independent fixture: `php tests/subsystems/expansion-distributions-hypergeometric.php`.

return ['parameters'=>['population'=>[10,0,100000,TRUE],'successes'=>[5,0,100000,TRUE],'draws'=>[3,0,100000,TRUE]],'valid'=>fn($p)=>$p['successes']<=$p['population']&&$p['draws']<=$p['population'],'support'=>'successes in draws without replacement','work'=>fn($p)=>max(1,$p['draws']),
'sample'=>function($p,$random){$left=$p['population'];$success=$p['successes'];$found=0;for($i=0;$i<$p['draws'];$i++){if($random()<$success/$left){$found++;$success--;}$left--;}return $found;}];
