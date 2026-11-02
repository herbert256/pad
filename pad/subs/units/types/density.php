<?php

  // density - `padConvert(value, from, to)` converts this family to canonical `kg/m3` using static factors. Symbols and aliases are case sensitive. Units: `kg/m3` = 1 kg/m3, `g/cm3` = 1000 kg/m3, `g/L` = 1 kg/m3, `kg/L` = 1000 kg/m3, `lb/ft3` = 16.01846337396014 kg/m3. Torque remains dimensionally separate from energy; g0 means standard gravity, not local measured gravity. Nonfinite values and cross-family conversions fail.
  //
  // Runnable example: `apps/tags/examples/unit/density.pad`. Independent fixture: `php tests/subsystems/expansion-units-density.php`.

return ['canonical'=>'kg/m3','units'=>[
'kg/m3'=>['scale'=>1,'offset'=>0,'aliases'=>['kg/m³']],
'g/cm3'=>['scale'=>1000,'offset'=>0,'aliases'=>['g/cm³']],
'g/L'=>['scale'=>1,'offset'=>0,'aliases'=>[]],
'kg/L'=>['scale'=>1000,'offset'=>0,'aliases'=>[]],
'lb/ft3'=>['scale'=>16.01846337396014,'offset'=>0,'aliases'=>['lb/ft³']],
]];
