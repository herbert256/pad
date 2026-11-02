<?php

  // acceleration - `padConvert(value, from, to)` converts this family to canonical `m/s2` using static factors. Symbols and aliases are case sensitive. Units: `m/s2` = 1 m/s2, `cm/s2` = 0.01 m/s2, `ft/s2` = 0.3048 m/s2, `g0` = 9.80665 m/s2. Torque remains dimensionally separate from energy; g0 means standard gravity, not local measured gravity. Nonfinite values and cross-family conversions fail.
  //
  // Runnable example: `apps/tags/examples/unit/acceleration.pad`. Independent fixture: `php tests/subsystems/expansion-units-acceleration.php`.

return ['canonical'=>'m/s2','units'=>[
'm/s2'=>['scale'=>1,'offset'=>0,'aliases'=>['m/s²']],
'cm/s2'=>['scale'=>0.01,'offset'=>0,'aliases'=>['Gal']],
'ft/s2'=>['scale'=>0.3048,'offset'=>0,'aliases'=>['ft/s²']],
'g0'=>['scale'=>9.80665,'offset'=>0,'aliases'=>['standard-gravity']],
]];
