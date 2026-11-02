<?php

  // torque - `padConvert(value, from, to)` converts this family to canonical `N*m` using static factors. Symbols and aliases are case sensitive. Units: `N*m` = 1 N*m, `kN*m` = 1000 N*m, `lbf*ft` = 1.3558179483314003 N*m, `lbf*in` = 0.1129848290276167 N*m. Torque remains dimensionally separate from energy; g0 means standard gravity, not local measured gravity. Nonfinite values and cross-family conversions fail.
  //
  // Runnable example: `apps/tags/examples/unit/torque.pad`. Independent fixture: `php tests/subsystems/expansion-units-torque.php`.

return ['canonical'=>'N*m','units'=>[
'N*m'=>['scale'=>1,'offset'=>0,'aliases'=>['N·m','newton-metre']],
'kN*m'=>['scale'=>1000,'offset'=>0,'aliases'=>['kN·m']],
'lbf*ft'=>['scale'=>1.3558179483314003,'offset'=>0,'aliases'=>['lb-ft']],
'lbf*in'=>['scale'=>0.1129848290276167,'offset'=>0,'aliases'=>['lb-in']],
]];
