<?php

  // duration - First parameter: signed elapsed seconds, finite and absolute maximum 1e12. `rounding=nearest|floor|ceil|truncate` rounds to whole seconds; nearest uses halves away from zero. `units=1..4` limits day/hour/minute/second components. A day is 86400 seconds. Nonnegative values use HTML time duration; negative values use span because HTML time has no signed duration syntax.
  //
  // ## Example
  //
  // ```pad
  // {time:duration -3661, units=3/}
  // ```
  //
  // Run the focused fixture with `php tests/subsystems/time-duration.php`.

include_once PAD.'subs/times/_lib/duration.php';
$padDurationUnits=padTagParm('units',4);
try {if(!is_scalar($padDurationUnits)||!ctype_digit((string)$padDurationUnits))throw new InvalidArgumentException('duration units must be 1..4');return padTimeDuration($padParm,(int)$padDurationUnits,padTagParm('rounding','nearest'));}
catch(InvalidArgumentException $e){if($padCheckSyntax)padError($e->getMessage());return FALSE;}
