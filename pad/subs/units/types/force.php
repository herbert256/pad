<?php

  // force - `padConvert(value, from, to)` converts this family to canonical `N` using static factors. Symbols and aliases are case sensitive. Units: `N` = 1 N, `kN` = 1000 N, `dyn` = 1e-05 N, `lbf` = 4.4482216152605 N, `kgf` = 9.80665 N. Torque remains dimensionally separate from energy; g0 means standard gravity, not local measured gravity. Nonfinite values and cross-family conversions fail.
  //
  // Runnable example: `apps/tags/examples/unit/force.pad`. Independent fixture: `php tests/subsystems/expansion-units-force.php`.

return ['canonical'=>'N','units'=>[
'N'=>['scale'=>1,'offset'=>0,'aliases'=>['newton']],
'kN'=>['scale'=>1000,'offset'=>0,'aliases'=>[]],
'dyn'=>['scale'=>1e-05,'offset'=>0,'aliases'=>['dyne']],
'lbf'=>['scale'=>4.4482216152605,'offset'=>0,'aliases'=>['pound-force']],
'kgf'=>['scale'=>9.80665,'offset'=>0,'aliases'=>['kilogram-force']],
]];
