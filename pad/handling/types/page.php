<?php

  $padHandPage  = (int) ($padPrm [$pad] ['page'] ??  1);
  $padHandRows  = (int) ($padPrm [$pad] ['rows'] ?? 10);

  $padHandStart = ( ( $padHandPage - 1 ) * $padHandRows ) + 1;
  $padHandEnd   = (   $padHandStart      + $padHandRows ) - 1;

  padHandGo ($padData [$pad], $padHandStart, $padHandEnd);

?>