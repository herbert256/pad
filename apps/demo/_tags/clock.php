<?php

  $format = $padPrm[$pad]['format'] ?? $padOpt[$pad][1] ?? 'Y-m-d H:i:s';

  return date($format);

?>