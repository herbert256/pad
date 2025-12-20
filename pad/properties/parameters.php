<?php

  global $padOpt;

  $padTagParmsResult = $padOpt [$padIdx];

  unset ( $padTagParmsResult[0] );

  $padTagParmsResult = padDataForcePad ($padTagParmsResult);

  return $padTagParmsResult;

?>
