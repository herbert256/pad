<?php

  $padTagParmsResult = $GLOBALS ['padOpt'] [$padIdx];

  unset ( $padTagParmsResult[0] );
  
  $padTagParmsResult = padDataForcePad ($padTagParmsResult);

  return $padTagParmsResult;

?>