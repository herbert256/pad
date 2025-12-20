<?php

  padCallbackBeforeXxx ('init');

  foreach ( $padData [$pad] as $padK => $padV)
    padCallbackBeforeRow ( $padData [$pad] [$padK] );

  padCallbackBeforeXxx ('exit');

?>
