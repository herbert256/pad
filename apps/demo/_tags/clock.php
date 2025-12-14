<?php

  // Clock tag - outputs formatted date/time
  // Usage: {clock format='H:i:s'} or {clock 'l, F j, Y'}

  $format = $padPrm[$pad]['format'] ?? $padOpt[$pad][1] ?? 'Y-m-d H:i:s';

  return date($format);

?>
